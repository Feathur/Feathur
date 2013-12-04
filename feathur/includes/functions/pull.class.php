<?php
class Pull {

	public static function pull_status($sServer){
		
		$sTimestamp = time();
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
		$sSSH = Server::server_connect($sServer);
		$sUptime = explode(' ', $sSSH->exec("cat /proc/uptime"));
		$sCPU = explode(' ', $sSSH->exec("cat /proc/loadavg"));	
		$sRAM = explode('kB', $sSSH->exec("cat /proc/meminfo"));
		$sUsedRAM = ((((preg_replace("/[^0-9]/", "", $sRAM[0])) - preg_replace("/[^0-9]/", "", $sRAM[1])) - preg_replace("/[^0-9]/", "", $sRAM[2])) - preg_replace("/[^0-9]/", "", $sRAM[2]));
		$sTotalRAM = preg_replace("/[^0-9]/", "", $sRAM[0]);

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
		foreach($sPullBandwidth as $value){
			$value = explode(' ', $value);
			foreach($value as $number){
				$sBandwidth = $sBandwidth + $number;
			}
		}
		
		// Update server row
		$sServer->uLoadAverage = $sCPU[0];
		$sServer->uHardDiskTotal = $sDiskTotal;
		$sServer->uHardDiskFree = ($sDiskTotal - $sDiskUsed);
		$sServer->uTotalMemory = $sTotalRAM;
		$sServer->uFreeMemory = ($sTotalRAM - $sUsedRAM);
		$sServer->uBandwidth = $sBandwidth;
		$sServer->uStatus = true;
		$sServer->uStatusWarning = false;
		$sServer->uHardwareUptime = $sUptime[0];
		$sServer->uLastCheck = $sTimestamp;
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
		$sStatistics->uHardDiskUsed = ($sDiskTotal - $sDiskUsed);
		$sStatistics->uBandwidth = $sBandwidth;
		$sStatistics->InsertIntoDatabase();
		
		return true;
	}
}