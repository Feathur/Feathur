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
		),
	);
}
