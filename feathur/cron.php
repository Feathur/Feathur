<?php
// Check to make sure that this script isn't being executed remotely.
if(!(php_sapi_name() == 'cli')){
	die("Unfortunately this script must be executed via CLI.");
}

chdir('/var/feathur/feathur/');
include('./includes/loader.php');
error_reporting(E_ALL ^ E_NOTICE);
$sTime = time();

set_time_limit(6000);
ini_set('memory_limit','512M');
$sLocalSSH = new Net_SSH2('127.0.0.1');
$sLocalKey = new Crypt_RSA();
$sLocalKey->loadKey(file_get_contents($cphp_config->settings->rootkey));
if(!($sLocalSSH->login("root", $sLocalKey))) {
	die("Cannot connect to this server, check local key.");
}

// Setup screen to begin syncing templates assuming that:
// 1. A sync hasn't occurred in the last 5 minutes.
// 2. A sync isn't still in progress (EG: template.lock)
$sTemplateSync = Core::GetSetting('last_template_sync');
$sBefore = time() - (60 * 5);
$sTemplateSync = $sTemplateSync->sValue;
if($sTemplateSync < $sBefore){

	$sLock = $sLocalSSH->exec("cat /var/feathur/data/template.lock;");
	if(strpos($sLock, 'No such file or directory') !== false) {

		// Issue template lock.
		$sLock = $sLocalSSH->exec("cd /var/feathur/data;touch template.lock;");
		echo "Starting template sync...\n";
		
		if($sOpenVZ = $database->CachedQuery("SELECT * FROM servers WHERE `type` = 'openvz'", array())){
			foreach($sOpenVZ->data as $sValue){
				$sServer = new Server($sValue["id"]);
				$sCommandList .= "rsync -avz -e \"ssh -o StrictHostKeyChecking=no -i /var/feathur/data/keys/{$sServer->sKey}\" /var/feathur/data/templates/openvz/* root@{$sServer->sIPAddress}:/vz/template/cache/;";
			}
			echo "Issuing commands to sync OpenVZ templates.\n";
		}
		
		if($sKVM = $database->CachedQuery("SELECT * FROM servers WHERE `type` = 'kvm'", array())){
			foreach($sKVM->data as $sValue){
				$sServer = new Server($sValue["id"]);
				$sCommandList .= "rsync -avz -e \"ssh -o StrictHostKeyChecking=no -i /var/feathur/data/keys/{$sServer->sKey}\" /var/feathur/data/templates/kvm/* root@{$sServer->sIPAddress}:/var/feathur/data/templates/kvm/;";
			}
			echo "Issuing commands to sync KVM templates.\n";
		}
		
		$sCommandList .= "rm -rf /var/feathur/data/template.lock;";
		$sCommandList = escapeshellarg($sCommandList);
		$sLocalSSH->exec("screen -dm -S cron bash -c {$sCommandList};");
		$sLastUpdate = Core::UpdateSetting('last_template_sync', time());
		unset($sCommandList);
		echo "Issued commands to sync templates.\n";
	}
} else {
	echo "Another template sync is already in progress, skipping template sync.\n";
}

// Begin pulling bandwidth assuming that:
// 2. A pull isn't still in progress (EG: template.lock)
$sLock = $sLocalSSH->exec("cat /var/feathur/data/bandwidth.lock");
if(strpos($sLock, 'No such file or directory') !== false) {

	// Issue bandwidth lock.
	echo "Starting bandwidth calculations...\n";
	$sLock = $sLocalSSH->exec("echo \"{$sTime}\" > /var/feathur/data/bandwidth.lock;");
	$sBandwidthAccounting = Core::GetSetting('bandwidth_accounting');
	$sBefore = (time() - (60 * 5));
	if($sOpenVZ = $database->CachedQuery("SELECT * FROM servers WHERE `type` = 'openvz' && `bandwidth_timestamp` < :Before LIMIT 20", array("Before" => $sBefore))){
		foreach($sOpenVZ->data as $sValue){
			$sServer = new Server($sValue["id"]);
			$sServerConnect = $sServer->server_connect($sServer);
			
			$sSetup = $sServerConnect->exec("cat /var/feathur/data/feathur-bandwidth.txt");
			if(strpos($sSetup, 'No such file or directory') !== false) {
				echo "Installing bandwidth monitor on server {$sServer->sName}\n";
				$sCommandList = escapeshellarg("rm -rf *bandwidth.sh*;wget http://feathur.com/scripts/openvz-bandwidth.sh;bash openvz-bandwidth.sh;");
				$sExecute = $sServerConnect->exec("screen -dm -S cron bash -c {$sCommandList};");
			} else {
			
				echo "Running bandwidth query on server {$sServer->sName}\n";
				$sExecute = $sServerConnect->exec("cd /var/feathur/data/;bash bandwidth.sh;");
				$sBandwidth = $sServerConnect->exec("cat /var/feathur/data/bandwidth.txt;");
				
				$sBandwidth = explode("\n", $sBandwidth);
				foreach($sBandwidth as $sIP){
					$sIP = explode(" ", $sIP);
					if((isset($sIP[1])) && (isset($sIP[2]))){
						$sIP[1] = round((($sIP[1] / 1024) / 1024), 4);
						$sIP[2] = round((($sIP[1] / 1024) / 1024), 4);
						if($sBandwidthAccounting == 'both'){
							$sBandwidthUsed = $sIP[1] + $sIP[2];
						} elseif($sBandwidthAccounting == 'up'){
							$sBandwidthUsed = $sIP[1];
						} elseif($sBandwidthAccounting == 'down'){
							$sBandwidthUsed = $sIP[2];
						} else {
							$sBandwidthUsed = $sIP[1] + $sIP[2];
						}
						
						$sTotalBandwidth = $sTotalBandwidth + $sIP[1] + $sIP[2];
					} else {
						echo "Skipping blank line...\n";
					}
					
					if($sIPData = $database->CachedQuery("SELECT * FROM ipaddresses WHERE `ip_address` = :IP", array("IP" => $sIP[0]))){
						if(!empty($sIPData->data[0]["vps_id"])){
							$sVPS = new VPS($sIPData->data[0]["vps_id"]);
							$sPreUpdate = $sVPS->sBandwidthUsage;
							$sVPS->uBandwidthUsage = $sBandwidthUsed + $sPreUpdate;
							$sVPS->InsertIntoDatabase();
							echo "{$sVPS->sId} ({$sIP[0]}) => Old: {$sPreUpdate} | New: {$sBandwidthData[$sVPS->sId]} | Added: {$sBandwidthUsed}\n";
						} else {
							$sLog[] = array("result" => "For some reason the IP {$sIPData->data[0]["ip_address"]} is generating traffic, but it isn't assigned to a VPS. You might want to take a look into this.", "command" => "Automated bandwidth checker.");
							$sSaveLog = ServerLogs::save_server_logs($sLog, $sServer);
							unset($sLog);
						}
					}
					unset($sBandwidthUsed);
					$sTotalIPs++;
				}
				
				$sServerConnect->exec("pkill pmacctd;pmacctd -c src_host,dst_host -f /usr/local/etc/pmacctd.conf;");
				
				unset($sTotalBandwidth);
				unset($sTotalIPs);
				$sServer->uBandwidthTimestamp = time();
				$sServer->InsertIntoDatabase();
			}
			$sCurrent++;
		}
		echo "Finishing up OpenVZ bandwidth accounting...\n";
	} else {
		echo "Skipping OpenVZ bandwidth accounting, no servers currently available.\n";
	}

} else {
	echo "Another bandwidth cron is currently running, skipping bandwidth calculations.\n";
}

// Release cron locks.
$sLock = $sLocalSSH->exec("rm -rf /var/feathur/data/bandwidth.lock");

