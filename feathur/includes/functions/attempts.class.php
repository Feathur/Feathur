<?php
class Attempt extends CPHPDatabaseRecordClass {

	public $table_name = "attempts";
	public $id_field = "id";
	public $fill_query = "SELECT * FROM attempts WHERE `id` = :Id";
	public $verify_query = "SELECT * FROM attempts WHERE `id` = :Id";
	public $query_cache = 1;
	
	public $prototype = array(
		'numeric' => array(
			"Timestamp" => "timestamp",
		),
		'string' => array(
			'IPAddress' => "ip_address",
			'Type' => "type",
		),
	);
}