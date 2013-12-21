<?php
if(!(php_sapi_name() == 'cli')){
	die("Unfortunately this script must be executed via CLI.");
}

error_reporting(E_ALL ^ E_NOTICE);
// Query: SELECT nname, vpsid, contactemail, syncserver, username, coma_vmipaddress_a, hostname FROM vps WHERE syncserver='s1.c12.ny.bluevm.com' && resourceplan_used LIKE '%blue1%';
$sUsername = "email";
$sPassword = "password";
$sServerId = 31;
$sDefaultTemplateId = 3;
$sAssignNewIP = 0;

set_time_limit(1800);
include('./includes/loader.php');

$sUser = User::login($sUsername, $sPassword, 1);
if(is_array($sUser)){
	echo $sUser["red"];
	die();
}

if(!file_exists("hypervm-array.php")) {
	die("Unfortunatly there is no data to import. (File missing)");
}

include('hypervm-array.php');

if(!is_array($vps)){
	die("Unfortunatly there is no data to import (Array missing/invalid)");
}

// Get server information
if($sServers = $database->CachedQuery("SELECT * FROM servers WHERE `id` = :ServerId", array(':ServerId' => $sServerId))){
	$sServerId = $sServers->data[0]["id"];
} else {
	echo "Unfortunatly no server matches your query.";
	die();
}

$sServer = new Server($sServerId);
$sTemplate = new Template($sDefaultTemplateId);

foreach($vps as $sVPS){

	if(!empty($sVPS["contactemail"])){
	
		// Get user information
		if($sCheckUsers = $database->CachedQuery("SELECT * FROM accounts WHERE `email_address` = :UserEmail", array(':UserEmail' => $sVPS["contactemail"]))){
			$sActionUser = new User($sCheckUsers->data[0]["id"]);
		} else {
			$sActionUser = User::generate_user($sVPS["contactemail"], $sVPS["nname"], 1);
			if(is_array($sActionUser)){
				echo $sVPS['nname']." Skipped (Account)\n";
				$sSkipped = 1;
			}
		}
		
		if(empty($sSkipped)){
			$sStart = new openvz;
			$sRequested["POST"]["server"] = $sServer->sId;
			$sRequested["POST"]["template"] = $sTemplate->sId;
			$sRequested["POST"]["user"] = $sActionUser->sId;
			$sRequested["POST"]["inodes"] = "200000";
			$sRequested["POST"]["ram"] = "256";
			$sRequested["POST"]["swap"] = "256";
			$sRequested["POST"]["disk"] = "10";
			$sRequested["POST"]["numproc"] = "128";
			$sRequested["POST"]["numiptent"] = "80";
			$sRequested["POST"]["ipaddresses"] = "0";
			$sRequested["POST"]["hostname"] = $sVPS["nname"];
			$sRequested["POST"]["nameserver"] = "8.8.8.8";
			$sRequested["POST"]["password"] = random_string(12);
			$sRequested["POST"]["cpuunits"] = "1000";
			$sRequested["POST"]["cpulimit"] = "100";
			$sRequested["POST"]["bandwidthlimit"] = "512";
			$sDatabase = $sStart->database_openvz_create($sUser, $sRequested);
			$sCreateVPS = $sStart->openvz_create($sUser, $sRequested);
			
			if(is_numeric($sCreateVPS["vps"])){
				$sNewVPS = new VPS($sCreateVPS["vps"]);
				$sIPs = substr($sVPS["coma_vmipaddress_a"], 1);
				$sIPs = explode(",", $sIPs);
				
				if(empty($sAssignNewIP)){
					$sAction = "assignip";
					$sDBAction = "database_{$sNewVPS->sType}_{$sAction}";
					$sServerAction = "{$sNewVPS->sType}_{$sAction}";
					foreach($sIPs as $sIP){
						if(empty($sFirst)){
							$sNewVPS->uPrimaryIP = $sIP;
							$sNewVPS->InsertIntoDatabase();
							$sFirst = 1;
						}
						if(!empty($sIP)){
							$sRequested["GET"]["ip"] = $sIP;
							$sDBResult = $sStart->$sDBAction($sUser, $sNewVPS, $sRequested);
							$sServerResult = $sStart->$sServerAction($sUser, $sNewVPS, $sRequested);
						}
					}
				} else {
					$sTotalIPs = count($sIPs);
					$sAction = "addip";
					$sDBAction = "database_{$sNewVPS->sType}_{$sAction}";
					$sServerAction = "{$sNewVPS->sType}_{$sAction}";
					$sRequested["GET"]["ip"] = $sTotalIPs;
					$sDBResult = $sStart->$sDBAction($sUser, $sNewVPS, $sRequested);
					$sServerResult = $sStart->$sServerAction($sUser, $sNewVPS, $sRequested);
				}
				
				unset($sRequested);
				
				$sShutdown = $sStart->openvz_shutdown($sUser, $sNewVPS, $sRequested);
				
				$sCommands .= 'screen -dm -S '.$sVPS["vpsid"].' bash -c \'vzctl stop '.$sVPS["vpsid"].';';
				$sCommands .= 'ssh root@'.$sServer->sIPAddress.' "vzctl stop '.$sNewVPS->sContainerId.';vzctl set '.$sNewVPS->sContainerId.' --disabled yes --save;rm -rf /vz/private/'.$sNewVPS->sContainerId.'/*;";';
				$sCommands .= 'cd /vz/private/'.$sVPS["vpsid"].'; tar cf - . --numeric-owner | pigz -9c | ssh root@'.$sServer->sIPAddress.' "pigz -dc | tar xpf - -C /vz/private/'.$sNewVPS->sContainerId.'/;";';
				$sCommands .= 'ssh root@'.$sServer->sIPAddress.' "vzctl set '.$sNewVPS->sContainerId.' --disabled no --save;vzctl start '.$sNewVPS->sContainerId.'";exit;\';';
			} else {
				var_dump($sCreateVPS);
			}
		}
	} else {
		echo $sVPS['nname']." skipped (Email).\n";
	}
	
	unset($sIgnore);
	unset($sSkipped);
	unset($sFirst);
}

echo $sCommands;
