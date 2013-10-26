<?php
include('./includes/loader.php');

// Check for login.
if(empty($sUser)){
	header("Location: index.php");
	die();
}

$sView = Templater::AdvancedParse($sTemplate->sValue.'/about', $locale->strings, array("Username" => $sUser->sUsername));
echo Templater::AdvancedParse($sTemplate->sValue.'/master', $locale->strings, array("Content" => $sView, "Page" => "about", "Errors" => $sErrors));