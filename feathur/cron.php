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

// Create template link for KVM.
$sLink = $sLocalSSH->exec("ln -s /var/feathur/data/templates/kvm /var/feathur/feathur/templates/");

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
				$sCommandList .= "echo \"{$sServer->sName} Starting...\n\";rsync -avz -e \"ssh -o StrictHostKeyChecking=no -i /var/feathur/data/keys/{$sServer->sKey}\" /var/feathur/data/templates/openvz/* root@{$sServer->sIPAddress}:/vz/template/cache/;";
			}
			echo "Issuing commands to sync OpenVZ templates.\n";
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

// System status tracker.
if($sServerList = $database->CachedQuery("SELECT * FROM servers", array())){
	foreach($sServerList->data as $sServer){
		if($sTotal == 10) {
			$sLocalSSH->exec($sCommandList);
			unset($sCommandList);
			unset($sTotal);
			echo "Launched a batch of uptime checkers.\n";
			sleep(5);
		}
		$sServer = new Server($sServer["id"]);
		$sCommandList .= "screen -dm -S uptracker bash -c 'cd /var/feathur/feathur/scripts/;php pull_server.php {$sServer->sId} >> /var/feathur/data/status.log;exit;';";
		$sTotal++;
		
		$sBefore = (time() - (5 * 60));
		$sUptime = $sServer->sLastCheck;
		$sStatus = $sServer->sStatus;
		if(($sBefore > $sUptime) && ($sStatus === true)){
			$sStatusWarning = $sServer->sStatusWarning;
			if($sStatusWarning === false){
				if($sAdminList = $database->CachedQuery("SELECT * FROM `accounts` WHERE `permissions` = :Permissions", array("Permissions" => 7))){
					foreach($sAdminList->data as $sAdmin){
						$sVariable = array("server" => $sServer->sName);
						$sAlert = Core::SendEmail($sAdmin["email_address"], "Server Down: {$sServer->sName} - To {$sAdmin['email_address']}", "down", $sVariable);
					}
				}
				$sServer->uStatusWarning = true;
				echo "Sent email about outage to administrators.\n";
			}
			$sServer->uStatus = false;
			$sServer->InsertIntoDatabase();
		}
	}
	
	$sLocalSSH->exec($sCommandList);
	unset($sCommandList);
	unset($sTotal);
	echo "Finished launching uptime checkers.\n";
}

// Reset bandwidth if today is the first day of the month and the last reset was more than 10 days ago.
$sLastReset = Core::GetSetting('bandwidth_timestamp');
$sTimeAgo = (time() - (((60 * 60) * 24) * 10));
$sDayToday = date('j');
if(($sLastReset->sValue < $sTimeAgo) && ($sDayToday == 1)){
	$sReset = $database->prepare("UPDATE `vps` SET `bandwidth_usage` = '0'");
	$sReset->execute();
	$sUpdateReset = Core::UpdateSetting('bandwidth_timestamp', time());
}

// License
// Please don't remove or edit this code, a lot of work went into Feathur.
// Thank us for our work by leaving this code here or by paying for a license.
// While I realize this won't stop anyone who really wants to disable the "alert" system, it might prevent someone who knows nothing about PHP.
echo "License update...";
if($sSlaves = $database->CachedQuery("SELECT * FROM servers", array())){
	$sCountSlaves = count($sSlaves->data);
}
$sHost = Core::GetSetting('panel_url');
$sURL = "http://check.feathur.com/api.php?host={$sHost->sValue}&slaves={$sCountSlaves}";
$sCurl = curl_init();
curl_setopt($sCurl, CURLOPT_URL, $sURL);
curl_setopt($sCurl, CURLOPT_RETURNTRANSFER, 1);
$sLicense = json_decode(curl_exec($sCurl), true);
curl_close($sCurl);
if($sLicense["type"] == 'success'){
	$sUpdateLicense = Core::UpdateSetting('license', "1");
} else {
	$sUpdateLicense = Core::UpdateSetting('license', "0");
}


// Check for updates.
echo "Checking for updates if available...";
$sAutomaticUpdates = Core::GetSetting('automatic_updates');
$sAutomaticUpdates = $sAutomaticUpdates->sValue;
$sLastUpdateCheck = Core::GetSetting('last_update_check');
$sLastUpdateCheck = $sLastUpdateCheck->sValue;
$sTimeAgo = time - (15 * 60);
if($sLastUpdateCheck < $sTimeAgo){
	if($sAutomaticUpdates == 1){
		$sSSH = new Net_SSH2('127.0.0.1');
		$sKey = new Crypt_RSA();
		$sKey->loadKey(file_get_contents($cphp_config->settings->rootkey));
		if($sSSH->login("root", $sKey)) {
			$sSSH->exec("cd /var/feathur/; git pull; cd /var/feathur/feathur/; php update.php; rm -rf update.php;");
			$sVersion = $sSSH->exec("cat /var/feathur/version.txt");
			$sLastUpdate = Core::UpdateSetting('last_update_check', time());
			$sNewVersion = Core::UpdateSetting('current_version', $sVersion);
		}
	}
}

// Release cron locks.
$sLock = $sLocalSSH->exec("rm -rf /var/feathur/data/bandwidth.lock");

