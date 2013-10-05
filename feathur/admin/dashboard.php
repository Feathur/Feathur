<?php
if($sUser->sPermissions != 7){
	die("Sorry you've accessed our system without permission");
}

$sPage = "dashboard";
$sPageType = "admin";

if(empty($_GET['errors'])){
	error_reporting(E_ERROR | E_PARSE);
}


$sStatus = $_GET['status'];

$sPull = new Pull;
$sData = $sPull->Start();

$sServers = $database->CachedQuery("SELECT * FROM servers", array(), 10);

foreach($sServers->data as $key => $value){
	if($value["status"] == 1){
		$sTime = ConvertTime(time() - $value["up_since"]);
	} else {
		$sTime = ConvertTime(time() - $value["down_since"]);
	}
	if($value["status_type"] == full){
		$sTotal = $value["display_memory"] + $value["display_load"] + $value["display_hard_disk"];
		$sPercent = round((90 / $sTotal), 0);
		$sPercentFreeMemory = round(((100 / $value["total_memory"]) * $value["free_memory"]), 0);
		$sPercentUsedMemory = 100 - $sPercentFreeMemory;
		$sPercentFreeDisk = round(((100 / $value["hard_disk_total"]) * $value["hard_disk_free"]), 0);
		$sPercentUsedDisk = 100 - $sPercentFreeDisk;
		$sData[] = array("id" => $value["id"],
				"name" => $value["name"],
				"status_type" => $value["status_type"],
				"status" => $value["status"],
				"hardware_uptime" => ConvertTime($value["hardware_uptime"]),
				"network_uptime" => $sTime,
				"total_memory" => $value["total_memory"],
				"free_memory" => $value["free_memory"],
				"location" => $value["location"],
				"load_average" => $value["load_average"],
				"hard_disk_total" => $value["hard_disk_total"],
				"hard_disk_free" => $value["hard_disk_free"],
				"display_memory" => $value["display_memory"],
				"display_load" => $value["display_load"],
				"display_hard_disk" => $value["display_hard_disk"],
				"display_network_uptime" => $value["display_network_uptime"],
				"display_hardware_uptime" => $value["display_hardware_uptime"],
				"display_location" => $value["display_location"],
				"display_hs" => $value["display_hs"],
				"display_bandwidth" => $value["display_bandwidth"],
				"bandwidth" => round((($value["bandwidth"] - $value["last_bandwidth"]) / ($value["last_check"] - $value["previous_check"])), 2),
				"percent" => $sPercent,
				"percent_free_memory" => $sPercentFreeMemory,
				"percent_used_memory" => $sPercentUsedMemory,
				"percent_used_disk" => $sPercentUsedDisk,
				"percent_free_disk" => $sPercentFreeDisk,
				);
	} elseif(($value["status_type"] == tcp_ping) || ($value["status_type"] == icmp_ping)){
		$sData[] = array("id" => $value["id"],
				"name" => $value["name"],
				"status_type" => $value["status_type"],
				"status" => $value["status"],
				"location" => $value["location"],
				"hardware_uptime" => "N/A",
				"network_uptime" => $sTime,
				"display_hs" => $value["display_hs"],
				"total_memory" => 0,
				"free_memory" => 0,
				"load_average" => 0,
				"hard_disk_total" => 0,
				"hard_disk_free" => 0,
				"display_memory" => 0,
				"display_load" => 0,
				"display_hard_disk" => 0,
				"display_network_uptime" => $value["display_network_uptime"],
				"display_hardware_uptime" => 0,
				);
	}
}

if(!is_array($sData)){
	$sData = 0;
}

$sContent = Templater::AdvancedParse($sAdminTemplate->sValue.'/status', $locale->strings, array("Data" => $sData, "Status" => $sStatus));

if(!empty($sStatus)){
	$sPull = preg_replace('/\r\n|\r|\n/', '', $sContent);
	echo json_encode(array("content" => $sPull));
	die();
}