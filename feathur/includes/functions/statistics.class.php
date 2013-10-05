<?php
class Statistics extends CPHPDatabaseRecordClass {

	public $table_name = "statistics";
	public $id_field = "id";
	public $fill_query = "SELECT * FROM statistics WHERE `id` = :Id";
	public $verify_query = "SELECT * FROM statistics WHERE `id` = :Id";
	public $query_cache = 1;
	
	public $prototype = array(
		'string' => array(
			"HardwareUptime" => "hardware_uptime",
			"TotalMemory" => "total_memory",
			"FreeMemory" => "free_memory",
			"LoadAverage" => "load_average",
			"HardDiskFree" => "hard_disk_free",
			"HardDiskTotal" => "hard_disk_total",
			"Bandwidth" => "bandwidth",
		),
		'numeric' => array(
			"ServerId" => "server_id",
			"Timestamp" => "timestamp",
		),
		'boolean' => array(
			"Status" => "status"
		),
	);
}