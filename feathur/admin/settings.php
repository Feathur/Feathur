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
	$sSendgrid = Core::UpdateSetting('sendgrid', preg_replace('/[^0-9]/', '', $sRequested["POST"]["sendgrid"]));
	$sSendgridUsername = Core::UpdateSetting('sendgrid_username', preg_replace('/[^a-zA-Z0-9\s]/', '', $sRequested["POST"]["sendgrid_username"]));
	
	if((!empty($sRequested["POST"]["sendgrid_password"])) && ($sRequested["POST"]["sendgrid_password"] != 'password')){
		$sSendgridPassword = Core::UpdateSetting('sendgrid_password', $sRequested["POST"]["sendgrid_password"]);
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