<?php
include('./includes/loader.php');
$sServerId = "";
if(!empty($sServerId)){
	$sServer = new Server($sServerId);
	$sSSH = Server::server_connect($sServer);
	$sLVM = $sSSH->exec("cd /dev/{$sServer->sVolumeGroup}/;ls");
	echo $sLVM;
}