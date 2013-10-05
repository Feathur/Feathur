<?php
include('./includes/loader.php');

$sId = $_GET['id'];
$sView = $_GET['view'];
$sAction = $_GET['action'];
$sSearch = $_GET['search'];
$sType = $_GET['type'];
$sPage = "admin";
$sPageType = "";

if(empty($sUser)){
	header("Location: index.php");
	die();
}

if($sUser->sPermissions != 7){
	header("Location: main.php");
	die();
}

if($sView == 'createvps'){
	include("./admin/createvps.php");
} elseif($sView == 'addserver'){
	include("./admin/addserver.php");
} elseif($sView == 'templates'){
	include("./admin/templates.php");
} elseif($sView == 'list'){
	include("./admin/list.php");
} elseif($sView == 'adduser'){
	include("./admin/adduser.php");
} elseif($sView == 'ippools'){
	include("./admin/ippools.php");
} elseif($sView == 'update'){
	include("./admin/update.php");
} else {
	include("./admin/dashboard.php");
}

echo Templater::AdvancedParse($sTemplate->sValue.'/master', $locale->strings, array("Content" => $sContent, "Page" => $sPage, "PageType" => $sPageType, "Errors" => $sErrors));