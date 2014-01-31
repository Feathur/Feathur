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
}
