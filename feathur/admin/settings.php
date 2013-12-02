<?php
if($sUser->sPermissions != 7){
	die("Sorry you've accessed our system without permission");
}

if(!empty($sRequested["GET"]["submit"])){
	$sTitle = Core::UpdateSetting('title', preg_replace('/[^a-zA-Z0-9\s]/', '', $sRequested["POST"]["title"]));
	$sDescription = Core::UpdateSetting('description', preg_replace('/[^a-zA-Z0-9\s]/', '', $sRequested["POST"]["description"]));
	$sPanelUrl = Core::UpdateSetting('panel_url', preg_replace('/[^a-zA-Z0-9.\s]/', '', $sRequested["POST"]["panel_url"]));
	$sMaintanance = Core::UpdateSetting('maintenance', preg_replace('/[^0-9]/', '', $sRequested["POST"]["maintanance"]));
	$sUpdateType = Core::UpdateSetting('update_type', preg_replace('/[^a-zA-Z0-9\s]/', '', $sRequested["POST"]["update_type"]));
	$sMailSetting = Core::UpdateSetting('mail', preg_replace('/[^0-9]/', '', $sRequested["POST"]["mail"]));
	$sMailUsernameSetting = Core::UpdateSetting('mail_username', preg_replace('/[^a-zA-Z0-9\s]/', '', $sRequested["POST"]["mail_username"]));
	
	if((!empty($sRequested["POST"]["mail_password"])) && ($sRequested["POST"]["mail_password"] != 'password')){
		$sMailPasswordSetting = Core::UpdateSetting('mail_password', $sRequested["POST"]["mail_password"]);
	}
	
	$sBandwidthAccounting = Core::UpdateSetting('bandwidth_accounting', preg_replace('/[^a-z]/', '', $sRequested["POST"]["bandwidth_accounting"]));
	$sTemplate = Core::UpdateSetting('template', preg_replace('/[^a-zA-Z0-9\s]/', '', $sRequested["POST"]["template"]));
	$sAdminTemplate = Core::UpdateSetting('admin_template', preg_replace('/[^a-zA-Z0-9\s]/', '', $sRequested["POST"]["admin_template"]));
	echo json_encode(array("json" => 1, "type" => "success", "result" => "Settings updated successfuly."));
	die();
}

$sPage = "settings";
$sPageType = "settings";


$sContent = Templater::AdvancedParse($sAdminTemplate->sValue.'/settings', $locale->strings, array());