<?php
class IPv6 extends CPHPDatabaseRecordClass {

	public $table_name = "ipv6addresses";
	public $id_field = "id";
	public $fill_query = "SELECT * FROM ipv6addresses WHERE `id` = :Id";
	public $verify_query = "SELECT * FROM ipv6addresses WHERE `id` = :Id";
	public $query_cache = 1;
	
	public $prototype = array(
		'string' => array(
			'Suffix' => "suffix",
		),
		'numeric' => array(
			'VPSId' => "vps_id",
			'BlockId' => "block_id",
			'UserBlockId' => "userblock_id",
		)
	);
}
