<?php
// This tool will remove all excess LVMs on any servers under Feathur's control.
// Be careful when using this tool as undocumented VMs will be destroyed.
if(php_sapi_name() != 'cli') die('Unfortunately this script must be executed via CLI.');
@set_time_limit(6000);
@ini_set('memory_limit','512M');

include('./includes/loader.php');

if($sServerList = $database->CachedQuery("SELECT * FROM `servers` WHERE `type` = :Type", array('Type' => "kvm"))){
	foreach($sServerList->data as $sServer){
		$sServer = new Server($sServer["id"]);
		$sSSH = Server::server_connect($sServer);
		$sLVMs = explode("\n", $sSSH->exec("cd /dev/{$sServer->sVolumeGroup}/;ls"));
		foreach($sLVMs as $sLVM){
			$sLVM = preg_replace("/[^0-9]/","", $sLVM);
			if($sLVM > 0){
				if(!$sExists = $database->CachedQuery("SELECT * FROM `vps` WHERE `container_id` = :ContainerId", array('ContainerId' => $sLVM))){
					echo "Removing {$sLVM} as it no longer exists.\n";
					$sSSH->exec("virsh destroy kvm{$sLVM};lvremove -f {$sServer->sVolumeGroup}/kvm{$sLVM}_img;");
				}
			}
		}
	}
}