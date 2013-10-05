<?php
if($sUser->sPermissions != 7){
	die("Sorry you've accessed our system without permission");
}

$sPage = "update";
$sPageType = "settings";
$sType = $_GET['type'];

$sAutomaticUpdates = Core::GetSetting('automatic_updates');
$sCurrentVersion = Core::GetSetting('current_version');
$sURL = "http://repo.feathur.com/version.php";
$sCurl = curl_init();
curl_setopt($sCurl, CURLOPT_URL, $sURL);
curl_setopt($sCurl, CURLOPT_RETURNTRANSFER, 1);
$sVersion = json_decode(curl_exec($sCurl), true);
curl_close($sCurl);
if($sCurrentVersion->sValue != $sVersion["version"]){
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
		$sSSH->exec("wget http://repo.feathur.com/update.sh;bash update.sh;rm -rf update.sh");
		$sLastUpdate = Core::UpdateSetting('last_update_check', time());
		$sUpdateVersion = Core::UpdateSetting('current_version', $sVersion["version"]);
		$sErrors[] = array("green" => "Force update completed!");
	}
	$sJson = 1;
}

$sAutomaticUpdates = Core::GetSetting('automatic_updates');
$sCurrentVersion = Core::GetSetting('current_version');
$sURL = "http://repo.feathur.com/version.php";
$sCurl = curl_init();
curl_setopt($sCurl, CURLOPT_URL, $sURL);
curl_setopt($sCurl, CURLOPT_RETURNTRANSFER, 1);
$sVersion = json_decode(curl_exec($sCurl), true);
curl_close($sCurl);
if($sCurrentVersion->sValue != $sVersion["version"]){
	$sOutOfDate = 1;
}

$sContent .= Templater::AdvancedParse($sAdminTemplate->sValue.'/update', $locale->strings, array("AutomaticUpdates" => $sAutomaticUpdates->sValue,
																								"CurrentVersion" => $sVersion["version"],
																								"YourVersion" => $sCurrentVersion->sValue,
																								"OutOfDate" => $sOutOfDate,
																								"Errors" => $sErrors));
																								
if($sJson == 1){
	echo json_encode(array("content" => $sContent));
	die();
}

unset($sErrors);