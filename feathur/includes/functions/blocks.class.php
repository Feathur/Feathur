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
}
