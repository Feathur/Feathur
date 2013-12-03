<?php
if($sUser->sPermissions != 7){
	die("Sorry you've accessed our system without permission");
}

if($sServerList = $database->CachedQuery("SELECT * FROM servers", array())){
	foreach($sServerList->data as $sServer){
		$sServer = new Server($sServer["id"]);
		
		if($sType == 1){
			$sType = 0;
		} else {
			$sType = 1;
		}
		
		$sStatistics[] = array("name" => $sServer->sName,
								"load_average" => $sServer->sLoadAverage,
								"disk_usage" => (100 - (round(((100 / $sServer->sHardDiskTotal) * $sServer->sHardDiskFree), 1))),
								"ram_usage" => (100 - (round(((100 / $sServer->sTotalMemory) * $sServer->sFreeMemory), 1))),
								"status" => $sServer->sStatus,
								"uptime" => ConvertTime(round($sServer->sHardwareUptime, 0)),
								"type" => $sType);
	}
}

$sPage = "dashboard";
$sPageType = "admin";

$sContent = Templater::AdvancedParse($sAdminTemplate->sValue.'/status', $locale->strings, array("Statistics" => $sStatistics));

if(!empty($sRequested["GET"]["json"])){
	echo json_encode(array("content" => $sContent));
}