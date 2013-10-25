<?php
// Settings
// Administrative login information
$sEmail = "";
$sPassword = "";

// Default template id if OpenVZ. (Usually 1, but check database to be sure.)
$sDefaultTemplate = "1";

// Converter Type allows for three types of conversions:
// 1 = Convert a single node to Feathur. (Will create an account for each user and add their VPS to it.)
// 2 = Convert all users so that they have an account on Feathur, but move no VPS yet.
$sConverterType = "";

// If conversion type equals 1, update the variable bellow to specify which node to convert.
$sNodeId = "";

// Only change this if you want to convert just one VPS from the node above. (EG: testing, missed one, etc...)
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

function split_crap($sVariable){
	$sVariable = explode(":", $sVariable);
	return $sVariable[0];
}

if(!$sSolusVM = $database->CachedQuery("SHOW DATABASES LIKE 'solusvm'", array())){
	die("There is no solusvm database, cannot convert.\n");
}

if(empty($sConverterType)){
	die("You must specify a converter type in the settings of this script.\n");
}

if(($sConverterType == 1) && (empty($sNodeId))){
	die("If you are trying to convert one node you must tell Feathur which node you want to convert (sNodeId) in the settings of this script.\n");
}

if($sConverterType == 1){
	if($sSolusNodes = $database->CachedQuery("SELECT * FROM `solusvm`.`nodes` WHERE `nodeid` = :NodeId", array("NodeId" => $sNodeId))){
		$sSolusNode = $sSolusNodes->data[0];
		echo "Starting conversion process for node: {$sSolusNode["name"]}\n";
		if($sFeathurNode = $database->CachedQuery("SELECT * FROM `servers` WHERE (`ip_address` = :SolusIP || `ip_address` = :SolusHostname)", array("SolusIP" => $sSolusNode["ip"], "SolusHostname" => $sSolusNode["hostname"]))){
			echo "There is a node in Feathur that matches the SolusVM hostname/ip of the server you're trying to convert... Awesome!\n";
			$sServer = new Server($sFeathurNode->data[0]["id"]);
			if(!empty($sSingleVPS)){
				$sAdditional = "&& `vserverid` = '{$sSingleVPS}'";
			}
			
			if($sListVPS = $database->CachedQuery("SELECT * FROM `solusvm`.`vservers` WHERE `nodeid` = :NodeId {$sAdditional}", array("NodeId" => $sSolusNode["nodeid"]))){
				$sCountVPS = count($sListVPS->data);
				echo "Found {$sCountVPS} VPS to convert.... beginning conversion in 5 seconds.\n";
				sleep(5);
				
				
				foreach($sListVPS->data as $sValue){
					echo "Starting conversion of VPS ID: {$sValue["vserverid"]} ({$sValue["hostname"]} / {$sValue["mainipaddress"]})\n";
					
					if($sSolusNode["type"] == 'kvm'){
						if($sSolusUser = $database->CachedQuery("SELECT * FROM `solusvm`.`clients` WHERE `clientid` = :ClientId", array("ClientId" => $sValue["clientid"]))){
							$sSolusUser = $sSolusUser->data[0];
							if($sKVMData = $database->CachedQuery("SELECT * FROM `solusvm`.`kvmdata` WHERE `vserverid` = :VServerId", array("VServerId" => $sValue["vserverid"]))){
								$sKVMData = $sKVMData->data[0];
								if($sFeathurUser = $database->CachedQuery("SELECT * FROM `accounts` WHERE `email_address` = :ClientEmail", array("ClientEmail" => $sSolusUser["emailaddress"]))){
									$sVPSUser = new User($sFeathurUser->data[0]["id"]);
									echo "Found existing Feathur user for VPS ID: {$sValue["vserverid"]} ({$sValue["hostname"]} / {$sValue["mainipaddress"]}), placing this VPS under that account...\n";
								} else {
									$sVPSUser = User::generate_user($sSolusUser["emailaddress"], $sSolusUser["username"], 1);
									echo "Created Feathur account for user of VPS ID: {$sValue["vserverid"]} ({$sValue["hostname"]} / {$sValue["mainipaddress"]}), placing this VPS under that account...\n";
								}
								
								echo "Inserting VPS into database...\n";
								$sRequested["POST"]["server"] = $sServer->sId;
								$sRequested["POST"]["user"] = $sVPSUser->sId;
								$sRequested["POST"]["ram"] = (($sValue["ram"] / 1024) / 1024);
								$sRequested["POST"]["disk"] = ((($sValue["disk"] / 1024) / 1024) / 1024);
								$sRequested["POST"]["ipaddresses"] = 0;
								$sRequested["POST"]["hostname"] = $sValue["hostname"];
								$sRequested["POST"]["nameserver"] = "8.8.8.8";
								$sRequested["POST"]["cpulimit"] = $sKVMData["vcpu"];
								$sRequested["POST"]["bandwidthlimit"] = ((($sValue["bandwidth"] / 1024) / 1024) / 1024);
								$sServerType = new $sServer->sType;
								$sMethod = "database_{$sServer->sType}_create";
								$sVPSId = $sServerType::$sMethod($sUser, $sRequested, 1);
								
								if(is_array($sVPSId)){
									var_dump($sVPSId);
									die();
								}
								
								$sVPS = new VPS($sVPSId);
								echo "New VPS with ID: {$sVPSId}\n";
								
								echo "Beginning IP assignment.\n";
								if($sIPData = $database->CachedQuery("SELECT * FROM `solusvm`.`ipaddresses` WHERE `vserverid` = :VServerId", array("VServerId" => $sValue["vserverid"]))){
									foreach($sIPData->data as $sIP){
										if($sSolusBlockData = $database->CachedQuery("SELECT * FROM `solusvm`.`ipblocks` WHERE `blockid` = :BlockId", array("BlockId" => $sIP["blockid"]))){
											$sSolusBlockData = $sSolusBlockData->data[0];
											if(!$sFeathurBlockData = $database->CachedQuery("SELECT * FROM blocks WHERE `gateway` = :Gateway", array("Gateway" => $sSolusBlockData["gateway"]))){
												echo "Adding block because one doesn't exist for this range.\n";
												$sBlock = new Block(0);
												$sBlock->uName = $sSolusBlockData["name"];
												$sBlock->uGateway = $sSolusBlockData["gateway"];
												$sBlock->uNetmask = $sSolusBlockData["mask"];
												$sBlock->InsertIntoDatabase();
											} else {
												echo "Using existing block for IPs\n";
												$sBlock = new Block($sFeathurBlockData->data["0"]["id"]);
											}
										}
										
										if(!$sIPExists = $database->CachedQuery("SELECT * FROM `ipaddresses` WHERE `ip_address` = :IP", array('IP' => $sIP["ipaddress"]))){
											echo "Adding IP to database.\n";
											$sIPAdd = new IP(0);
											$sIPAdd->uIPAddress = $sIP["ipaddress"];
											$sIPAdd->uVPSId = $sVPS->sId;
											$sIPAdd->uBlockId = $sBlock->sId;
											$sIPAdd->InsertIntoDatabase();
											
										} else {
											echo "Using existing IP.\n";
											$sIPAdd = new IP($sIPExists->data[0]["id"]);
											$sIPAdd->uVPSId = $sVPS->sId;
											$sIPAdd->InsertIntoDatabase();
										}
										$sIPTotal++;
									}
								}
								
								$sVPS->uPrimaryIP = $sValue["mainipaddress"];
								$sVPS->uMac = $sKVMData["mac"];
								$sVPS->InsertIntoDatabase();
								
								$sTotalMacs = 1;
								$sMac = $sVPS->sMac;
								while($sTotalMacs < $sIPTotal){
									$sMac .= ",".generate_mac();
									$sTotalMacs++;
								}
								
								if($sMac != $sVPS->sMac){
									$sVPS->uMac = $sMac;
									$sVPS->InsertIntoDatabase();
								}
														
								echo "Generating new configs for VPS...\n";
								$sCreateConfig = $sServerType->kvm_config($sUser, $sVPS, $sRequested);
								$sCreateDHCP = $sServerType->kvm_dhcp($sUser, $sVPS, $sRequested);
								
								echo "Shutting down VPS under SolusVM's control, renaming LV...\n";
								$sShutdown = $sServerConnect->exec("virsh destroy kvm{$sKVMData["xid"]};lvrename {$sServer->sVolumeGroup} kvm{$sKVMData["xid"]}_img kvm{$sVPS->sContainerId}_img;");
								
								echo "Starting up VPS under Feathur's control.\n";
								$sBoot = $sServerType->kvm_boot($sUser, $sVPS, $sRequested);
								echo $sBoot['result']."\n";
							} else {
								echo "Could not find VPS data in database for VPS ID: {$sValue["vserverid"]} ({$sValue["hostname"]} / {$sValue["mainipaddress"]}), skipping...\n";
							}
						} else {
							echo "Could not find SolusVM client for VPS ID: {$sValue["vserverid"]} ({$sValue["hostname"]} / {$sValue["mainipaddress"]}), skipping...\n";
						}
					}
					
					if($sSolusNode["type"] == 'openvz'){
						echo "Starting conversion of VPS ID: {$sValue["vserverid"]} ({$sValue["hostname"]} / {$sValue["mainipaddress"]})\n";
						
						if($sSolusUser = $database->CachedQuery("SELECT * FROM `solusvm`.`clients` WHERE `clientid` = :ClientId", array("ClientId" => $sValue["clientid"]))){
							$sSolusUser = $sSolusUser->data[0];
							if($sOpenVZData = $database->CachedQuery("SELECT * FROM `solusvm`.`vzdata` WHERE `vserverid` = :VServerId", array("VServerId" => $sValue["vserverid"]))){
								$sOpenVZData = $sOpenVZData->data[0];
								if($sFeathurUser = $database->CachedQuery("SELECT * FROM `accounts` WHERE `email_address` = :ClientEmail", array("ClientEmail" => $sSolusUser["emailaddress"]))){
									$sVPSUser = new User($sFeathurUser->data[0]["id"]);
									echo "Found existing Feathur user for VPS ID: {$sValue["vserverid"]} ({$sValue["hostname"]} / {$sValue["mainipaddress"]}), placing this VPS under that account...\n";
								} else {
									$sVPSUser = User::generate_user($sSolusUser["emailaddress"], $sSolusUser["username"], 1);
									echo "Created Feathur account for user of VPS ID: {$sValue["vserverid"]} ({$sValue["hostname"]} / {$sValue["mainipaddress"]}), placing this VPS under that account...\n";
								}
								
								echo "Inserting VPS into database...\n";
								$sRequested["POST"]["server"] = $sServer->sId;
								$sRequested["POST"]["user"] = $sVPSUser->sId;
								$sRequested["POST"]["template"] = $sDefaultTemplate;
								$sRequested["POST"]["ram"] = (($sValue["ram"] / 1024) / 1024);
								$sRequested["POST"]["swap"] = (($sValue["burst"] / 1024) / 1024);
								$sRequested["POST"]["disk"] = ((($sValue["disk"] / 1024) / 1024) / 1024);
								$sRequested["POST"]["inodes"] = split_crap($sOpenVZData["vz_diskinodes"]);
								$sRequested["POST"]["numproc"] = split_crap($sOpenVZData["vz_avnumproc"]);
								// SolusVM's setting for this isn't great so we'll make our own.
								$sRequested["POST"]["numiptent"] = "80";
								$sRequested["POST"]["ipaddresses"] = "";
								$sRequested["POST"]["hostname"] = $sValue["hostname"];
								$sRequested["POST"]["nameserver"] = "8.8.8.8";
								$sRequested["POST"]["cpuunits"] = split_crap($sOpenVZData["vz_cpuunits"]);
								$sRequested["POST"]["cpulimit"] = (split_crap($sOpenVZData["vz_cpus"]) * 100);
								$sRequested["POST"]["bandwidthlimit"] = ((($sValue["bandwidth"] / 1024) / 1024) / 1024);
								$sServerType = new $sServer->sType;
								$sMethod = "database_{$sServer->sType}_create";
								$sVPSId = $sServerType::$sMethod($sUser, $sRequested, 1);
								
								if(is_array($sVPSId)){
									var_dump($sVPSId);
									die();
								}
								
								$sVPS = new VPS($sVPSId);
								echo "New VPS with ID: {$sVPSId}\n";
								
								echo "Beginning IP assignment.\n";
								if($sIPData = $database->CachedQuery("SELECT * FROM `solusvm`.`ipaddresses` WHERE `vserverid` = :VServerId", array("VServerId" => $sValue["vserverid"]))){
									foreach($sIPData->data as $sIP){
										if($sSolusBlockData = $database->CachedQuery("SELECT * FROM `solusvm`.`ipblocks` WHERE `blockid` = :BlockId", array("BlockId" => $sIP["blockid"]))){
											$sSolusBlockData = $sSolusBlockData->data[0];
											if(!$sFeathurBlockData = $database->CachedQuery("SELECT * FROM blocks WHERE `gateway` = :Gateway", array("Gateway" => $sSolusBlockData["gateway"]))){
												echo "Adding block because one doesn't exist for this range.\n";
												$sBlock = new Block(0);
												$sBlock->uName = $sSolusBlockData["name"];
												$sBlock->uGateway = $sSolusBlockData["gateway"];
												$sBlock->uNetmask = $sSolusBlockData["mask"];
												$sBlock->InsertIntoDatabase();
											} else {
												echo "Using existing block for IPs\n";
												$sBlock = new Block($sFeathurBlockData->data["0"]["id"]);
											}
										}
										
										if(!$sIPExists = $database->CachedQuery("SELECT * FROM `ipaddresses` WHERE `ip_address` = :IP", array('IP' => $sIP["ipaddress"]))){
											echo "Adding IP to database.\n";
											$sIPAdd = new IP(0);
											$sIPAdd->uIPAddress = $sIP["ipaddress"];
											$sIPAdd->uVPSId = $sVPS->sId;
											$sIPAdd->uBlockId = $sBlock->sId;
											$sIPAdd->InsertIntoDatabase();
											
										} else {
											echo "Using existing IP.\n";
											$sIPAdd = new IP($sIPExists->data[0]["id"]);
											$sIPAdd->uVPSId = $sVPS->sId;
											$sIPAdd->InsertIntoDatabase();
										}
										$sIPTotal++;
									}
								}
								
								$sVPS->uPrimaryIP = $sValue["mainipaddress"];
								$sVPS->InsertIntoDatabase();
								
								echo "Moving VPS configs to new ID...\n";
								$sCommandList = "vzctl chkpnt {$sValue["ctid"]} --dumpfile /tmp/Dump.{$sValue["ctid"]};";
								$sCommandList .= "mv /etc/vz/conf/{$sValue["ctid"]}.conf /etc/vz/conf/{$sVPS->sContainerId}.conf;";
								$sCommandList .= "mv /vz/private/{$sValue["ctid"]} /vz/private/{$sVPS->sContainerId};";
								$sCommandList .= "mv /vz/root/{$sValue["ctid"]} /vz/root/{$sVPS->sContainerId};";
								$sCommandList .= "vzctl restore {$sVPS->sContainerId} --dumpfile /tmp/Dump.{$sValue["ctid"]};";
								$sServerConnect = $sServer->server_connect($sServer);
								$sUpdate = $sServerConnect->exec($sCommandList);
								
								$sStatus = $sSSH->exec("vzctl status {$sVPS->sContainerId};");
								if(strpos($sStatus, 'running') !== false) {
									echo "VPS Old: {$sValue["vserverid"]} now Feathurized: {$sVPS->sId}\n";
								} else {
									echo "Something borked with the conversion of {$sValue["vserverid"]} ({$sValue["hostname"]} / {$sValue["mainipaddress"]}), skipping...\n";
								}
							} else {
								echo "Could not find VPS data in database for VPS ID: {$sValue["vserverid"]} ({$sValue["hostname"]} / {$sValue["mainipaddress"]}), skipping...\n";
							}
						} else {
							echo "Could not find SolusVM client for VPS ID: {$sValue["vserverid"]} ({$sValue["hostname"]} / {$sValue["mainipaddress"]}), skipping...\n";
						}
					}
				}
				
				echo "To the best of this script's ability this conversion is completed.\n";
			} else {
				die("No VPS were found within the parameters to convert. Aborting...\n");
			}
		} else {
			die("There is no server in Feathur that matches the hostname/ip of the SolusVM server you specified.\n");
		}
	} else {
		die("There is no entry in the solus database for the server with the ID: {$sNodeId}.\n");
	}
}

if($sConverterType == 2){

}