<?php
if($sUser->sPermissions != 7){
	die("Sorry you've accessed our system without permission");
}

$sPage = "settings";
$sPageType = "settings";


$sContent = Templater::AdvancedParse($sAdminTemplate->sValue.'/settings', $locale->strings, array());