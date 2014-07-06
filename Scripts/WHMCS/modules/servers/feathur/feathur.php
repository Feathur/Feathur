<?php

function feathur_GeneralConfig(){
	// Set these variables accordingly.
	$sConfig = array(
					"master" => "",
					"email" => "",
					"password" => "",
					"whmcs_admin_user" => "admin",
				);
	return $sConfig;
}

function feathur_RemoteConnect($sPost, $sDestination){
	$sPost = http_build_query($sPost);
	$sCurl = curl_init();
	curl_setopt($sCurl, CURLOPT_URL, $sDestination);
	curl_setopt($sCurl, CURLOPT_POST, 1);
	curl_setopt($sCurl, CURLOPT_POSTFIELDS, $sPost);
	curl_setopt($sCurl, CURLOPT_RETURNTRANSFER, true);
	
	$sOutput = curl_exec($sCurl);
	curl_close($sCurl);
	return json_decode($sOutput, true);
} 

function feathur_ConfigOptions() {
	$sConfig = feathur_GeneralConfig();
	$sPost = array(
				"email" => $sConfig["email"],
				"password" => $sConfig["password"],
				"action" => "listservers"
				);
	$sServers = feathur_RemoteConnect($sPost, $sConfig["master"]);
	foreach($sServers as $value){
		$sServerList .= $value["ip"].',';
	}
	
	$sPost = array(
				"email" => $sConfig["email"],
				"password" => $sConfig["password"],
				"action" => "listgroups"
				);
	$sGroups = feathur_RemoteConnect($sPost, $sConfig["master"]);
	foreach($sGroups as $value){
		$sGroupList .= "Group=>{$value["id"]}-{$value["name"]},";
	}
	
	if(is_array($sGroups)){
		$sList = $sGroupList;
	} else {
		$sList = $sServerList;
	}
	
	$sConfigArray = array(
		"Server / Group" => array("Type" => "dropdown", "Options" => $sList, "Description" => "Will list servers if no groups exist."),
		"RAM" => array("Type" => "text", "Size" => "25", "Default" => "256", "Description" => "MB" ),
		"SWAP" => array("Type" => "text", "Size" => "25", "Default" => "256", "Description" => "MB" ),
		"Disk" => array("Type" => "text", "Size" => "25", "Default" => "10", "Description" => "GB" ),
		"CPU Units" => array("Type" => "text", "Size" => "25", "Default" => "1000", "Description" => "Default: 1000" ),
		"CPU Limit" => array("Type" => "text", "Size" => "25", "Default" => "100", "Description" => "100/core" ),
		"Bandwidth Limit" => array("Type" => "text", "Size" => "25", "Default" => "1024", "Description" => "GB" ),
		"Inodes" => array("Type" => "text", "Size" => "25", "Default" => "200000", "Description" => "Default: 200000" ),
		"Max Processes" => array("Type" => "text", "Size" => "25", "Default" => "128", "Description" => "Default: 128" ),
		"Max Connections" => array("Type" => "text", "Size" => "25", "Default" => "80", "Description" => "Default: 80" ),
		"IP Addresses" => array("Type" => "dropdown", "Options" => "1,2,3,4,5,6,7,8,9,10"),
		"Nameserver" => array("Type" => "text", "Size" => "25", "Default" => "8.8.8.8", "Description" => "Default: 8.8.8.8" ),
	);
	return $sConfigArray;
}

function feathur_CreateAccount($sData) {
	$sConfig = feathur_GeneralConfig();
	$sPost = array(
				"email" => $sConfig["email"],
				"password" => $sConfig["password"],
				"action" => "createvps",
				"useremail" => $sData["clientsdetails"]["email"],
				"username" => $sData["clientsdetails"]["firstname"],
				"server" => $sData["configoption1"],
				"ram" => $sData["configoption2"],
				"swap" => $sData["configoption3"],
				"disk" => $sData["configoption4"],
				"cpuunits" => $sData["configoption5"],
				"cpulimit" => $sData["configoption6"],
				"bandwidthlimit" => $sData["configoption7"],
				"inodes" => $sData["configoption8"],
				"numproc" => $sData["configoption9"],
				"numiptent" => $sData["configoption10"],
				"ipaddresses" => $sData["configoption11"],
				"nameserver" => $sData["configoption12"],
				"hostname" => preg_replace('/[^A-Za-z0-9-.]/', '', $sData["domain"]),
				);

	$sSetupVPS = feathur_RemoteConnect($sPost, $sConfig["master"]);
	if($sSetupVPS["type"] == 'success'){
		$sCustomField = mysql_fetch_array(mysql_query("SELECT * FROM tblcustomfields WHERE relid='{$sData["pid"]}' && fieldname='feathurvpsid'"));
		$sCommand = "updateclientproduct";
		$sPostFields["serviceid"] = $sData["serviceid"];
		$sPostFields["serviceusername"] = $sData["clientsdetails"]["email"];
		$sPostFields["servicepassword"] = "";
		$sCustomFields = array($sCustomField["id"] => $sSetupVPS["vps"]);
		$sPostFields["customfields"] = base64_encode(serialize($sCustomFields));
		$sAPIPost = localAPI($sCommand, $sPostFields, $sConfig["whmcs_admin_user"]);
		if($sAPIPost["result"] == success){
			$sResult = "success";
		} else {
			$sResult = $sAPIPost["message"];
		}
	} else {
		$sResult = $sSetupVPS["result"];
	}
	return $sResult;
}

function feathur_TerminateAccount($sData) {
	$sConfig = feathur_GeneralConfig();
	$sPost = array(
				"email" => $sConfig["email"],
				"password" => $sConfig["password"],
				"action" => "terminatevps",
				"useremail" => $sData["clientsdetails"]["email"],
				"vpsid" => $sData["customfields"]["feathurvpsid"]
				);

	$sTerminateVPS = feathur_RemoteConnect($sPost, $sConfig["master"]);
	if($sTerminateVPS["type"] == 'success'){
		$sResult = "success";
	} else {
		$sResult = $sTerminateVPS["result"];
	}
	return $sResult;
}

function feathur_SuspendAccount($sData) {
	$sConfig = feathur_GeneralConfig();
	$sPost = array(
				"email" => $sConfig["email"],
				"password" => $sConfig["password"],
				"action" => "suspendvps",
				"useremail" => $sData["clientsdetails"]["email"],
				"vpsid" => $sData["customfields"]["feathurvpsid"]
				);

	$sSuspendVPS = feathur_RemoteConnect($sPost, $sConfig["master"]);
	if($sSuspendVPS["type"] == 'success'){
		$sResult = "success";
	} else {
		$sResult = $sSuspendVPS["result"];
	}
	return $sResult;
}

function feathur_UnsuspendAccount($sData) {
	$sConfig = feathur_GeneralConfig();
	$sPost = array(
				"email" => $sConfig["email"],
				"password" => $sConfig["password"],
				"action" => "unsuspendvps",
				"useremail" => $sData["clientsdetails"]["email"],
				"vpsid" => $sData["customfields"]["feathurvpsid"]
				);

	$sUnsuspendVPS = feathur_RemoteConnect($sPost, $sConfig["master"]);
	if($sUnsuspendVPS["type"] == 'success'){
		$sResult = "success";
	} else {
		$sResult = $sUnsuspendVPS["result"];
	}
	return $sResult;
}