<?php
class Transfer extends CPHPDatabaseRecordClass {

	public $table_name = "transfers";
	public $id_field = "id";
	public $fill_query = "SELECT * FROM transfers WHERE `id` = :Id";
	public $verify_query = "SELECT * FROM transfers WHERE `id` = :Id";
	public $query_cache = 1;
	
	public $prototype = array(
		'numeric' => array(
			'VPSId' => "vps_id",
			'FromServer' => "from_server",
			'ToServer' => "to_server",
			'Phase' => "phase",
			'Completed' => "completed",
			'Cleanup' => "cleanup",
		),
	);
	
}