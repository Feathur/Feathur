<?php
class ServerBlock extends CPHPDatabaseRecordClass {

	public $table_name = "server_blocks";
	public $id_field = "id";
	public $fill_query = "SELECT * FROM server_blocks WHERE `id` = :Id";
	public $verify_query = "SELECT * FROM server_blocks WHERE `id` = :Id";
	public $query_cache = 1;
	
	public $prototype = array(
		'numeric' => array(
			'ServerId' => "server_id",
			'BlockId' => "block_id",
			'IPv6' => "ipv6",
		)
	);
}
