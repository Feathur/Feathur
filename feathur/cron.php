<?php
chdir('/var/feathur/feathur/');
include('./includes/loader.php');
$sRandom = rand(1, 5);
echo "Waiting {$sRandom} seconds...";
sleep($sRandom);
$sTimestamp = time();
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);

function running($sTimestamp){
	return $sCurrent = time() - $sTimestamp;
}

function cleanup($sTimestamp){
	$sCurrent = time() - $sTimestamp;
	if($sCurrent > 60){
		die("Ended cron early due to time constraints.");
	}
}

$sCurrent = running($sTimestamp);
echo "Starting Pull: {$sCurrent} seconds";
cleanup($sTimestamp);

// Check system uptime
$sPull = new Pull;
$sData = $sPull->Start();

$sCurrent = running($sTimestamp);
echo "End of Pull: {$sCurrent} seconds";
cleanup($sTimestamp);

// Template sync.
$sLastTemplateSync = Core::GetSetting('last_template_sync');
$sTimeAgo = time() - (60 * 5);
if($sLastTemplateSync->sValue < $sTimeAgo){
	$sLastTemplateSync = Core::UpdateSetting('last_template_sync', time());
	if($sServers = $database->CachedQuery("SELECT * FROM servers", array())){
		foreach($sServers->data as $value){
			$sServer = new Server($value["id"]);
			$sTemplateType = "update_{$sServer->sType}_templates";
			$sLocalSSH = new Net_SSH2('127.0.0.1');
			$sLocalKey = new Crypt_RSA();
			$sLocalKey->loadKey(file_get_contents($cphp_config->settings->rootkey));
			if($sLocalSSH->login("root", $sLocalKey)) {
				$sTemplateUpdate = VPS::$sTemplateType($sServer, $sLocalSSH);
			}
		}
	}
}

$sCurrent = running($sTimestamp);
echo "Finishing Template Syncing: {$sCurrent} seconds";
cleanup($sTimestamp);