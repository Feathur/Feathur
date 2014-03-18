<?php
class UserIPv6Block extends CPHPDatabaseRecordClass {

	public $table_name = "useripv6blocks";
	public $id_field = "id";
	public $fill_query = "SELECT * FROM useripv6blocks WHERE `id` = :Id";
	public $verify_query = "SELECT * FROM useripv6blocks WHERE `id` = :Id";
	public $query_cache = 1;
	
	public $prototype = array(
		'string' => array(
			'UserBlock' => "user_block",
			'Current' => "current",
		),
		'numeric' => array(
			'VPSId' => "vps_id",
			'BlockId' => "block_id",
		)
	);

}
