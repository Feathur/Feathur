<?php
include('./includes/loader.php');

// Get prerequisites.
$sId = $_GET['id'];
$sAction = $_GET['action'];

// Check for login.
if(empty($sUser)){
	header("Location: index.php");
	die();
}

// Check to make sure the user is actually trying to view a vps.
if(empty($sId)){
	header("Location: main.php");
	die();
}

// Check to make sure the vps the user is trying to view is theirs or they are a admin.
$sVPS = new VPS($sId);
if(($sVPS->sUserId != $sUser->sId) && ($sUser->sPermissions != 7)){
	header("Location: main.php");
	die();
}

// Restrict access to the vps if the user's vps is suspended.
if(($sVPS->sSuspended == 1) && ($sUser->sPermissions != 7)){
	echo Templater::AdvancedParse($sTemplate->sValue.'/suspended', $locale->strings, array());
	die();
}

if($sAction == connect){
	if((!empty($_POST['hostname'])) && (!empty($_POST['port']))){
		$sView = Templater::AdvancedParse($sTemplate->sValue.'/console', $locale->strings, array("connect" => "1", "VPS" => array("data" => $sVPS->uData), "Hostname" => htmlspecialchars($_POST["hostname"], ENT_QUOTES), "Port" => htmlspecialchars($_POST["port"], ENT_QUOTES)));
	} else { 
		$sView = Templater::AdvancedParse($sTemplate->sValue.'/console', $locale->strings, array("connect" => "0", "VPS" => array("data" => $sVPS->uData)));
	}
} else {
	$sView = Templater::AdvancedParse($sTemplate->sValue.'/console', $locale->strings, array("connect" => "0", "VPS" => array("data" => $sVPS->uData)));
}

echo $sView;