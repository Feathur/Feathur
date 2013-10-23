<?php
include('./includes/functions/user.class.php');
include('./includes/functions/core.class.php');
include('./includes/functions/settings.class.php');
include('./includes/functions/templates.class.php');
include('./includes/functions/vps.class.php');
include('./includes/functions/vps.openvz.class.php');
include('./includes/functions/vps.kvm.class.php');
include('./includes/functions/servers.class.php');
include('./includes/functions/ipaddresses.class.php');
include('./includes/functions/blocks.class.php');
include('./includes/functions/server.blocks.class.php');
include('./includes/library/Net/SSH2.php');
include('./includes/library/Crypt/RSA.php');
include('./includes/library/File/ANSI.php');
include('./includes/functions/pull.class.php');
include('./includes/functions/history.class.php');
include('./includes/functions/statistics.class.php');
include('./includes/functions/rdns.class.php');
include('./includes/functions/transfer.class.php');

function ConvertTime($ss) {
	$sSeconds = $ss%60;
	$sMinutes = floor(($ss%3600)/60);
	$sHours = floor(($ss%86400)/3600);
	$sDays = floor(($ss%2592000)/86400);
	$sMonths = floor($ss/2592000);
	
	if($sMonths > 0){
		$sResult[] = "{$sMonths} Months";
	}
	
	if($sDays > 0){
		$sResult[] = "{$sDays} Days";
	}
	
	if($sHours > 0){
		$sResult[] = "{$sHours}h";
	}
	
	if($sMinutes > 0){
		$sResult[] = "{$sMinutes}m";
	}
	
	if($sSeconds > 0){
		$sResult[] = "{$sSeconds}s";
	}
	
	$sTotal = count($sResult);
	
	foreach($sResult as $value){
		$sCurrent++;
		if($sCurrent != $sTotal){
			$sReturn .= $value.', ';
		} else {
			$sReturn .= $value.' ';
		}
	}
	
	return $sReturn;
}

function formatBytes($sSize, $sPrecision = 2){
	$sBase = log($sSize) / log(1024);
	$sSuffixes = array('MB', 'GB', 'TB');   

	return round(pow(1024, $sBase - floor($sBase)), $sPrecision) . $sSuffixes[floor($sBase)];
}

function generate_mac(){
	global $database;
	while($i <= 6){
		$sGen[] = dechex(rand(0, 15));
		$i++;
	}
									
	$sMac = "00:16:3c:".$sGen[0].$sGen[1].":".$sGen[2].$sGen[3].":".$sGen[4].$sGen[5];
	if($sExists = $database->CachedQuery("SELECT * FROM vps WHERE `mac` LIKE :Mac", array('Mac' => $sMac))){
		return generate_mac(); 
	} else {
		return $sMac;
	}
}

function SortPrimaryIP($a, $b) {
	return strcmp($b['primary'], $a['primary']);
}