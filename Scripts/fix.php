<?php
$_CPHP_CONFIG = "/var/feathur/data/config.json";
$_CPHP = true;
require("./cphp/base.php");

if(!$sRefresh = $database->CachedQuery("SELECT * FROM settings WHERE `setting_name` LIKE :Setting", array('Setting' => "refresh_time"))){
	$sAdd = $database->prepare("INSERT INTO settings(setting_name, setting_value, setting_group) VALUES('refresh_time', '10', 'site_settings')");
	$sAdd->execute();
}