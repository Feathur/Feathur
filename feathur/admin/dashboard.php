<?php
if($sUser->sPermissions != 7){
	die("Sorry you've accessed our system without permission");
}

$sPage = "dashboard";
$sPageType = "admin";

$sContent = Templater::AdvancedParse($sAdminTemplate->sValue.'/status', $locale->strings, array());