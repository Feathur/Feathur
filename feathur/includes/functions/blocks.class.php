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
				
				if($sServerBlocks = $database->CachedQuery("SELECT * FROM server_blocks WHERE `block_id` = :BlockId AND `ipv6` = 0", array('BlockId' => $sBlock))){
					foreach($sServerBlocks->data as $sValue){
						$sServer = new Server($sValue["server_id"]);
						$sServerList[] = array("id" => $sValue["server_id"], "name" => $sServer->sName);
						unset($sServer);
					}
				}
	
				if($sServers = $database->CachedQuery("SELECT * FROM servers", array())){
					foreach($sServers->data as $sValue){
						$sServer = new Server($sValue["id"]);
						if(!$sServerBlockCheck = $database->CachedQuery("SELECT * FROM server_blocks WHERE `server_id` = :ServerId AND `block_id` = :BlockId AND `ipv6` = 0", array('ServerId' => $sServer->sId, 'BlockId' => $sBlock))){
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
	
}
