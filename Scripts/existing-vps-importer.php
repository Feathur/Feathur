<?php
// Settings
// Administrative login information
$sEmail = "";
$sPassword = "";

// Default template id if OpenVZ. (Usually 1, but check database to be sure.)
$sDefaultTemplate = "";

// If conversion type equals 1, update the variable bellow to specify which node to convert.
// You can lookup the node in PHPMyAdmin.
$sNodeId = "";

// Only change this if you want to convert just one VPS from the node above. (EG: testing, missed one, etc...)
// In this case it would be the ctid of the vps.
$sSingleVPS = "";

// END OF SETTINGS.

// Check to make sure that this script isn't being executed remotely.
if(!(php_sapi_name() == 'cli')){
	die("Unfortunately this script must be executed via CLI.");
}

chdir('/var/feathur/feathur/');
include('./includes/loader.php');
error_reporting(E_ALL ^ E_NOTICE);
set_time_limit(6000);

if((!empty($sEmail)) && (!empty($sPassword))){
	$sUser = User::login($sEmail, $sPassword, 1);
	if(is_array($sUser)){
		echo $sUser['result']."\n";
		die();
	}
} else {
	die("The username and password can not be blank!\n");
	
}

if(empty($sNodeId)){
	die("You must enter a Feathur node id.");
}

// Connect to server.
$sServer = new Server($sNodeId);
$sServerConnect = $sServer->server_connect($sServer);

// Pull list of VPS
$sVPSList = $sServerConnect->exec("vzlist -H -o ctid,hostname,ip,diskspace,cpuunits,cpulimit,oomguarpages.b");
$sVPSList = preg_split('/\r\n|\r|\n/', $sVPSList);

// Filter out crap.
foreach($sVPSList as $sData){
	$sData = preg_replace("/\s+/", " ", $sData);
	$sData = explode(" ", $sData);
	foreach($sData as $sKey => $sValue){
		$sData[$sKey] = preg_replace('/\s+/', '', $sValue);
	}
	
	if(!empty($sSingleVPS)){
		if($sData[0] != $sSingleVPS){
			continue;
		}
	}
	
	echo "Inserting VPS into database...\n";
	$sRequested["POST"]["server"] = $sServer->sId;
	$sRequested["POST"]["user"] = $sUser->sId;
	$sRequested["POST"]["template"] = $sDefaultTemplate;
	// Assuming RAM and SWAP are the same. Not the greatest practice, but vzlist doesn't have a vswap offering according to man pages.
	$sRequested["POST"]["ram"] = ($sData[6] / 1024);
	$sRequested["POST"]["swap"] = ($sData[6] / 1024);
	$sRequested["POST"]["disk"] = (($sData[3] / 1024) / 1024);
	$sRequested["POST"]["inodes"] = "2000000";
	$sRequested["POST"]["numproc"] = "80";
	$sRequested["POST"]["numiptent"] = "80";
	$sRequested["POST"]["ipaddresses"] = "0";
	$sRequested["POST"]["hostname"] = $sData[1];
	$sRequested["POST"]["nameserver"] = "8.8.8.8";
	$sRequested["POST"]["cpuunits"] = $sData[4];
	$sRequested["POST"]["cpulimit"] = $sData[5];
	$sRequested["POST"]["bandwidthlimit"] = "1024";
	$sServerType = new $sServer->sType;
	$sMethod = "database_{$sServer->sType}_create";
	$sVPSId = $sServerType::$sMethod($sUser, $sRequested, 1);
	
	if(is_array($sVPSId)){
		var_dump($sVPSId);
		die();
	}
	
	$sVPS = new VPS($sVPSId);
	echo "New VPS with ID: {$sVPSId}\n";
	
	$sIPData = explode('.', $sData[2]);
	if(!$sFeathurBlockData = $database->CachedQuery("SELECT * FROM blocks WHERE `gateway` LIKE :Gateway", array("Gateway" => "%{$sIPData[0]}.{$sIPData[1]}.{$sIPData[2]}.%"))){
		echo "Adding block because one doesn't exist for this range.\n";
		$sBlock = new Block(0);
		$sBlock->uName = "Import Block";
		$sBlock->uGateway = "{$sIPData[0]}.{$sIPData[1]}.{$sIPData[2]}.";
		$sBlock->uNetmask = "255.255.255.0";
		$sBlock->InsertIntoDatabase();
	} else {
		echo "Using existing block for IP\n";
		$sBlock = new Block($sFeathurBlockData->data["0"]["id"]);
	}
	
	if(!$sIPExists = $database->CachedQuery("SELECT * FROM `ipaddresses` WHERE `ip_address` = :IP", array('IP' => $sData[2]))){
		echo "Adding IP to database.\n";
		$sIPAdd = new IP(0);
		$sIPAdd->uIPAddress = $sData[2];
		$sIPAdd->uVPSId = $sVPS->sId;
		$sIPAdd->uBlockId = $sBlock->sId;
		$sIPAdd->InsertIntoDatabase();
		
	} else {
		echo "Using existing IP.\n";
		$sIPAdd = new IP($sIPExists->data[0]["id"]);
		$sIPAdd->uVPSId = $sVPS->sId;
		$sIPAdd->InsertIntoDatabase();
	}
	
	$sVPS->uPrimaryIP = $sData[2];
	$sVPS->InsertIntoDatabase();
	
	echo "Moving VPS configs to new ID...\n";
	$sCommandList = "vzctl chkpnt {$sData[0]} --dumpfile /tmp/Dump.{$sData[0]};";
	$sCommandList .= "mv /etc/vz/conf/{$sData[0]}.conf /etc/vz/conf/{$sVPS->sContainerId}.conf;";
	$sCommandList .= "mv /vz/private/{$sData[0]} /vz/private/{$sVPS->sContainerId};";
	$sCommandList .= "mv /vz/root/{$sData[0]} /vz/root/{$sVPS->sContainerId};";
	$sCommandList .= "vzctl restore {$sVPS->sContainerId} --dumpfile /tmp/Dump.{$sData[0]};";
	$sUpdate = $sServerConnect->exec($sCommandList);
	$sStatus = $sServerConnect->exec("vzctl status {$sVPS->sContainerId};");
	if(strpos($sStatus, 'running') !== false) {
		echo "VPS Old: {$sData[0]} now Feathurized: {$sVPS->sId}\n";
	} else {
		echo "Something borked with the conversion of {$sData[0]} ({$sData[1]} / {$sData[2]}), skipping...\n";
	}
}
echo "To the best of this script's ability this conversion is completed.\n";