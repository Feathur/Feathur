<?php
class SMTP extends CPHPDatabaseRecordClass {

	public $table_name = "smtp";
	public $id_field = "id";
	public $fill_query = "SELECT * FROM smtp WHERE `id` = :Id";
	public $verify_query = "SELECT * FROM smtp WHERE `id` = :Id";
	public $query_cache = 1;
	
	public $prototype = array(
		'numeric' => array(
			"VPSId" => "vps_id",
			"Timestamp" => "timestamp",
			"Connections" => "connections",
		),
	);
}