<?php
include('./includes/loader.php');

if(empty($sUser)){
	header("Location: index.php");
	die();
}

if(($sUser->sPermissions == 7) && (empty($_GET['force']))){
	header("Location: admin.php");
	die();
}

$sPullVPS = $database->CachedQuery("SELECT * FROM vps WHERE `user_id` = :UserId", array('UserId' => $sUser->sId));

if(empty($sPullVPS)){
	$sErrors[] = array("red" => "You currently do not have any VPS. Please contact our support department.");
}

$sMain = Templater::AdvancedParse($sTemplate->sValue.'/main', $locale->strings, array());

echo Templater::AdvancedParse($sTemplate->sValue.'/master', $locale->strings, array("Content" => $sMain, "Page" => "main", "Errors" => $sErrors));