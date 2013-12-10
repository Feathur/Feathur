<?php
class VPS extends CPHPDatabaseRecordClass {

	public $table_name = "vps";
	public $id_field = "id";
	public $fill_query = "SELECT * FROM vps WHERE `id` = :Id";
	public $verify_query = "SELECT * FROM vps WHERE `id` = :Id";
	public $query_cache = 1;
	
	public $prototype = array(
		'string' => array(
			'Type' => "type",
			'Hostname' => "hostname",
			'PrimaryIP' => "primary_ip",
			'Nameserver' => "nameserver",
			'Mac' => "mac",
			'BootOrder' => "boot_order",
			'NetworkDriver' => "network_driver",
			'DiskDriver' => "disk_driver",
		),
		'numeric' => array(
			'UserId' => "user_id",
			'ContainerId' => "container_id",
			'ServerId' => "server_id",
			'RAM' => "ram",
			'SWAP' => "swap",
			'Disk' => "disk",
			'CPUUnits' => 'cpuunits',
			'CPULimit' => 'cpulimit',
			'NumIPTent' => "numiptent",
			'NumProc' => "numproc",
			'Inodes' => "inodes",
			'TemplateId' => "template_id",
			'Suspended' => "suspended",
			'SuspendingAdmin' => "suspending_admin",
			'BandwidthLimit' => "bandwidthlimit",
			'MacAddress' => "mac_address",
			'TunTap' => "tuntap",
			'PPP' => "ppp",
			'IPTables' => "iptables",
			'Rebuilding' => "rebuilding",
			'VNCPort' => "vnc_port",
			'PrivateNetwork' => "private_network",
			'BandwidthUsage' => "bandwidth_usage",
			'LastBandwidth' => "last_bandwidth",
		),
	);
	
	public static function localhost_connect(){
		global $cphp_config;
		$sSSH = new Net_SSH2('127.0.0.1');
		$sKey = new Crypt_RSA();
		$sKey->loadKey(file_get_contents($cphp_config->settings->rootkey));
		if($sSSH->login("root", $sKey)) {
			return $sSSH;
		} else {
			return $sErrors = array("Could not connect to the local host.");
		}
	}
	
	public static function save_vps_logs($sLogs, $sVPS){
		foreach($sLogs as $key => $value){
			$sLog = new VPSLogs(0);
			$sLog->uEntry = $value["result"];
			$sLog->uCommand = $value["command"];
			$sLog->uVPSId = $sVPS->sId;
			$sLog->uTimestamp = time();
			$sLog->InsertIntoDatabase();
		}
	}
	
	public static function list_ipspace($sVPS){
		global $database;
		if($sIPs = $database->CachedQuery("SELECT * FROM ipaddresses WHERE `vps_id` = :VPSId", array('VPSId' => $sVPS->sId))){
			foreach($sIPs->data as $key => $value){
				if($value["ip_address"] == $sVPS->sPrimaryIP){
					$sPrimary = 1;
				} else {
					$sPrimary = 0;
				}
				if(!empty($value["block_id"])){
					$sBlock = new Block($value["block_id"]);
					$sIPAddresses[] = array("id" => $value["id"], "ip" => $value["ip_address"], "primary" => $sPrimary, "block" => $value["block_id"], "gateway" => $sBlock->sGateway, "netmask" => $sBlock->sNetmask);
				} else {
					$sIPAddresses[] = array("id" => $value["id"], "ip" => $value["ip_address"], "primary" => $sPrimary, "block" => $value["block_id"], "gateway" => "N/A", "netmask" => "N/A");
				}
			}
			usort($sIPAddresses, 'SortPrimaryIP');
			return $sIPAddresses;
		}
	}
	
	public static function list_templates($sVPS){
		global $database;
		if($sList = $database->CachedQuery("SELECT * FROM templates WHERE `type` = :Type", array('Type' => $sVPS->sType))){
			foreach($sList->data as $key => $value){
				if($value["id"] == $sVPS->sTemplateId){
					$sPrimary = 1;
				} else {
					$sPrimary = 0;
				}
				$sTemplates[] = array("id" => $value["id"], "name" => $value["name"], "primary" => $sPrimary);
			}
		}
		return $sTemplates;
	}
	
	public static function list_servers($sVPS){
		global $database;
		$sList = $database->CachedQuery("SELECT * FROM servers WHERE `type` = :Type", array('Type' => $sVPS->sType));
		foreach($sList->data as $value){
			if($value["id"] == $sVPS->sServerId){
				$sCurrent = 1;
			} else {
				$sCurrent = 0;
			}
			$sServers[] = array("id" => $value["id"], "name" => $value["name"], "current" => $sCurrent);
		}
		return $sServers;
	}
	
	public static function array_servers(){
		global $database;
		$sServers = $database->CachedQuery("SELECT * FROM servers", array());
		foreach($sServers->data as $value){
			$sServerList[] = array("id" => $value["id"], "ip" => $value["ip_address"], "type" => $value["type"]);
		}
		return $sServerList;
	}
	
	public static function list_uservps($sVPS){
		global $database;
		if($sUserVPS = $database->CachedQuery("SELECT * FROM vps WHERE `user_id` = :UserId", array('UserId' => $sVPS->sUserId))){
			foreach($sUserVPS->data as $value){
				if($value["id"] == $sVPS->sId){
					$sThis = 1;
				} else {
					unset($sThis);
				}
				$sThisServer = new Server($value["server_id"]);
				$sUserVPSList[] = array("id" => $value["id"], "hostname" => $value["hostname"], "container_id" => $value["container_id"], "server" => $sThisServer->sName, "server_ip" => $sThisServer->sIPAddress,  "this" => $sThis);
			}
		}
		return $sUserVPSList;
	}
	
	public static function check_ipspace($sServer, $sMinimum){
		global $database;
		$sBlocks = $database->CachedQuery("SELECT * FROM server_blocks WHERE `server_id` = :ServerId", array('ServerId' => $sServer));
		if(!empty($sBlocks)){
			foreach($sBlocks->data as $key => $value){
				$sIPs = $database->CachedQuery("SELECT * FROM ipaddresses WHERE `block_id` = :BlockId AND `vps_id` = 0", array('BlockId' => $value["block_id"]));
				$sTotal = count($sIPs->data);
			}
			if($sTotal >= $sMinimum){
				return true;
			} else {
				return $sArray = array("json" => 1, "type" => "caution", "result" => "There are not enough IPs for this VPS!");
			}
		} else {
			return $sArray = array("json" => 1, "type" => "caution", "result" => "There are no blocks assigned to the selected server!");
		}
	}
	
	public static function add_template($sLocalSSH, $uName, $uURL, $uType){
		global $database;
		if(filter_var($uURL, FILTER_VALIDATE_URL) === FALSE) {
			return $sError = array("red" => "Invalid URL for the template to download");
		} else {
			if($uType == 'openvz'){
				$sList = ".tar.gz";
			} elseif($uType == 'kvm'){
				$sList = ".iso";
			}
			$sName = preg_replace("/[^a-z0-9._-\s]+/i", "", $uName);
			$sPath = str_replace($sList, "", basename($uURL));
			if($sExists = $database->CachedQuery("SELECT * FROM templates WHERE `path` LIKE :TemplatePath && `type` = :Type", array('TemplatePath' => "%".$sPath."%", 'Type' => $uType))){
				$sPath .= random_string(6);
			}
			$sTemplate = new Template(0);
			$sTemplate->uName = $sName;
			$sTemplate->uPath = $sPath;
			$sTemplate->uType = $uType;
			$sTemplate->InsertIntoDatabase();
			$sDownload = $sLocalSSH->exec("cd /var/feathur/data/templates/;mkdir {$sTemplate->sType};cd {$sTemplate->sType};wget_output=$(wget -O {$sTemplate->sPath}{$sList} \"{$uURL}\")");
			$sRandoCalrissian = random_string(12);
			$sCheckDownload = $sLocalSSH->exec("cd /var/feathur/data/templates/{$sTemplate->sType};if [ -f {$sTemplate->sPath}{$sList} ]; do echo \"{$sRandoCalrissian}\"; fi;if [ $(ls -l {$sTemplate->sPath}{$sList} | awk '{print $5}') == 0 ]; do echo {$sRandoCalrissian}; fi");
			if(strpos($sCheckDownload, $sRandoCalrissian) !== false) {
				return $sArray = array("json" => 1, "type" => "success", "result" => "Template added, should be syncing to the servers here shortly.", "reload" => "1");
			} else {
				$sClean = $database->CachedQuery("DELETE FROM templates WHERE `id` = :Id", array('Id' => $sTemplate->sId));
				$sData = $sLocalSSH->exec("cd /var/feathur/data/templates/{$sTemplate->sType};rm -rf {$sTemplate->sPath}{$sList};");
				return $sArray = array("json" => 1, "type" => "error", "result" => "There was an issue downloading the template/iso.");
			}
		}
	}
	
	public static function remove_template($sLocalSSH, $uId){
		global $database;
		if(is_numeric($uId)){
			$sTemplate = new Template($uId);
			
			if($sTemplate->sType == 'openvz'){
				$sList = ".tar.gz";
			} elseif($sTemplate->sType == 'kvm'){
				$sList = ".iso";
			}
			$sRemoveFile = $sLocalSSH->exec("cd /var/feathur/data/templates/{$sTemplate->sType};rm -rf {$sTemplate->sPath}{$sList};");
			$sClean = $database->CachedQuery("DELETE FROM templates WHERE `id` = :Id", array('Id' => $sTemplate->sId));
			return $sArray = array("json" => 1, "type" => "success", "result" => "Template/ISO has been deleted.", "reload" => "1");
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "There is no template matching that id.");
		}
	}
}