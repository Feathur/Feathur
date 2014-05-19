<?php
include('./includes/loader.php');
$sServerId = "";
if(!empty($sServerId)){
	$sServer = new Server($sServerId);
	$sSSH = Server::server_connect($sServer);
	$sLVMs = explode(" ", $sSSH->exec("cd /dev/{$sServer->sVolumeGroup}/;ls"));
	foreach($sLVMs as $sLVM){
		$sLVM = preg_replace("/[^0-9]/","", $sLVM);
		if($sLVM > 0){
			if(!$sExists = $database->CachedQuery("SELECT * FROM `vps` WHERE `container_id` = :ContainerId", array('ContainerId' => $sLVM))){
				echo "virsh destroy {$sLVM};lvremove -f {$sServer->sVolumeGroup}/kvm{$sLVM}_img;";
			}
		}
	}
}