<?php
if($sUser->sPermissions != 7){
	die("Sorry you've accessed our system without permission.");
}

$sPage = "update";
$sPageType = "settings";
$sType = $_GET['type'];

function check_updates(){
	$sCurrentVersion = Core::GetSetting('current_version');
	$sCurrentVersionClean = preg_replace('/[.]/', '', $sCurrentVersion->sValue);
	$sURL = "https://raw.github.com/BlueVM/Feathur/develop/version.txt";
	$sCurl = curl_init();
	curl_setopt($sCurl, CURLOPT_URL, $sURL);
	curl_setopt($sCurl, CURLOPT_RETURNTRANSFER, 1);
	$sVersion = curl_exec($sCurl);
	$sVersionCompare = preg_replace('/[.]/', '', $sVersion);
	curl_close($sCurl);
	if(ctype_digit($sVersionCompare)){
		if($sVersionCompare > $sCurrentVersionClean){
			return array("your_version" => $sCurrentVersion->sValue, "current_version" => $sVersion, "update" => "0");
		} else {
			return array("your_version" => $sCurrentVersion->sValue, "current_version" => $sVersion, "update" => "1");
		}
	} else {
		return array("your_version" => $sCurrentVersion->sValue, "current_version" => "Github Down", "update" => "0");
	}
}

if($sAction == automatic){
	if($_GET['value'] == 1){
		$sAutomatic = Core::UpdateSetting('automatic_updates', "1");
		$sErrors[] = array("result" => "Automatic updates have been turned on.", "type" => "success");
	} elseif($_GET['value'] == 0){
		$sAutomatic = Core::UpdateSetting('automatic_updates', "0");
		$sErrors[] = array("result" => "Automatic updates have been turned off.", "type" => "success");
	}
}

if($sAction == force){
	$sSSH = new Net_SSH2('127.0.0.1');
	$sKey = new Crypt_RSA();
	$sKey->loadKey(file_get_contents($cphp_config->settings->rootkey));
	if($sSSH->login("root", $sKey)) {
		$sOldVersion = Core::GetSetting('current_version');
		$sSSH->exec("cd /var/feathur/; git pull; cd /var/feathur/feathur/; php update.php; rm -rf update.php;");
		$sLastUpdate = Core::UpdateSetting('last_update_check', time());
		$sCurrentVersion = Core::GetSetting('current_version');
		if($sCurrentVersion->sValue != $sOldVersion->sValue){
			$sErrors[] = array("result" => "Force update completed.", "type" => "success");
		} else {
			$sErrors[] = array("result" => "Force update completed!", "type" => "error");
		}
	}
	$sJson = 1;
}

$sAutomaticUpdates = Core::GetSetting('automatic_updates');
$sUpdates[] = check_updates();

$sContent .= Templater::AdvancedParse($sAdminTemplate->sValue.'/update', $locale->strings, array("AutomaticUpdates" => $sAutomaticUpdates->sValue,
																								"Updates" => $sUpdates,
																								"Outdated" => $sUpdates[0]["update"],
																								"Errors" => $sErrors));
																								
if($sJson == 1){
	echo json_encode(array("content" => $sContent));
	die();
}

unset($sErrors);