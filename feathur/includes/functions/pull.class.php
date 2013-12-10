<?php
class Pull {

	public static function pull_status($sServer){
		
		echo "Setting up prerequisites...\n";
		$sTimestamp = time();
		global $database;
		// Insert History
		$sHistory = new History(0);
		$sHistory->uServerId = $sServer;
		$sHistory->uTimestamp = $sTimestamp;
		$sHistory->uStatus = false;
		$sHistory->InsertIntoDatabase();
		
		// Insert Statistics
		$sStatistics = new Statistics(0);
		$sStatistics->uServerId = $sServer;
		$sStatistics->uStatus = false;
		$sStatistics->uTimestamp = $sTimestamp;
		$sStatistics->InsertIntoDatabase();
		
		// Connect to Server
		$sServer = new Server($sServer);
		$sSSH = Server::server_connect($sServer, "1");
		if(is_array($sSSH)){
			die();
		}
		
		// Pull system stats.
		echo "Connected to server...\n";
		$sUptime = explode(' ', $sSSH->exec("cat /proc/uptime"));
		$sCPU = explode(' ', $sSSH->exec("cat /proc/loadavg"));	
		$sUsedRAM = preg_replace('/[^0-9]/', '', $sSSH->exec("free | head -n 3 | tail -n 1 | awk '{print $3}'"));
		$sTotalRAM = preg_replace('/[^0-9]/', '', $sSSH->exec("free | head -n 2 | tail -n 1 | awk '{print $2}'"));

		$sDisk = $sSSH->exec("df");
		$sDisk = explode("\n", trim($sDisk));
		array_shift($sDisk);
		foreach($sDisk as $sValue){
			$sValue = explode(" ", preg_replace("/\s+/", " ", $sValue));
			if(is_numeric($sValue[2])){
				$sDiskUsed = $sDiskUsed + $sValue[2];
				$sDiskTotal = $sDiskTotal + $sValue[1];
			}
		}
		$sDiskUsed = $sDiskUsed / 1048576;
		$sDiskTotal = $sDiskTotal / 1048576;
		
		$sPullBandwidth = explode("\n", $sSSH->exec("ifconfig $interface | grep 'RX bytes' | awk -F: '{print $2,$3}' | awk '{print $1,$6}'"));
		foreach($sPullBandwidth as $sData){
			if(empty($sNewBandwidth)){
				$sData = explode(" ", $sData);
				$sData[0] = preg_replace('/[^0-9]/', '', $sData[0]);
				$sData[1] = preg_replace('/[^0-9]/', '', $sData[1]);
				$sNewBandwidth = round(($sData[0] / 131072) + ($sData[1] / 131072), 2);
			}
		}
		
		// Update server row.
		// Check to make sure that the current bandwidth is higher than last bandwidth.
		// If higher, update statuses, otherwise replace both values with current value.
		// This prevents bandwidth accounting from becoming negative.
		$sBandwidthNegative = $sServer->sBandwidth;
		if($sNewBandwidth > $sBandwidthNegative){
			$sServer->uPreviousCheck = $sServer->sLastCheck;
			$sServer->uLastCheck = $sTimestamp;
			$sServer->uLastBandwidth = $sServer->sBandwidth;
			$sServer->uBandwidth = $sNewBandwidth;
		} else {
			$sServer->uPreviousCheck = $sTimestamp;
			$sServer->uLastCheck = $sTimestamp;
			$sServer->uLastBandwidth = $sNewBandwidth;
			$sServer->uBandwidth = $sNewBandwidth;
		}
		$sServer->uLoadAverage = $sCPU[0];
		$sServer->uHardDiskTotal = $sDiskTotal;
		$sServer->uHardDiskFree = ($sDiskTotal - $sDiskUsed);
		$sServer->uTotalMemory = $sTotalRAM;
		$sServer->uFreeMemory = ($sTotalRAM - $sUsedRAM);
		$sServer->uStatus = true;
		$sServer->uStatusWarning = false;
		$sServer->uHardwareUptime = $sUptime[0];
		$sServer->InsertIntoDatabase();
		
		// Update history
		$sHistory->uStatus = true;
		$sHistory->InsertIntoDatabase();
		
		// Update statistics
		$sStatistics->uStatus = true;
		$sStatistics->uHardwareUptime = $sUptime[0];
		$sStatistics->uTotalMemory = $sTotalRAM;
		$sStatistics->uFreeMemory = ($sTotalRAM - $sUsedRAM);
		$sStatistics->uLoadAverage = $sCPU[0];
		$sStatistics->uHardDiskTotal = $sDiskTotal;
		$sStatistics->uHardDiskFree = ($sDiskTotal - $sDiskUsed);
		$sStatistics->uBandwidth = $sNewBandwidth;
		$sStatistics->InsertIntoDatabase();
		
		// Cleanup
		unset($sPullBandwidth);
		echo "Server polling completed...\n";
		
		// Bandwidth polling for each VPS on this server.
		$sBandwidthAccounting = Core::GetSetting('bandwidth_accounting');
		echo "Beginning bandwidth accounting\n";
		
		// OpenVZ processing instructions.
		if($sServer->sType == 'openvz'){
			if($sListVPS = $database->CachedQuery("SELECT * FROM `vps` WHERE `server_id` = :ServerId", array("ServerId" => $sServer->sId))){
				foreach($sListVPS->data as $sVPS){
					$sVPS = new VPS($sVPS["id"]);
					$sPullBandwidth = explode("\n", $sSSH->exec("vzctl exec {$sVPS->sContainerId} ifconfig $interface | grep 'RX bytes' | awk -F: '{print $2,$3}' | awk '{print $1,$6}';"));
					foreach($sPullBandwidth as $sData){
						$sData = explode(" ", $sData);
						$sData[0] = round(((preg_replace('/[^0-9]/', '', $sData[0]) / 1024) / 1024), 2);
						$sData[1] = round(((preg_replace('/[^0-9]/', '', $sData[1]) / 1024) / 1024), 2);
						
						if($sBandwidthAccounting->sValue == 'upload'){
							$sTotal = $sTotal + $sData[1];
						}
						
						if($sBandwidthAccounting->sValue == 'download'){
							$sTotal = $sTotal + $sData[2];
						}
						
						if($sBandwidthAccounting->sValue == 'both'){
							$sTotal = $sTotal + $sData[0] + $sData[1];
						}
					}
					
					if($sVPS->sLastBandwidth < $sTotal){
							$sChange = round(($sTotal - $sVPS->sLastBandwidth), 2);
					} else {
						if(!empty($sVPS->sBandwidthUsage)){
							$sChange = round($sTotal, 2);
						}
					}
					
					echo "Bandwidth for: {$sVPS->sId} - Total: {$sTotal} - Change: +{$sChange}\n";
					
					$sVPS->uBandwidthUsage = $sVPS->sBandwidthUsage + $sChange;
					$sVPS->uLastBandwidth = $sTotal;
					$sVPS->InsertIntoDatabase();
					
					
					unset($sData);
					unset($sTotal);
					unset($sChange);
				}
				unset($sPullBandwidth);
			}
		}
		
		// KVM Processing instructions
		if($sServer->sType == 'kvm'){
			$sPullBandwidth = explode("\n", $sSSH->exec('for i in `ip link show | grep mtu | awk \'{print $2}\' | awk -F: \'{print $1}\'`; do  vpsid=$(echo $i | awk -F. \'{print $1}\' | awk -Fm \'{print $2}\'); vpsbw=`ifconfig $i | grep \'RX bytes\' | awk -F: \'{print $2,$3}\' | awk \'{print $1,$6}\';`; echo "$vpsid $vpsbw"; done'));
			var_dump($sPullBandwidth);
			foreach($sPullBandwidth as $sRow){
				$sCheckValid = str_split($sRow);
				if(ctype_digit($sCheckValid[0])){
					$sData = explode(" ", $sRow);
					$sVPSId = preg_replace('/[^0-9]/', '', $sData[0]);
					try {
						if(!empty($sVPSId)){
							if($sListVPS = $database->CachedQuery("SELECT * FROM `vps` WHERE `container_id` = :ContainerId AND `type` = :Type AND `server_id` = :ServerId", array("ContainerId" => $sVPSId, "Type" => "kvm", "ServerId" => $sServer->sId))){
								$sVPS = new VPS($sListVPS->data[0]["id"]);
							} else {
								echo "Skipping... no VPS found for this ID.\n";
							}
						} else {
							echo "Skipping invalid VPS - {$sVPSId} - (1)\n";
							continue;
						}
					} catch (Exception $e) {
						echo "Skipping invalid VPS - {$sVPSId} - (2) \n";
						continue;
					}
					
					$sData[1] = round(((preg_replace('/[^0-9]/', '', $sData[1]) / 1024) / 1024), 2);
					$sData[2] = round(((preg_replace('/[^0-9]/', '', $sData[2]) / 1024) / 1024), 2);
					
					if($sBandwidthAccounting->sValue == 'upload'){
						$sTotal = $sTotal + $sData[2];
					}
						
					if($sBandwidthAccounting->sValue == 'download'){
						$sTotal = $sTotal + $sData[1];
					}
						
					if($sBandwidthAccounting->sValue == 'both'){
						$sTotal = $sTotal + $sData[2] + $sData[1];
					}
					
					if($sVPS->sLastBandwidth < $sTotal){
							$sChange = round(($sTotal - $sVPS->sLastBandwidth), 2);
					} else {
						if(!empty($sVPS->sBandwidthUsage)){
							$sChange = round($sTotal, 2);
						}
					}
					
					echo "Bandwidth for: {$sVPS->sId} - Total: {$sTotal} - Change: +{$sChange}\n";
					
					$sVPS->uBandwidthUsage = $sVPS->sBandwidthUsage + $sChange;
					$sVPS->uLastBandwidth = $sTotal;
					$sVPS->InsertIntoDatabase();
					
					unset($sData);
					unset($sTotal);
					unset($sChange);
				} else {
					echo "Skipping row... invalid.\n";
				}
			}
			unset($sPullBandwidth);
		}
		
		echo "Completed.\n";
		return true;
	}
}