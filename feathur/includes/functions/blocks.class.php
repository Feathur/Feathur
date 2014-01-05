<?php
class Block extends CPHPDatabaseRecordClass {

	public $table_name = "blocks";
	public $id_field = "id";
	public $fill_query = "SELECT * FROM blocks WHERE `id` = :Id";
	public $verify_query = "SELECT * FROM blocks WHERE `id` = :Id";
	public $query_cache = 1;
	
	public $prototype = array(
		'string' => array(
			'Name' => "name",
			'Gateway' => "gateway",
			'Netmask' => "netmask",
			'Prefix' => "prefix",
			'Current' => "current",
			'Secondary' => "secondary",
		),
		'numeric' => array(
			'IPv6' => "ipv6",
			'PerUser' => "per_user",
		)
	);
	
	public static function block_list($sType){
		global $database;
		if($sBlocks = $database->CachedQuery("SELECT * FROM `blocks` WHERE `ipv6` = :Type", array("Type" => $sType))){
			foreach($sBlocks->data as $sValue){
				if($sType == 0){
					$sTotal = $database->CachedQuery("SELECT * FROM ipaddresses WHERE `block_id` = :BlockId", array('BlockId' => $sValue["id"]));
					$sUsed = $database->CachedQuery("SELECT * FROM ipaddresses WHERE `block_id` = :BlockId AND `vps_id` != 0", array('BlockId' => $sValue["id"]));
					$sBlockList[] = array("id" => $sValue["id"], "name" => $sValue["name"], "total" => count($sTotal->data), "used" => count($sUsed->data));
				} else {
					$sBlockList[] = array("id" => $sValue["id"], "name" => $sValue["name"]);
				}
			}
			return $sBlockList;
		}
		return false;
	}
	
	public static function list_pool($sType, $sPool){
		global $database;
		if($sBlocks = $database->CachedQuery("SELECT * FROM `blocks` WHERE `id` = :Pool", array("Pool" => $sPool))){
			if(empty($sType)){
				if($sIPs = $database->CachedQuery("SELECT * FROM ipaddresses WHERE `block_id` = :BlockId", array('BlockId' => $sPool))){
					foreach($sIPs->data as $sValue){
						if(!empty($sValue["vps_id"])){
							try {
								$sVPS = new VPS($sValue["vps_id"]);
								$sTempUser = new User($sVPS->sUserId);
							} catch (Exception $e) {
								$sVPS = 0;
								$sTempUser = 0;
							}
						}
						$sIPList[] = array("id" => $sValue["id"], "ip" => $sValue["ip_address"], "Owner" => $sTempUser->sUsername, "OwnerId" => $sTempUser->sId);
						unset($sTempUser);
						unset($sVPS);
					}
				}
				
				if($sServerBlocks = $database->CachedQuery("SELECT * FROM server_blocks WHERE `block_id` = :BlockId AND `ipv6` = 0", array('BlockId' => $sPool))){
					foreach($sServerBlocks->data as $sValue){
						$sServer = new Server($sValue["server_id"]);
						$sServerList[] = array("id" => $sValue["server_id"], "name" => $sServer->sName);
						unset($sServer);
					}
				}
	
				if($sServers = $database->CachedQuery("SELECT * FROM servers", array())){
					foreach($sServers->data as $sValue){
						$sServer = new Server($sValue["id"]);
						if(!$sServerBlockCheck = $database->CachedQuery("SELECT * FROM server_blocks WHERE `server_id` = :ServerId AND `block_id` = :BlockId AND `ipv6` = 0", array('ServerId' => $sServer->sId, 'BlockId' => $sPool))){
							$sAvailableServers[] = array("id" => $sServer->sId, "name" => $sServer->sName);
						}
						unset($sServer);
					}
				}
				
				return $sData = array("IPList" => $sIPList, "ServerList" => $sServerList, "AvailableServers" => $sAvailableServers, "BlockName" => $sBlocks->data[0]["name"]);
			} else {
				
			}
		}
	}
	
	public static function create_pool($sRequested){
		global $database;
		if(empty($sRequested["GET"]["type"])){
			if(!empty($sRequested["GET"]["name"])){
				$sNewBlock = new Block(0);
				$sNewBlock->uName = $sRequested["GET"]["name"];
				$sNewBlock->uGateway = $sRequested["GET"]["gateway"];
				$sNewBlock->uNetmask = $sRequested["GET"]["netmask"];
				$sNewBlock->InsertIntoDatabase();
				return $sSuccess = array("content" => "The block {$sRequested["GET"]["name"]} has been created.");
			} else {
				return $sError = array("red" => "You must give each block a name.");
			}
		} else {
		
		}
	}
	
	public static function delete_pool($sRequested){
		global $database;
		if(empty($sRequested["GET"]["type"])){
			if(!empty($sRequested["GET"]["id"])){
				if(!$sServers = $database->CachedQuery("SELECT * FROM `ipaddresses` WHERE `block_id` = :BlockId AND `vps_id` != 0", array("BlockId" => $sRequested["GET"]["id"]))){
					$sDeleteIPs = $database->CachedQuery("DELETE FROM `ipaddresses` WHERE `block_id` = :BlockId", array("BlockId" => $sRequested["GET"]["id"]));
					$sDeleteBlock = $database->CachedQuery("DELETE FROM `blocks` WHERE `id` = :Id", array("Id" => $sRequested["GET"]["id"]));
					return $sSuccess = array("content" => "The block has been deleted.");
				} else {
					return $sError = array("red" => "You can not delete a block with IPs assigned to VPS.");
				}
			} else {
				return $sError = array("red" => "You must specify a pool to delete.");
			}
		} else {
		
		}
	}
	
	public static function rename_pool($sRequested){
		global $database;
		if(!empty($sRequested["GET"]["id"])){
			$sBlock = new Block($sRequested["GET"]["id"]);
			$sBlock->uName = $sRequested["GET"]["name"];
			$sBlock->InsertIntoDatabase();
			return $sSuccess = array("content" => "The block has been updated.");
		} else {
			return $sError = array("red" => "You must specify a block to edit.");
		}
	}
	
	public static function add_ipv4($sRequested){
		global $database;
		if(filter_var($sRequested["GET"]["ip"], FILTER_VALIDATE_IP)) {
			if(is_numeric($sRequested["GET"]["pool"])){
				if(!$sIPExists = $database->CachedQuery("SELECT * FROM `ipaddresses` WHERE `ip_address` = :IP", array('IP' => $sRequested["GET"]["ip"]))){
					$sIP = new IP(0);
					$sIP->uIPAddress = $sRequested["GET"]["ip"];
					$sIP->uVPSId = 0;
					$sIP->uBlockId = $sRequested["GET"]["pool"];
					$sIP->InsertIntoDatabase();
					return $sSuccess = array("content" => "The IP has been added to the pool.");
				} else {
					return $sError = array("red" => "The IP you entered is already in the system!");
				}
			} else {
				return $sError = array("red" => "Invalid block. Please reload and try again!");
			}
		} else {
			return $sError = array("red" => "You must specify a valid range or IP!");
		}
	}
	
	public static function add_ipv4_range($sRequested){
		global $database;
		if((filter_var($sRequested["GET"]["start"], FILTER_VALIDATE_IP)) && (filter_var($sRequested["GET"]["end"], FILTER_VALIDATE_IP))){
			if(is_numeric($sRequested["GET"]["pool"])){
				$sStart = ip2long($sRequested["GET"]["start"]);
				$sEnd = ip2long($sRequested["GET"]["end"]);
				$sTotalIPs = $sEnd - $sStart;
				if(($sTotalIPs < 256) && ($sTotalIPs > 0)){
					$sCurrent = $sStart;
					while($sCurrent <= $sEnd){
						if(!$sIPExists = $database->CachedQuery("SELECT * FROM `ipaddresses` WHERE `ip_address` = :IP", array('IP' => long2ip($sCurrent)))){
							$sIP = new IP(0);
							$sIP->uIPAddress = long2ip($sCurrent);
							$sIP->uVPSId = 0;
							$sIP->uBlockId = $sRequested["GET"]["pool"];
							$sIP->InsertIntoDatabase();
							unset($sIP);
						}
						$sCurrent++;
					}
					return $sSuccess = array("content" => "The IPs have been added to the pool.");
				} else {
					return $sError = array("red" => "You must specify a valid range or IP!");
				}
			} else {
				return $sError = array("red" => "Invalid block. Please reload and try again!");
			}
		} else {
			return $sError = array("red" => "You must specify a valid range or IP!");
		}
	}
	
	public static function remove_ipv4($sRequested){
		global $database;
		if(is_numeric($sRequested["GET"]["id"])){
			$sIPRemoval = $database->CachedQuery("DELETE FROM `ipaddresses` WHERE `id` = :Id", array('Id' => $sRequested["GET"]["id"]));
			return $sSuccess = array("content" => "The IP has been removed from the pool.");
		} else {
			return $sError = array("red" => "The IP you attempted to remove is invalid.");
		}
	}
	
	public static function remove_server($sRequested){
		global $database;
		if(is_numeric($sRequested["GET"]["id"])){
			if(is_numeric($sRequested["GET"]["pool"])){
				if($sServerLookup = $database->CachedQuery("SELECT * FROM `server_blocks` WHERE `server_id` = :ServerId AND `block_id` = :BlockId", array('ServerId' => $sRequested["GET"]["id"], 'BlockId' => $sRequested["GET"]["pool"]))){
					$sCleanUp = $database->CachedQuery("DELETE FROM `server_blocks` WHERE `server_id` = :ServerId AND `block_id` = :BlockId", array('ServerId' => $sRequested["GET"]["id"], 'BlockId' => $sRequested["GET"]["pool"]));
					return $sSuccess = array("content" => "The server has been removed from the pool.");
				} else {
					return $sError = array("red" => "The server or block you entered is invalid.");
				}
			} else {
				return $sError = array("red" => "The block you entered is invalid.");
			}
		} else {
			return $sError = array("red" => "The server you selected is invalid.");
		}
	}
	
	public static function add_server($sRequested){
		global $database;
		if(is_numeric($sRequested["GET"]["id"])){
			if(is_numeric($sRequested["GET"]["pool"])){
				if($sServerLookup = $database->CachedQuery("SELECT * FROM `servers` WHERE `id` = :Id", array('Id' => $sRequested["GET"]["id"]))){
					$sServer = new Server($sRequested["GET"]["id"]);
					$sServerBlock = new ServerBlock(0);
					$sServerBlock->sServerId = $sServer->sId;
					$sServerBlock->sBlockId = $sRequested["GET"]["pool"];
					$sServerBlock->InsertIntoDatabase();
					return $sSuccess = array("content" => "The server has been added to the pool.");
				} else {
					return $sError = array("red" => "The server you selected is invalid.");
				}
			} else {
				return $sError = array("red" => "The block you entered is invalid.");
			}
		} else {
			return $sError = array("red" => "The server you selected is invalid.");
		}
	}
}
