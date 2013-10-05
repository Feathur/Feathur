<?php
if($sUser->sPermissions != 7){
	die("Sorry you've accessed our system without permission");
}

$sPage = "addserver";
$sPageType = "servers";

if($sAction == submitserver){
	$sAddServer = Server::server_add($_POST['name'], $_POST['hostname'], $_POST['username'], $_POST['key'], $_POST['type'], $_POST['status'], $_POST['location']);
	if(is_array($sAddServer)){
		$sErrors[] = $sAddServer;
	}
}

$sContent = Templater::AdvancedParse($sAdminTemplate->sValue.'/addserver', $locale->strings, array());