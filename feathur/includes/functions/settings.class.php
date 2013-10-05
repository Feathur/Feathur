<?php

class Setting extends CPHPDatabaseRecordClass {

	public $table_name = "settings";
	public $id_field = "id";
	public $fill_query = "SELECT * FROM settings WHERE `id` = :Id";
	public $verify_query = "SELECT * FROM settings WHERE `id` = :Id";
	public $query_cache = 1;
	
	public $prototype = array(
		'string' => array(
			'Name'		=> "setting_name",
			'Value'		=> "setting_value",
			'Group'		=> "setting_group"
		)
	);
}
