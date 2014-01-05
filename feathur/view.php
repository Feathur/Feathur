<?php
include('./includes/loader.php');

// Check for login.
if(empty($sUser)){
	header("Location: index.php");
	die();
}

// Check to make sure the user is actually trying to view a vps.
if((empty($sId)) || (!is_numeric($sId))){
	header("Location: main.php");
	die();
}

// Check to see if the user owns the VPS or if the VPS exists at all (if admin).
try {
	if($sUser->sPermissions != 7){
		$database->CachedQuery("SELECT * FROM vps WHERE `id` = :VPSId AND `user_id` = :UserId", array('VPSId' => $sId, 'UserId' => $sUser->sId));
	} else {
		$database->CachedQuery("SELECT * FROM vps WHERE `id` = :VPSId", array('VPSId' => $sId));
	}
	
	$sVPS = new VPS($sId);
	
} catch (Exception $e) {
	header("Location: main.php");
	die();
}

if(($sVPS->sUserId != $sUser->sId) && ($sUser->sPermissions != 7)){
	header("Location: main.php");
	die();
}

// Restrict access to the vps if the user's vps is suspended.
$sSuspended = $sVPS->sSuspended;
if((!empty($sSuspended)) && ($sUser->sPermissions != 7)){
	if($sSuspended == 2){
		$sUserView = Templater::AdvancedParse($sTemplate->sValue.'/abuse', $locale->strings, array("Hostname" => $sVPS->sHostname));
	} else {
		$sUserView = Templater::AdvancedParse($sTemplate->sValue.'/suspended', $locale->strings, array("Hostname" => $sVPS->sHostname));
	} 
	echo Templater::AdvancedParse($sTemplate->sValue.'/master', $locale->strings, array("Content" => $sUserView, "Page" => "main"));
	die();
}

// Restrict access to the vps if the VPS is being transferred.
if($sTransfer = $database->CachedQuery("SELECT * FROM transfers WHERE `completed` = 0 AND `vps_id` = :VPSId", array('VPSId' => $sVPS->sId))){
	$sUserView = Templater::AdvancedParse($sTemplate->sValue.'/transfer', $locale->strings, array());
	echo Templater::AdvancedParse($sTemplate->sValue.'/master', $locale->strings, array("Content" => $sUserView, "Page" => "main", "Errors" => $sErrors, "VPSId" => $sVPS->sId));
	die();
}

if(!empty($sAction)){
	set_time_limit(100);
	$sStart = new $sVPS->sType;
	$sDBAction = "database_{$sVPS->sType}_{$sAction}";
	$sServerAction = "{$sVPS->sType}_{$sAction}";
	if(((method_exists($sStart, $sDBAction) === true)) && ((method_exists($sStart, $sServerAction) === true))){
		$sDBResult = $sStart->$sDBAction($sUser, $sVPS, $sRequested);
		if(is_array($sDBResult)){
			echo json_encode($sDBResult);
			die();
		}
		$sServerResult = $sStart->$sServerAction($sUser, $sVPS, $sRequested);
		if(!empty($sServerResult["json"])){
			echo json_encode($sServerResult);
			die();
		}
	} else {
		echo json_encode(array("json" => 1, "type" => "error", "result" => "Invalid action requested. Please try again.", "reload" => 1));
		die();
	}
}

// Check to make sure VPS isn't rebuilding.
if($sVPS->sRebuilding == 1){
	$sUserView .= Templater::AdvancedParse($sTemplate->sValue.'/rebuild', $locale->strings, array("VPS" => array("data" => $sVPS->uData)));
	echo Templater::AdvancedParse($sTemplate->sValue.'/master', $locale->strings, array("Content" => $sUserView, "Page" => "main"));
	die();
}

$sVPSOwner = new User($sVPS->sUserId);

$sUserView .= Templater::AdvancedParse($sTemplate->sValue.'/'.$sVPS->sType.'.view', $locale->strings, array("VPS" => array("data" => $sVPS->uData), "IPs" => VPS::list_ipspace($sVPS), "Templates" => VPS::list_templates($sVPS), "Servers" => VPS::list_servers($sVPS), "User" => array("data" => $sVPSOwner->uData), "UserVPSList" => VPS::list_uservps($sVPS)));
echo Templater::AdvancedParse($sTemplate->sValue.'/master', $locale->strings, array("Content" => $sUserView, "Page" => "main"));
die();