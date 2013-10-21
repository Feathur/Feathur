<?php
class Server extends CPHPDatabaseRecordClass {

	public $table_name = "servers";
	public $id_field = "id";
	public $fill_query = "SELECT * FROM servers WHERE `id` = :Id";
	public $verify_query = "SELECT * FROM servers WHERE `id` = :Id";
	public $query_cache = 1;
	
	public $prototype = array(
		'string' => array(
			'Name' => "name",
			'User' => "user",
			'IPAddress' => "ip_address",
			'Key' => "key",
			'Type' => "type",
			"URL" => "url",
			"StatusType" => "status_type",
			"LoadAlert" => "load_alert",
			"RAMAlert" => "ram_alert",
			"HardDiskAlert" => "hard_disk_alert",
			"HardwareUptime" => "hardware_uptime",
			"TotalMemory" => "total_memory",
			"FreeMemory" => "free_memory",
			"LoadAverage" => "load_average",
			"HardDiskFree" => "hard_disk_free",
			"HardDiskTotal" => "hard_disk_total",
			"Bandwidth" => "bandwidth",
			"Port" => "port",
			"Location" => "location",
			"VolumeGroup" => "volume_group",
			"QEMUPath" => "qemu_path",
		),
		'numeric' => array(
			"Password" => "password",
			"LastCheck" => "last_check",
			"PreviousCheck" => "previous_check",
			"UpSince" => "up_since",
			"DownSince" => "down_since",
			"AlertAfter" =>	"alert_after",
			"LoadAlert" => "load_alert",
			"RAMAlert" => "ram_alert",
			"HardDiskAlert" => "hard_disk_alert",
			"DisplayMemory" => "display_memory",
			"DisplayLoad" => "display_load",
			"DisplayHardDisk" => "display_hard_disk",
			"DisplayNetworkUptime" => "display_network_uptime",
			"DisplayHardwareUptime" => "display_hardware_uptime",
			"DisplayLocation" => "display_location",
			"DisplayHistory" => "display_history",
			"DisplayStatistics" => "display_statistics",
			"DisplayHistoryLink" => "display_hs",
			"DisplayBandwidth" => "display_bandwidth",
			"DisplayHS" => "display_hs",
			"HardwareUptime" => "hardware_uptime",
			"TotalMemory" => "total_memory",
			"FreeMemory" => "free_memory",
			"LoadAverage" => "load_average",
			"HardDiskFree" => "hard_disk_free",
			"HardDiskTotal" => "hard_disk_total",
			"Bandwidth" => "bandwidth",
			"LastBandwidth" => "last_bandwidth",
			"BandwidthTimestamp" => "bandwidth_timestamp",
			"ContainerBandwidth" => "container_bandwidth",
		),
		'boolean' => array(
			"Status" => "status",
			"StatusWarning" => "status_warning",
		),
	);
	
	public static function server_add($uName, $uHostname, $uSuper, $uKey, $uType, $uStatus, $uLocation){
		if(!empty($uName)){
			if(!empty($uHostname)){
				if(!empty($uSuper)){
					if(!empty($uKey)){
						if(!empty($uType)){
							if(!empty($uLocation)){
								$sSSH = new Net_SSH2($uHostname);
								$sKey = new Crypt_RSA();
								$sKey->loadKey($uKey);
								if($sSSH->login($uSuper, $sKey)) {
									$sKeyLocation = random_string(30).'.txt';
									file_put_contents("/var/feathur/data/keys/".$sKeyLocation, $uKey);
									$sServer = new Server(0);
									$sServer->uName = $uName;
									$sServer->uIPAddress = $uHostname;
									$sServer->uUser = $uSuper;
									$sServer->uKey = $sKeyLocation;
									$sServer->uType = $uType;
									if(!empty($uStatus)){
										$sServer->uURL = $uStatus;
									} else {
										$sServer->uURL = "http://".$uHostname."/uptime.php";
									}
									$sServer->uLocation = $uLocation;
									$sServer->uStatusType = "full";
									$sServer->uDisplayMemory = 1;
									$sServer->uDisplayLoad = 1;
									$sServer->uDisplayHardDisk = 1;
									$sServer->uDisplayNetworkUptime = 1;
									$sServer->uDisplayHardwareUptime = 1;
									$sServer->uDisplayLocation = 1;
									$sServer->uDisplayHistory = 1;
									$sServer->uDisplayStatistics = 1;
									$sServer->uDisplayHS = 1;
									$sServer->uDisplayBandwidth = 1;
									$sServer->uContainerBandwidth = 1;
									$sServer->uHardwareUptime = 1;
									$sServer->uUpSince = 1;
									$sServer->InsertIntoDatabase();
									header("Location: admin.php");
								} else {
									return $sResult = array("red" => "Could not connect to the server");
								}
							} else {
								return $sResult = array("red" => "You must enter a location for this server.");
							}
						} else {
							return $sResult = array("red" => "You must select a server type.");
						}
					} else {
						return $sResult = array("red" => "You must enter a SSH key.");
					}
				} else {
					return $sResult = array("red" => "You must enter a super user (Eg: root)");
				}
			} else {
				return $sResult = array("red" => "You must enter an IP or Hostname");
			}
		} else {
			return $sResult = array("red" => "You must give the server a name.");
		}
	}
	
	public static function server_connect($sServer){
		$sSSH = new Net_SSH2($sServer->sIPAddress);
		
		if($sServer->sPassword == 0){
			$sKey = new Crypt_RSA();
			$sKey->loadKey(file_get_contents('/var/feathur/data/keys/'.$sServer->sKey));
		} else {
			$sKey = file_get_contents('/var/feathur/data/keys'.$sServer->sKey);
		}

		if (!$sSSH->login($sServer->sUser, $sKey)) {
    			echo json_encode(array("result" => 'Unable to connect to the host node, please contact customer serivce.'));
    			die();
		} else {
			$sSSH->setTimeout(5);
			return $sSSH;
		}
	}
}