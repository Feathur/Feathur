<?php
class History extends CPHPDatabaseRecordClass {

	public $table_name = "history";
	public $id_field = "id";
	public $fill_query = "SELECT * FROM history WHERE `id` = :Id";
	public $verify_query = "SELECT * FROM history WHERE `id` = :Id";
	public $query_cache = 1;
	
	public $prototype = array(
		'numeric' => array(
			"ServerId" => "server_id",
			"Timestamp" => "timestamp",
		),
		'boolean' => array(
			"Status" => "status"
		),
	);
}