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
			'SecondaryDrive' => "secondary_drive",
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
			'IPv6' => "ipv6",
			'SMTPWhitelist' => "smtp_whitelist",
			'ISOSyncing' => "iso_syncing",
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
		if($sList = $database->CachedQuery("SELECT * FROM templates WHERE `type` = :Type AND `disabled` = 0", array('Type' => $sVPS->sType))){
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
		$sBlocks = $database->CachedQuery("SELECT * FROM server_blocks WHERE `server_id` = :ServerId AND `ipv6` = 0", array('ServerId' => $sServer));
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
	
	public static function add_template($uName, $uURL, $uType){
		global $database;
		$sTypes = array(".iso", ".tar.gz", ".tar.xz");
		if(filter_var($uURL, FILTER_VALIDATE_URL) === FALSE) {
			return $sError = array("red" => "Invalid URL for the template to download");
		}
		
		// Attempt to get data about the template.
		try {
			$sTemplateData = array_change_key_case(get_headers($uURL, TRUE));
			if((!isset($sTemplateData['content-length'])) || (empty($sTemplateData['content-length']))){
				throw new Exception("ISO Invalid");
			}
		} catch (Exception $e) {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Template/ISO URL is invalid or down.");
		}
		
		if(is_array($sTemplateData["content-length"])){
			foreach($sTemplateData["content-length"] as $sValue){
				$sTotal = $sTotal + $sValue;
			}
			$sTemplateData["content-length"] = $sTotal;
		}
		
		// Make sure the template is at least 10 MB.
		if($sTemplateData["content-length"] > 10485760){
		
			// Get the download file data and make sure it's valid.
			$sData = parse_url($uURL);
			$sPath = preg_replace("/[^a-z0-9_.-]+/i", "", basename($sData["path"]));
			if($sPathSearch = $database->CachedQuery("SELECT * FROM templates WHERE `path` = :Path", array('Path' => $sPath))){
			
				if($uType == 'openvz'){
					while($sUnique == 0){
						foreach($sTypes as $sValue){
							if(strpos($sPath, $sValue) !== false) {
								if(!empty($sEnd)){
									return $sArray = array("json" => 1, "type" => "error", "result" => "Template/ISO URL is invalid.");
								}
								$sEnd = $sValue;
								$sPath = str_replace($sValue, '', $sPath);
							}
						}
						
						if(empty($sEnd)){
							return $sArray = array("json" => 1, "type" => "error", "result" => "Template/ISO URL is invalid.");
						}
						
						$sPath = $sPath.'-'.str_pad(rand(1, 10000), 5, '0', STR_PAD_LEFT).$sEnd;
						if(!$sPathSearch = $database->CachedQuery("SELECT * FROM templates WHERE `path` = :Path", array('Path' => $sPath))){
							$sUnique = 1;
						}
					}
				}
				
				if($uType == 'kvm'){
					while($sUnique == 0){
						$sPath = str_pad(rand(1, 1000000000), 10, '0', STR_PAD_LEFT).'.iso';
						if(!$sPathSearch = $database->CachedQuery("SELECT * FROM templates WHERE `path` = :Path", array('Path' => $sPath))){
							$sUnique = 1;
						}
					}
				}
			}
			
			// Save to database.
			$sTemplate = new Template(0);
			$sTemplate->uName = preg_replace("/[^a-z0-9_ .-]+/i", "", $uName);
			$sTemplate->uURL = $uURL;
			$sTemplate->uType = $uType;
			$sTemplate->uPath = $sPath;
			$sTemplate->uSize = $sTemplateData["content-length"];
			$sTemplate->uDisabled = 0;
			$sTemplate->InsertIntoDatabase();
			return $sArray = array("json" => 1, "type" => "success", "result" => "Template/ISO added.", "reload" => "1");
		}
		
		return $sArray = array("json" => 1, "type" => "error", "result" => "Template/ISO URL is invalid or down.");
	}
	
	public static function remove_template($sLocalSSH, $uId){
		global $database;
		if(is_numeric($uId)){
			$sTemplate = new Template($uId);
			$sClean = $database->CachedQuery("DELETE FROM templates WHERE `id` = :Id", array('Id' => $sTemplate->sId));
			return $sArray = array("json" => 1, "type" => "success", "result" => "Template/ISO has been deleted.", "reload" => "1");
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "There is no template matching that id.");
		}
	}
}