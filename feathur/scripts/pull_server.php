<?php
// Check to make sure that this script isn't being executed remotely.
if(!(php_sapi_name() == 'cli')){
	die("Unfortunately this script must be executed via CLI.");
}

set_time_limit(60);
chdir('/var/feathur/feathur/');
include('./includes/loader.php');
error_reporting(E_ALL ^ E_NOTICE);

$sServer = $argv[1];
if(!empty($sServer)){
	$sPull = Pull:pull_status($sServer);
}

die();