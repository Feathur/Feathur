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

// License
// Please don't remove or edit this code, a lot of work went into Feathur.
// Thank us for our work by leaving this code here or by paying for a license.
// While I realize this won't stop anyone who really wants to disable the "alert" system, it might prevent someone who knows nothing about PHP.
eval(base64_decode("JHNMaWNlbnNlID0gQ29yZTo6R2V0U2V0dGluZygnbGljZW5zZScpOw0KTmV3VGVtcGxhdGVyOjpTZXRHbG9iYWxWYXJpYWJsZSgiTGljZW5zZSIsICRzTGljZW5zZS0+c1ZhbHVlKTs="));

// Send Grid
if($sNewSetting = $database->CachedQuery("SELECT * FROM settings WHERE `setting_name` = 'sendgrid'", array())){
	$sSendGrid = Core::GetSetting('sendgrid');
	if($sSendGrid->sValue == 1){
		include("./includes/library/sendgrid/SendGrid_loader.php");
	}
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

function ErrorHandler($errno, $errstr, $errfile, $errline) {
	error_reporting(0);
	global $sTemplate;
	$sErrors[] = array("red" => 'Unable to connect to the host node, please contact <a href="https://bluevm.com" target="_blank">BlueVM Customer Support</a>.');
	$sView = Templater::AdvancedParse($sTemplate->sValue.'/error', $locale->strings, array());
	echo Templater::AdvancedParse($sTemplate->sValue.'/master', $locale->strings, array("Content" => $sView, "Page" => "error", "Errors" => $sErrors));
	die();
}

set_error_handler('ErrorHandler', E_USER_NOTICE);