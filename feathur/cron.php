<?php
// Check to make sure that this script isn't being executed remotely.
if(!(php_sapi_name() == 'cli')){
	die("Unfortunately this script must be executed via CLI.");
}

chdir('/var/feathur/feathur/');
include('./includes/loader.php');
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
$sTime = time();

$sLocalSSH = new Net_SSH2('127.0.0.1');
$sLocalKey = new Crypt_RSA();
$sLocalKey->loadKey(file_get_contents($cphp_config->settings->rootkey));
if(!($sLocalSSH->login("root", $sLocalKey))) {
	die("Cannot connect to this server, check local key.");
}

// Check for lock file.
$sLock = $sLocalSSH->exec("cat /var/feathur/data/cron.lock");
if(strpos($sLock, 'No such file or directory') === false) {
	die("Another cron is currently running.");
}

$sCommandList .= "echo \"{$sTime}\" > /var/feathur/data/cron.lock;";

// Last Template Sync Check
$sTemplateSync = Core::GetSetting('last_template_sync');
$sBefore = time() - (60 * 5);
if($sTemplateSync < $sBefore){

	if($sOpenVZ = $database->CachedQuery("SELECT * FROM servers WHERE `type` = 'openvz'", array())){
		foreach($sOpenVZ->data as $sValue){
			$sServer = new Server($sValue["id"]);
			$sCommandList .= "rsync -avz -e \"ssh -o StrictHostKeyChecking=no -i /var/feathur/data/keys/{$sServer->sKey}\" /var/feathur/data/templates/openvz/* root@{$sServer->sIPAddress}:/vz/template/cache/;";
		}
	}
	
	if($sKVM = $database->CachedQuery("SELECT * FROM servers WHERE `type` = 'kvm'", array())){
		foreach($sKVM->data as $sValue){
			$sServer = new Server($sValue["id"]);
			$sCommandList .= "rsync -avz -e \"ssh -o StrictHostKeyChecking=no -i /var/feathur/data/keys/{$sServer->sKey}\" /var/feathur/data/templates/kvm/* root@{$sServer->sIPAddress}:/var/feathur/data/templates/kvm/;";
		}
	}
}

$sCommandList .= "rm -rf /var/feathur/data/cron.lock";
$sCommandList = escapeshellarg($sCommandList);
$sLocalSSH->exec("screen -dm -S cron bash -c {$sCommandList};");



