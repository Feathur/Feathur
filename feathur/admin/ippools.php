<?php
if($sUser->sPermissions != 7){
	die("Sorry you've accessed our system without permission");
}

$sPage = "ippools";
$sPageType = "settings";
$uType = $_GET['type'];
$uPool = $_GET['pool'];

if(!isset($uType)){
	$sContent .= Templater::AdvancedParse($sAdminTemplate->sValue.'/ippools', $locale->strings, array());
} else {
	if(isset($uPool)){
		$sData = Block::list_pool($sType, $sPool);
		$sContent .= Templater::AdvancedParse($sAdminTemplate->sValue.'/ippools', $locale->strings, array("Type" => $sType, "Pool" => $uPool, "IPList" => $sData["IPList"], "ServerList" => $sData["ServerList"], "AvailableServers" => $sData["AvailableServers"], "BlockName" => $sData["BlockName"]));
	} else {
		$sBlockList = Block::block_list($sType);
		$sContent .= Templater::AdvancedParse($sAdminTemplate->sValue.'/ippools', $locale->strings, array("Type" => $sType, "BlockList" => $sBlockList));
	}
}