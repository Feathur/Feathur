<?php
// Check to make sure that this script isn't being executed remotely.
if(!(php_sapi_name() == 'cli')){
	die("Unfortunately this script must be executed via CLI.");
}

$sAdd = $database->prepare("UPDATE `vps` SET `bandwidth_usage` = 0");
$sAdd->execute();