<?php
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

// Admin Template
$sAdminTemplate = Core::GetSetting('admin_template');
NewTemplater::SetGlobalVariable("AdminTemplate", $sAdminTemplate->sValue);

// Description
$sDescription = Core::GetSetting('description');
NewTemplater::SetGlobalVariable("Description", $sDescription->sValue);

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

// Panel URL
$sPanelURL = Core::GetSetting('panel_url');
NewTemplater::SetGlobalVariable("PanelURL", $sPanelURL->sValue);

// Maintenance
$sMaintanance = Core::GetSetting('maintenance');
NewTemplater::SetGlobalVariable("Maintanance", $sMaintanance->sValue);

// Update Type
$sMaintanance = Core::GetSetting('update_type');
NewTemplater::SetGlobalVariable("UpdateType", $sUpdateType->sValue);

// Sendgrid
$sSendgrid = Core::GetSetting('sendgrid');
NewTemplater::SetGlobalVariable("Sendgrid", $sSendgrid->sValue);

// Sendgrid User
$sSendgridUsername = Core::GetSetting('sendgrid_username');
NewTemplater::SetGlobalVariable("SendgridUsername", $sSendgridUsername->sValue);

// Check For Sendgrid Password
$sSendgridPassword = Core::GetSetting('sendgrid_password');
$sSendgridPassword = $sSendgridPassword->sValue;
if(!empty($sSendgridPassword)){
	NewTemplater::SetGlobalVariable("SendgridPassword", "1");
}

// Bandwidth Accounting
$sBandwidthAccounting = Core::GetSetting('bandwidth_accounting');
NewTemplater::SetGlobalVariable("BandwidthAccounting", $sBandwidthAccounting->sValue);

// License Setting
// Please don't remove or edit this code, a lot of work went into Feathur.
// Thank us for our work by leaving this code here or by paying for a license.
// While I realize this won't stop anyone who really wants to disable the "alert" system, it might prevent someone who knows nothing about PHP.
$sLicense = Core::GetSetting('license');
NewTemplater::SetGlobalVariable("License", $sLicense->sValue);

if($sSendGrid->sValue == 1){
	include("./includes/library/sendgrid/SendGrid_loader.php");
}

if(isset($_SESSION["user_id"])){
	$sUser = new User($_SESSION["user_id"]);
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