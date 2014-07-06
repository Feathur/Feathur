<?php
class IP extends CPHPDatabaseRecordClass {

	public $table_name = "ipaddresses";
	public $id_field = "id";
	public $fill_query = "SELECT * FROM ipaddresses WHERE `id` = :Id";
	public $verify_query = "SELECT * FROM ipaddresses WHERE `id` = :Id";
	public $query_cache = 1;
	
	public $prototype = array(
		'string' => array(
			'IPAddress' => "ip_address",
		),
		'numeric' => array(
			'VPSId' => "vps_id",
			'BlockId' => "block_id",
		)
	);
	
	public static function free_ipv4($sServer){
		global $database;
		if ($sBlockList = $database->CachedQuery("SELECT * FROM `server_blocks` WHERE `server_id` = :ServerId", array("ServerId" => $sServer->sId))) {
			foreach ($sBlockList->data as $sBlockRow) {
				if ($sIPList = $database->CachedQuery("SELECT * FROM `ipaddresses` WHERE `block_id` = :BlockId AND `vps_id` = :VPSId", array('BlockId' => $sBlockRow['block_id'], 'VPSId' => 0))) {
					$sIPCount = ($sIPCount + count($sIPList->data));
				}
			}
		} else {
			$sIPCount = '0';
		}
		
		return $sIPCount;
	}
}
