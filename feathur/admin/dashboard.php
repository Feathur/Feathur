<?php
if($sUser->sPermissions != 7){
	die("Sorry you've accessed our system without permission");
}

if($sServerList = $database->CachedQuery("SELECT * FROM `servers`", array())){
	foreach($sServerList->data as $sServer){
		$sServer = new Server($sServer["id"]);
		
		// Defines left or right in template.
		if($sType == 1){
			$sType = 0;
		} else {
			$sType = 1;
		}
		
		// Calculates hard disk usage percentages.
		$sServerHDF = $sServer->sHardDiskFree;
		$sServerHDT = $sServer->sHardDiskTotal;
		if((!empty($sServerHDF)) && (!empty($sServerHDT))){
			$sHardDiskUsed = (100 - (round(((100 / $sServer->sHardDiskTotal) * $sServer->sHardDiskFree), 1)));
			$sHardDiskFree = (round(((100 / $sServer->sHardDiskTotal) * $sServer->sHardDiskFree), 1));
		} else {
			$sHardDiskUsed = 1;
			$sHardDiskFree = 1;
		}
		
		// Calculates memory usage percentages.
		$sServerFM = $sServer->sFreeMemory;
		$sServerTM = $sServer->sTotalMemory;
		if((!empty($sServerTM)) && (!empty($sServerFM))){
			$sRAMUsed = (100 - (round(((100 / $sServer->sTotalMemory) * $sServer->sFreeMemory), 1)));
			$sRAMFree = (round(((100 / $sServer->sTotalMemory) * $sServer->sFreeMemory), 1));
		} else {
			$sRAMUsed = 1;
			$sRAMFree = 1;
		}
		
		// Calculates bandwidth average usage in mbps.
		$sBandwidthDifference = "N/A";
		$sLastCheck = $sServer->sLastCheck;
		$sPreviousCheck = $sServer->sPreviousCheck;
		$sBandwidth = $sServer->sBandwidth;
		$sLastBandwidth = $sServer->sLastBandwidth;
		if((!empty($sLastCheck)) && (!empty($sPreviousCheck)) && (!empty($sBandwidth)) && (!empty($sLastBandwidth))){
			$sTimeDifference = $sLastCheck - $sPreviousCheck;
			if(!empty($sTimeDifference)){
				$sBandwidthDifference = round((($sBandwidth - $sLastBandwidth) / $sTimeDifference), 2);
				// Alert if bandwidth average over 100 mbps.
				if($sBandwidthDifference > 100){
					$sHigh[] = array("name" => $sServer->sName);
				}
				$sBandwidthDifference = "{$sBandwidthDifference} Mbps";
			}
		}
		
		$sStatistics[] = array("name" => $sServer->sName,
								"load_average" => $sServer->sLoadAverage,
								"disk_usage" => $sHardDiskUsed,
								"disk_free" => $sHardDiskFree,
								"ram_usage" => $sRAMUsed,
								"ram_free" => $sRAMFree,
								"status" => $sServer->sStatus,
								"uptime" => ConvertTime(round($sServer->sHardwareUptime, 0)),
								"type" => $sType,
								"bandwidth" => $sBandwidthDifference);
		
		if(empty($sServer->sStatus)){
			$sDown[] = array("name" => $sServer->sName);
		}
		
		// Cleanup just in case.
		unset($sHardDiskUsed);
		unset($sHardDiskFree);
		unset($sRAMUsed);
		unset($sRAMFree);
		unset($sLastCheck);
		unset($sPreviousCheck);
		unset($sBandwidth);
		unset($sLastBandwidth);
		unset($sBandwidthDifference);
	}
}

$sPage = "dashboard";
$sPageType = "admin";

$sContent = Templater::AdvancedParse($sAdminTemplate->sValue.'/status', $locale->strings, array("Statistics" => $sStatistics, "Down" => $sDown, "High" => $sHigh, "Status" => $sRequested["GET"]["json"]));

if(!empty($sRequested["GET"]["json"])){
	echo json_encode(array("content" => $sContent));
	die();
}