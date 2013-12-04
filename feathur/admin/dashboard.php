<?php
if($sUser->sPermissions != 7){
	die("Sorry you've accessed our system without permission");
}

if($sServerList = $database->CachedQuery("SELECT * FROM `servers`", array())){
	foreach($sServerList->data as $sServer){
		$sServer = new Server($sServer["id"]);
		
		if($sType == 1){
			$sType = 0;
		} else {
			$sType = 1;
		}
		
		if((!empty($sServer->HardDiskFree)) && (!empty($sServer->HardDiskTotal))){
			$sHardDiskUsed = (100 - (round(((100 / $sServer->sHardDiskTotal) * $sServer->sHardDiskFree), 1)));
			$sHardDiskFree = (round(((100 / $sServer->sHardDiskTotal) * $sServer->sHardDiskFree), 1));
		}
		
		if((!empty($sServer->TotalMemory)) && (!empty($sServer->FreeMemory))){
			$sRAMUsed = (100 - (round(((100 / $sServer->sTotalMemory) * $sServer->sFreeMemory), 1)));
			$sRAMFree = (round(((100 / $sServer->sTotalMemory) * $sServer->sFreeMemory), 1));
		}
		
		$sStatistics[] = array("name" => $sServer->sName,
								"load_average" => $sServer->sLoadAverage,
								"disk_usage" => $sHardDiskUsed,
								"disk_free" => $sHardDiskFree,
								"ram_usage" => $sRAMUsed,
								"ram_free" => $sRAMFree,
								"status" => $sServer->sStatus,
								"uptime" => ConvertTime(round($sServer->sHardwareUptime, 0)),
								"type" => $sType);
		
		if(empty($sServer->sStatus)){
			$sDown[] = array("name" => $sServer->sName);
		}
	}
}

$sPage = "dashboard";
$sPageType = "admin";

$sContent = Templater::AdvancedParse($sAdminTemplate->sValue.'/status', $locale->strings, array("Statistics" => $sStatistics, "Down" => $sDown, "Status" => $sRequested["GET"]["json"]));

if(!empty($sRequested["GET"]["json"])){
	echo json_encode(array("content" => $sContent));
	die();
}