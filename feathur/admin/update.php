<?php
if($sUser->sPermissions != 7){
	die("Sorry you've accessed our system without permission");
}

$sPage = "update";
$sPageType = "settings";
$sType = $_GET['type'];

$sAutomaticUpdates = Core::GetSetting('automatic_updates');
$sCurrentVersion = Core::GetSetting('current_version');
$sUpdateType = Core::GetSetting('update_type');
$sURL = "https://raw.github.com/BlueVM/Feathur/{$sUpdateType->sValue}/version.txt";
$sCurl = curl_init();
curl_setopt($sCurl, CURLOPT_URL, $sURL);
curl_setopt($sCurl, CURLOPT_RETURNTRANSFER, 1);
$sVersion = curl_exec($sCurl);
curl_close($sCurl);
if($sCurrentVersion->sValue != $sVersion){
	$sOutOfDate = 1;
}

if($sAction == automatic){
	if($_GET['value'] == 1){
		$sAutomatic = Core::UpdateSetting('automatic_updates', "1");
		$sErrors[] = array("green" => "Automatic updates have been turned on!");
	} elseif($_GET['value'] == 0){
		$sAutomatic = Core::UpdateSetting('automatic_updates', "0");
		$sErrors[] = array("green" => "Automatic updates have been turned off!");
	}
}

if($sAction == force){
	$sSSH = new Net_SSH2('127.0.0.1');
	$sKey = new Crypt_RSA();
	$sKey->loadKey(file_get_contents($cphp_config->settings->rootkey));
	if($sSSH->login("root", $sKey)) {
		$sSSH->exec("cd /var/feathur/; git pull; cd /var/feathur/feathur/; php update.php; rm -rf update.php;");
		$sLastUpdate = Core::UpdateSetting('last_update_check', time());
		$sUpdateVersion = Core::UpdateSetting('current_version', $sVersion);
		$sErrors[] = array("green" => "Force update completed!");
	}
	$sJson = 1;
}

$sAutomaticUpdates = Core::GetSetting('automatic_updates');
$sCurrentVersion = Core::GetSetting('current_version');
$sUpdateType = Core::GetSetting('update_type');
$sURL = "https://raw.github.com/BlueVM/Feathur/{$sUpdateType->sValue}/version.txt";
$sCurl = curl_init();
curl_setopt($sCurl, CURLOPT_URL, $sURL);
curl_setopt($sCurl, CURLOPT_RETURNTRANSFER, 1);
$sVersion = json_decode(curl_exec($sCurl), true);
curl_close($sCurl);
if($sCurrentVersion->sValue != $sVersion){
	$sOutOfDate = 1;
}

if(empty($sVersion)){
	$sVersion = "";
}

$sContent .= Templater::AdvancedParse($sAdminTemplate->sValue.'/update', $locale->strings, array("AutomaticUpdates" => $sAutomaticUpdates->sValue,
																								"CurrentVersion" => $sVersion,
																								"YourVersion" => $sCurrentVersion->sValue,
																								"OutOfDate" => $sOutOfDate,
																								"Errors" => $sErrors));
																								
if($sJson == 1){
	echo json_encode(array("content" => $sContent));
	die();
}

unset($sErrors);