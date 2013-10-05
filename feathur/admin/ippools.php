<?php
if($sUser->sPermissions != 7){
	die("Sorry you've accessed our system without permission");
}

$sPage = "ippools";
$sPageType = "settings";
$sBlock = $_GET['block'];

if($sAction == addblock){
	if(!empty($_GET['name'])){
		$sNewBlock = new Block(0);
		$sNewBlock->uName = $_GET['name'];
		$sNewBlock->uGateway = $_GET['gateway'];
		$sNewBlock->uNetmask = $_GET['netmask'];
		$sNewBlock->InsertIntoDatabase();
	} else {
		$sErrors[] = array("red" => "You must give each block a unique name!");
	}
	$sJson = 1;
}

if($sAction == removeblock){
	if((!empty($_GET['id'])) && (is_numeric($_GET['id']))){
		$sDeleteBlock = $database->CachedQuery("DELETE FROM blocks WHERE `id` = :Id", array('Id' => $_GET['id']));
	} else {
		$sErrors[] = array("red" => "You must select a valid block!");
	}
	$sJson = 1;
}

if($sAction == updateblock){
	if((!empty($_GET['id'])) && (is_numeric($_GET['id']))){
		$sUpdateBlock = new Block($_GET['id']);
		$sUpdateBlock->uName = $_GET['name'];
		$sUpdateBlock->InsertIntoDatabase();
	} else {
		$sErrors[] = array("red" => "You must select a valid block!");
	}
	$sJson = 1;
}

if($sAction == addip){
	if(!empty($_GET['ip'])){
		if(filter_var($_GET['ip'], FILTER_VALIDATE_IP)) {
			if(is_numeric($sBlock)){
				if(!$sIPExists = $database->CachedQuery("SELECT * FROM `ipaddresses` WHERE `ip_address` = :IP", array('IP' => $_GET['ip']))){
					$sIP = new IP(0);
					$sIP->uIPAddress = $_GET['ip'];
					$sIP->uVPSId = 0;
					$sIP->uBlockId = $sBlock;
					$sIP->InsertIntoDatabase();
				} else {
					$sErrors[] = array("red" => "The IP you entered is already in the system!");
				}
			} else {
				$sErrors[] = array("red" => "Invalid block. Please reload and try again!");
			}
		} else {
			$sErrors[] = array("red" => "You must specify a valid range or IP!");
		}
	} elseif((!empty($_GET['start'])) && (!empty($_GET['end']))){
		if((filter_var($_GET['start'], FILTER_VALIDATE_IP)) && (filter_var($_GET['end'], FILTER_VALIDATE_IP))){
			if(is_numeric($sBlock)){
				$sStart = ip2long($_GET['start']);
				$sEnd = ip2long($_GET['end']);
				$sTotalIPs = $sEnd - $sStart;
				if(($sTotalIPs < 256) && ($sTotalIPs > 0)){
					$sCurrent = $sStart;
					while($sCurrent <= $sEnd){
						if(!$sIPExists = $database->CachedQuery("SELECT * FROM `ipaddresses` WHERE `ip_address` = :IP", array('IP' => long2ip($sCurrent)))){
							$sIP = new IP(0);
							$sIP->uIPAddress = long2ip($sCurrent);
							$sIP->uVPSId = 0;
							$sIP->uBlockId = $sBlock;
							$sIP->InsertIntoDatabase();
							unset($sIP);
						} else {
							$sErrors[] = array("red" => "The IP: ".long2ip($sCurrent)." Is already in the system, skipping it...");
						}
						$sCurrent++;
					}	
				} else {
					$sErrors[] = array("red" => "You must specify a valid range or IP!");
				}
			} else {
				$sErrors[] = array("red" => "Invalid block. Please reload and try again!");
			}
		} else {
			$sErrors[] = array("red" => "You must specify a valid range or IP!");
		}
	} else {
		$sErrors[] = array("red" => "You must specify a valid range or IP!");
	}
	$sJson = 1;
}

if($sAction == removeip){
	if(is_numeric($_GET['id'])){
		$sIPRemoval = $database->CachedQuery("DELETE FROM `ipaddresses` WHERE `id` = :Id", array('Id' => $_GET['id']));
	} else {
		$sErrors[] = array("red" => "The IP you attempted to remove is invalid.");
	}
	$sJson = 1;
}

if($sAction == addserver){
	if(is_numeric($_GET['id'])){
		if(is_numeric($sBlock)){
			if($sServerLookup = $database->CachedQuery("SELECT * FROM `servers` WHERE `id` = :Id", array('Id' => $_GET['id']))){
				$sServer = new Server($_GET['id']);
				$sServerBlock = new ServerBlock(0);
				$sServerBlock->sServerId = $sServer->sId;
				$sServerBlock->sBlockId = $sBlock;
				$sServerBlock->InsertIntoDatabase();
			} else {
				$sErrors[] = array("red" => "The server you selected is invalid.");
			}
		} else {
			$sErrors[] = array("red" => "The block you entered is invalid.");
		}
	} else {
		$sErrors[] = array("red" => "The server you selected is invalid.");
	}
	$sJson = 1;
}

if($sAction == removeserver){
	if(is_numeric($_GET['id'])){
		if(is_numeric($sBlock)){
			if($sServerLookup = $database->CachedQuery("SELECT * FROM `server_blocks` WHERE `server_id` = :ServerId AND `block_id` = :BlockId", array('ServerId' => $_GET['id'], 'BlockId' => $sBlock))){
				$sCleanUp = $database->CachedQuery("DELETE FROM `server_blocks` WHERE `server_id` = :ServerId AND `block_id` = :BlockId", array('ServerId' => $_GET['id'], 'BlockId' => $sBlock));
			} else {
				$sErrors[] = array("red" => "The server or block you entered is invalid.");
			}
		} else {
			$sErrors[] = array("red" => "The block you entered is invalid.");
		}
	} else {
		$sErrors[] = array("red" => "The server you selected is invalid.");
	}
	$sJson = 1;
}

if(empty($sBlock)){
	if($sBlocks = $database->CachedQuery("SELECT * FROM blocks", array())){
		foreach($sBlocks->data as $sValue){
			$sTotal = $database->CachedQuery("SELECT * FROM ipaddresses WHERE `block_id` = :BlockId", array('BlockId' => $sValue["id"]));
			$sUsed = $database->CachedQuery("SELECT * FROM ipaddresses WHERE `block_id` = :BlockId AND `vps_id` != 0", array('BlockId' => $sValue["id"]));
			$sBlockList[] = array("id" => $sValue["id"], "name" => $sValue["name"], "total" => count($sTotal->data), "used" => count($sUsed->data));
		}
	}
	$sContent = Templater::AdvancedParse($sAdminTemplate->sValue.'/blocks', $locale->strings, array("BlockList" => $sBlockList, "Errors" => $sErrors));
} elseif(is_numeric($sBlock)){
	if($sIPs = $database->CachedQuery("SELECT * FROM ipaddresses WHERE `block_id` = :BlockId", array('BlockId' => $sBlock))){
		foreach($sIPs->data as $sValue){
			if(!empty($sValue["vps_id"])){
				$sVPS = new VPS($sValue["vps_id"]);
				$sTempUser = new User($sVPS->sUserId);
			}
			$sIPList[] = array("id" => $sValue["id"], "ip" => $sValue["ip_address"], "Owner" => $sTempUser->sUsername, "OwnerId" => $sTempUser->sId);
			unset($sTempUser);
			unset($sVPS);
		}
	}
	
	if($sServerBlocks = $database->CachedQuery("SELECT * FROM server_blocks WHERE `block_id` = :BlockId", array('BlockId' => $sBlock))){
		foreach($sServerBlocks->data as $sValue){
			$sServer = new Server($sValue["server_id"]);
			$sServerList[] = array("id" => $sValue["server_id"], "name" => $sServer->sName);
			unset($sServer);
		}
	}
	
	if($sServers = $database->CachedQuery("SELECT * FROM servers", array())){
		foreach($sServers->data as $sValue){
			$sServer = new Server($sValue["id"]);
			if(!$sServerBlockCheck = $database->CachedQuery("SELECT * FROM server_blocks WHERE `server_id` = :ServerId AND `block_id` = :BlockId", array('ServerId' => $sServer->sId, 'BlockId' => $sBlock))){
				$sAvailableServers[] = array("id" => $sServer->sId, "name" => $sServer->sName);
			}
			unset($sServer);
		}
	}
	
	$sContent .= Templater::AdvancedParse($sAdminTemplate->sValue.'/manageblock', $locale->strings, array("IPList" => $sIPList, "ServerList" => $sServerList, "BlockId" => $sBlock, "AvailableServers" => $sAvailableServers));
	$sJson = 1;
} else {
	$sContent = "Invalid Block";
}

if($sJson == 1){
	echo json_encode(array("content" => $sContent));
	die();
}