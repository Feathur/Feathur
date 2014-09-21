<?php
date_default_timezone_set('UTC');
$sTimestamp = time();

// Get prerequisites.
$sId = $_GET['id'];
$sAction = $_GET['action'];
$sRequested = array("GET" => $_GET, "POST" => $_POST);
$sRequest = basename($_SERVER['PHP_SELF']);

// Site title
$sTitle = Core::GetSetting('title');
NewTemplater::SetGlobalVariable("Title", $sTitle->sValue);

// Request
NewTemplater::SetGlobalVariable("Request", $sRequest);

// Template
$sTemplate = Core::GetSetting('template');
NewTemplater::SetGlobalVariable("Template", $sTemplate->sValue);

// Description
$sDescription = Core::GetSetting('description');
NewTemplater::SetGlobalVariable("Description", $sDescription->sValue);

// Panel URL
$sPanelURL = Core::GetSetting('panel_url');
NewTemplater::SetGlobalVariable("PanelURL", $sPanelURL->sValue);

// Maintenance
$sMaintanance = Core::GetSetting('maintenance');
NewTemplater::SetGlobalVariable("Maintanance", $sMaintanance->sValue);

// Update Type
$sUpdateType = Core::GetSetting('update_type');
NewTemplater::SetGlobalVariable("UpdateType", $sUpdateType->sValue);

// Panel Mode
$sPanelMode = Core::GetSetting('panel_mode');
NewTemplater::SetGlobalVariable("PanelMode", $sPanelMode->sValue);

// Bandwidth Accounting
$sBandwidthAccounting = Core::GetSetting('bandwidth_accounting');
NewTemplater::SetGlobalVariable("BandwidthAccounting", $sBandwidthAccounting->sValue);

// Version
NewTemplater::SetGlobalVariable("FeathurVersion", file_get_contents('/var/feathur/version.txt'));

// License Setting
// Please don't remove or edit this code, a lot of work went into Feathur.
// Thank us for our work by leaving this code here or by paying for a license.
// While I realize this won't stop anyone who really wants to disable the "alert" system, it might prevent someone who knows nothing about PHP.
$sLicense = Core::GetSetting('license');
NewTemplater::SetGlobalVariable("License", $sLicense->sValue);

// Mail Settings
$sMail = Core::GetSetting('mail');
NewTemplater::SetGlobalVariable("Mail", $sMail->sValue);

// Mail User
$sMailUsername = Core::GetSetting('mail_username');
NewTemplater::SetGlobalVariable("MailUsername", $sMailUsername->sValue);

// Check For Mail Password
$sMailPassword = Core::GetSetting('mail_password');
$sMailPassword = $sMailPassword->sValue;
if(!empty($sMailPassword)){
	NewTemplater::SetGlobalVariable("MailPassword", "1");
}

if($sMail->sValue == 1){
	include("./includes/library/sendgrid/SendGrid_loader.php");
} elseif($sMail->sValue == 2){
	include("./includes/library/mandril/mandril.php");
}

if(isset($_SESSION["user_id"])){
	$sUser = new User($_SESSION["user_id"]);
	
	if($sUser->sPermissions == 7){
		// Admin Template
		$sAdminTemplate = Core::GetSetting('admin_template');
		NewTemplater::SetGlobalVariable("AdminTemplate", $sAdminTemplate->sValue);
		
		// History Difference
		$sHistoryDifference = Core::GetSetting('history_difference');
		NewTemplater::SetGlobalVariable("HistoryDifference", $sHistoryDifference->sValue);

		// Refresh Time
		$sRefreshTime = Core::GetSetting('refresh_time');
		NewTemplater::SetGlobalVariable("RefreshTime", $sRefreshTime->sValue);

		// Max History
		$sMaxHistory = Core::GetSetting('max_history');
		NewTemplater::SetGlobalVariable("MaxHistory", $sMaxHistory->sValue);

		// Max Statistics
		$sMaxStatistics = Core::GetSetting('max_statistics');
		NewTemplater::SetGlobalVariable("MaxStatistics", $sMaxStatistics->sValue);
		
		// Templates redone message
		$sTemplatesRedone = Core::GetSetting('template_redone_message');
		NewTemplater::SetGlobalVariable("TemplatesRedone", $sTemplatesRedone->sValue);
	}
	
	$_SESSION['permissions'] = $sUser->sPermissions;
	NewTemplater::SetGlobalVariable("Username", $sUser->sUsername);
	NewTemplater::SetGlobalVariable("UserPermissions", $sUser->sPermissions);
	
	$sPullVPS = $database->CachedQuery("SELECT * FROM vps WHERE `user_id` = :UserId", array('UserId' => $sUser->sId));
	
	if(!empty($sPullVPS)){
		foreach($sPullVPS->data as $key => $value){
			$sServer = new Server($value["server_id"]);
			if($sRequest == 'view.php'){
				if($sId == $value["id"]){
					$sViewing = 1;
				} else {
					$sViewing = 0;
				}
			} else {
				$sViewing = 0;
			}
			$sVPS[] = array("id" => $value["id"],
				"server_id" => $sServer->sId,
				"server_name" => $sServer->sName,
				"container_id" => $value["container_id"],
				"hostname" => $value["hostname"],
				"primary_ip" => $value["primary_ip"],
				"type" => ucfirst($value["type"]),
				"viewing" => $sViewing);
		}
	}
	NewTemplater::SetGlobalVariable("UserVPS", $sVPS);
}