<?php
if($sUser->sPermissions != 7){
	die("Sorry you've accessed our system without permission");
}

$sPage = "ippools";
$sPageType = "settings";
$uType = $_GET['type'];

if(!isset($sType)){
	$sContent .= Templater::AdvancedParse($sAdminTemplate->sValue.'/ippools', $locale->strings, array());
} else {
	$sBlockList = Block::block_list($sType);
	$sContent .= Templater::AdvancedParse($sAdminTemplate->sValue.'/ippools', $locale->strings, array("Type" => $sType, "BlockList" => $sBlockList));
}