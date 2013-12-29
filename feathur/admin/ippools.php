<?php
if($sUser->sPermissions != 7){
	die("Sorry you've accessed our system without permission");
}

$sPage = "ippools";
$sPageType = "settings";
$uType = $_GET['type'];
$uPool = $_GET['pool'];
$uAction = $_GET['action'];

if(isset($uAction)){
	$sResult = Block::$uAction($sRequested);
	if(is_array($sResult)){
		echo json_encode($sResult);
		die();
	}
}

if(!isset($uType)){
	$sContent .= Templater::AdvancedParse($sAdminTemplate->sValue.'/ippools', $locale->strings, array());
} else {
	if(isset($uPool)){
		$sData = Block::list_pool($uType, $uPool);
		$sContent .= Templater::AdvancedParse($sAdminTemplate->sValue.'/ippools', $locale->strings, array("Type" => $uType, "Pool" => $uPool, "IPList" => $sData["IPList"], "ServerList" => $sData["ServerList"], "AvailableServers" => $sData["AvailableServers"], "BlockName" => $sData["BlockName"]));
	} else {
		$sBlockList = Block::block_list($sType);
		$sContent .= Templater::AdvancedParse($sAdminTemplate->sValue.'/ippools', $locale->strings, array("Type" => $uType, "BlockList" => $sBlockList));
	}
}