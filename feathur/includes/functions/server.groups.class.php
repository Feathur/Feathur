<?php
class ServerGroups extends CPHPDatabaseRecordClass {

	public $table_name = "server_groups";
	public $id_field = "id";
	public $fill_query = "SELECT * FROM server_groups WHERE `id` = :Id";
	public $verify_query = "SELECT * FROM server_groups WHERE `id` = :Id";
	public $query_cache = 1;
	
	public $prototype = array(
		'numeric' => array(
			'ServerId' => "server_id",
			'GroupId' => "group_id",
		),
	);
	
	public static function select_server($uGroupId){
		global $database;
			try {
				$sGroup = new Group($uGroupId);
			} catch(Exception $e){
				die(json_encode(array('result' => 'Group selection was invalid.')));
			}
			
		// Look up servers in this group.
		if($sServers = $database->CachedQuery('SELECT * FROM server_groups WHERE `group_id` = :GroupId', array(':GroupId' => $sGroup->sId))){
			foreach($sServers->data as $sServer){
				$sServer = new Server($sServer["id"]);
				$sIPCount = IP::free_ipv4($sServer);
				
				// If free IPv4 is greater than zero and free disk is greater than 20% add server to list.
				if($sIPCount > 0){
					$sFreeDisk = round(((100 / $sServer->sHardDiskTotal) * ($sServer->sHardDiskFree)));
					if($sFreeDisk > 20){
						$sVPS = $database->CachedQuery('SELECT * FROM vps WHERE `server_id` = :ServerId', array(':ServerId' => $sServer->sId));
						$sVPSCount = count($sVPS->data);
						
						// Score determines which server should get the VPS.
						// Score is IPCount + numeric % of FreeDisk + a random number between 1 and 30 minus the current number of VPS on a node.
						// This way -in theory- VPS will be placed on random servers and will be balanced out over time.
						$sScore = round((((($sIPCount + $sFreeDisk) + rand(1, 30)) - $sVPSCount)));
						$sServerList[$sServer->sId] = $sScore;
					}
				}
				unset($sIPCount);
				unset($sVPS);
				unset($sVPSCount);
				unset($sScore);
			}
			
			if(isset($sServerList)){
				arsort($sServerList);
				return current(array_keys($sServerList));
			}
		}
		die(json_encode(array('result' => 'No servers were found in the group you selected.')));
	}
	
	
}