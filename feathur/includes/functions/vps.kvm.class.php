<?php
class kvm {

	public function database_kvm_create($sUser, $sRequested){
		$sUserPermissions = $sUser->sPermissions;
		if($sUserPermissions == 7){
			global $database;
			global $sRequested;
			$uServer = $sRequested["POST"]["server"];
			$uUser = $sRequested["POST"]["user"];
			$uTemplate = $sRequested["POST"]["template"];
			$uRAM = $sRequested["POST"]["ram"];
			$uDisk = $sRequested["POST"]["disk"];
			$uIPAddresses = $sRequested["POST"]["ipaddresses"];
			$uHostname = $sRequested["POST"]["hostname"];
			$uNameserver = $sRequested["POST"]["nameserver"];
			$uCPULimit = $sRequested["POST"]["cpulimit"];
			$uBandwidthLimit = $sRequested["POST"]["bandwidthlimit"];
			if((!empty($uServer)) && (is_numeric($uServer))){
				if((!empty($uUser)) && (is_numeric($uUser))){
					if((!empty($uRAM)) && (is_numeric($uRAM))){
						if((!empty($uDisk)) && (is_numeric($uDisk))){
							if((!empty($uIPAddresses)) && (is_numeric($uIPAddresses))){
								if((!empty($uCPULimit)) && (is_numeric($uCPULimit))){
									if((!empty($uBandwidthLimit)) && (is_numeric($uBandwidthLimit))){
										$sServer = new Server($uServer);
										$sOwner = new User($uUser);
										
										if(!empty($uTemplate)){
											$sTemplate = new Template($uTemplate);
										}
										
										$sIPCheck = VPS::check_ipspace($sServer->sId, $uIPAddresses);
										if(is_array($sIPCheck)){
											return $sIPCheck;
										}
			
			
										if(empty($uHostname)){
											$uHostname = "vps.example.com";
										}
											
										if(empty($uNameserver)){
											$uNameserver = "8.8.8.8";
										}
										
										$sMac = generate_mac();
										
										// VPS Database setup
										$sVPSId = Core::GetSetting('container_id');
										$sUpdate = Core::UpdateSetting('container_id', ($sVPSId->sValue + 1));
										$sVPS = new VPS(0);
										$sVPS->uType = $sServer->sType;
										$sVPS->uHostname = $uHostname;
										$sVPS->uNameserver = $uNameserver;
										$sVPS->uUserId = $sOwner->sId;
										$sVPS->uServerId = $sServer->sId;
										$sVPS->uContainerId = $sVPSId->sValue;
										$sVPS->uRAM = $uRAM;
										$sVPS->uDisk = $uDisk;
										$sVPS->uMac = $sMac;
										$sVPS->uCPULimit = $uCPULimit;
										if(!empty($uTemplate)){
											$sVPS->uTemplateId = $sTemplate->sId;
										}
										$sVPS->uBandwidthLimit = $uBandwidthLimit;
										$sVPS->uVNCPort = ($sVPS->sId + 5900);
										$sVPS->uBootOrder = "hd";
										$sVPS->InsertIntoDatabase();
										
										if($sBlocks = $database->CachedQuery("SELECT * FROM server_blocks WHERE `server_id` = :ServerId", array('ServerId' => $sServer->sId))){
											foreach($sBlocks->data as $key => $value){
												if($sIPs = $database->CachedQuery("SELECT * FROM ipaddresses WHERE `block_id` = :BlockId AND `vps_id` = 0", array('BlockId' => $value["block_id"]))){
													foreach($sIPs->data as $subvalue){
														if($sCurrentIPs < $uIPAddresses){
															$sIPList[] = array("id" => $subvalue["id"], "ip_address" => $subvalue["ip_address"], "block" => $subvalue["block_id"]);
															$sUpdate = $database->CachedQuery("UPDATE ipaddresses SET `vps_id` = :VPSId WHERE `id` = :Id", array('VPSId' => $sVPS->sId, 'Id' => $subvalue["id"]));
															if(empty($sFirst)){
																$sVPS->uPrimaryIP = $subvalue["ip_address"];
																$sVPS->InsertIntoDatabase();
																$sFirst = 1;
															}
															$sCurrentIPs++;
														}
													}
												}																					
											}
										}
										$sRequested["POST"]["VPS"] = $sVPS->sId;
										$sRequested["POST"]["IPList"] = $sIPList;
										return true;
									} else {
										return $sArray = array("json" => 1, "type" => "caution", "result" => "You must input the bandwidth limit!");
									}
								} else {
									return $sArray = array("json" => 1, "type" => "caution", "result" => "You must input the CPU limit!");
								}
							} else {
								return $sArray = array("json" => 1, "type" => "caution", "result" => "You must input the number of IP Addresses!");
							}
						} else {
							return $sArray = array("json" => 1, "type" => "caution", "result" => "You must input the disk limit!");
						}
					} else {
						return $sArray = array("json" => 1, "type" => "caution", "result" => "You must input the available RAM!");
					}
				} else {
					return $sArray = array("json" => 1, "type" => "caution", "result" => "You must input the owner of this VPS!");
				}
			} else {
				return $sArray = array("json" => 1, "type" => "caution", "result" => "You must input the server!");
			}
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Permissions invalid for selected action.");
		}
	}		
	
	public function kvm_create($sUser, $sRequested){
		$sUserPermissions = $sUser->sPermissions;
		if($sUserPermissions == 7){
			$sVPS = new VPS($sRequested["POST"]["VPS"]);
			$sServer = new Server($sVPS->sServerId);
			$sSSH = Server::server_connect($sServer);
			$sIPList = $sRequested["POST"]["IPList"];
			$sMemory = ($sVPS->sRAM * 1024);
			$sHardLimit = ($sMemory + 51200);
			$sCPUs = ($sVPS->sCPULimit / 100);
			
			if(!empty($sVPS->sTemplateId)){
				$sTemplate = new Template($sVPS->sTemplateId);
			}
			
			$sVPSConfig .= "<domain type='kvm'>";
			$sVPSConfig .= "<name>kvm{$sVPS->sContainerId}</name>";
			$sVPSConfig .= "<memory>{$sMemory}</memory>";
			$sVPSConfig .= "<currentMemory>{$sMemory}</currentMemory>";
			$sVPSConfig .= "<memtune><hard_limit>{$sHardLimit}</hard_limit></memtune>";
			$sVPSConfig .= "<vcpu>{$sCPUs}</vcpu>";
			$sVPSConfig .= "<cpu><topology sockets='1' cores='{$sCPUs}' threads='1'/></cpu>";
			$sVPSConfig .= "<os><type machine='pc'>hvm</type><boot dev='{$sVPS->sBootOrder}'/></os>";
			$sVPSConfig .= "<clock sync='localtime'/>";
			$sVPSConfig .= "<devices><emulator>/usr/bin/qemu</emulator><disk type='file' device='disk'><source file='/dev/{$sServer->sVolumeGroup}/kvm{$sVPS->sContainerId}_img'/><target dev='hda' bus='ide'/></disk><disk type='file' device='cdrom'>";
			
			if(!empty($sVPS->sTemplateId)){
				$sVPSConfig .= "<source file='/var/feathur/data/templates/kvm/{$sTemplate->sPath}'/>";
			}
			
			$sVPSConfig .= "<target dev='hdc'/><readonly/></disk>";
			$sVPSConfig .= "<interface type='bridge'><source bridge='br0'/><target dev='kvm{$sVPS->sContainerId}.0'/><mac address='{$sVPS->sMac}'/></interface>";
			$sVPSConfig .= "<graphics type='vnc' port='{$sVPS->sVNCPort}' passwd='' listen='127.0.0.1'/>";
			$sVPSConfig .= "<input type='tablet'/><input type='mouse'/></devices><features><acpi/><apic/></features></domain>";
			$sVPSConfig = escapeshellarg($sVPSConfig);
			
			$sBlock = new Block($sIPList[0]["block"]);
			$sDHCP .= "host kvm{$sVPS->sContainerId}.0 { hardware ethernet {$sVPS->sMac}; option routers {$sBlock->sGateway}; option subnet-mask {$sBlock->sNetmask}; fixed-address {$sIPList[0]["ip_address"]}; option domain-name-servers {$sVPS->sNameserver}; }";
			$sDHCP = escapeshellarg($sDHCP);
			
			$sCommandList .= "mkdir /var/feathur/;mkdir /var/feathur/configs/;echo {$sVPSConfig} > /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml; echo {$sDHCP} > /var/feathur/configs/kvm{$sVPS->sContainerId}-dhcp.conf;cat /var/feathur/configs/dhcpd.head /var/feathur/configs/*-dhcp.conf > /etc/dhcp/dhcpd.conf;service isc-dhcp-server restart;lvcreate -n kvm{$sVPS->sContainerId}_img -L {$sVPS->sDisk}G {$sServer->sVolumeGroup};virsh create /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml;";
			
			$sLog[] = array("command" => str_replace($sPassword, "obfuscated", $sCommandList), "result" => $sSSH->exec($sCommandList));
			$sSave = VPS::save_vps_logs($sLog, $sVPS);
			
			return $sArray = array("json" => 1, "type" => "success", "result" => "VPS has been created!", "reload" => 1, "vps" => $sVPS->sId);
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Permissions invalid for selected action.");
		}
	}
	
	public function database_kvm_boot($sUser, $sVPS, $sRequested){
		return true;
	}
	
	public function kvm_boot($sUser, $sVPS, $sRequested){
		$sServer = new Server($sVPS->sServerId);
		$sSSH = Server::server_connect($sServer);
		$sLog[] = array("command" => "virsh create /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml;", "result" => $sSSH->exec("virsh create /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml;"));
		$sSave = VPS::save_vps_logs($sLog, $sVPS);
		if(strpos($sLog[0]["result"], 'already exists') !== false) {
			return $sArray = array("json" => 1, "type" => "caution", "result" => "VPS is already running!");
		} elseif(strpos($sLog[0]["result"], 'No such file') !== false) {
			return $sArray = array("json" => 1, "type" => "error", "result" => "VPS is disabled, contact support!");
		} elseif(strpos($sLog[0]["result"], 'created from') !== false) { 
			return $sArray = array("json" => 1, "type" => "success", "result" => "VPS is currently starting up...");
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "An unknown error occured, contact support!");
		}
	}
	
	public function database_kvm_shutdown($sUser, $sVPS, $sRequested){
		return true;
	}
	
	public function kvm_shutdown($sUser, $sVPS, $sRequested){
		$sServer = new Server($sVPS->sServerId);
		$sSSH = Server::server_connect($sServer);
		$sLog[] = array("command" => "virsh destroy kvm{$sVPS->sContainerId}", "result" => $sSSH->exec("virsh destroy kvm{$sVPS->sContainerId}"));
		$sSave = VPS::save_vps_logs($sLog, $sVPS);
		if(strpos($sLog[0]["result"], 'destroyed') !== false) {
			return $sArray = array("json" => 1, "type" => "success", "result" => "VPS is shutting down... This could take a while.");
		} elseif(strpos($sLog[0]["result"], 'Domain not found') !== false) {
			return $sArray = array("json" => 1, "type" => "caution", "result" => "VPS is already shutdown!");
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "An unknown error occured, contact support!");
		}
	}
	
	public function database_kvm_reboot($sUser, $sVPS, $sRequested){
		return true;
	}
	
	public function kvm_reboot($sUser, $sVPS, $sRequested){
		$sServer = new Server($sVPS->sServerId);
		$sSSH = Server::server_connect($sServer);
		$sLog[] = array("command" => "virsh destroy kvm{$sVPS->sContainerId};virsh create /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml;", "result" => $sSSH->exec("virsh destroy kvm{$sVPS->sContainerId};virsh create /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml;"));
		$sSave = VPS::save_vps_logs($sLog, $sVPS);
		if(strpos($sLog[0]["result"], 'No such file') !== false) {
			return $sArray = array("json" => 1, "type" => "error", "result" => "VPS is disabled, contact support!");
		} elseif(strpos($sLog[0]["result"], 'created from') !== false) { 
			return $sArray = array("json" => 1, "type" => "success", "result" => "VPS is being restarted now...");
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "An unknown error occured, contact support!");
		}
	}
	
	public function database_kvm_password($sUser, $sVPS, $sRequested){
		return true;
	}
	
	public function kvm_password($sUser, $sVPS, $sRequested){
		if((!empty($sRequested["POST"]["password"])) && ((strlen($sRequested["POST"]["password"])) >= 5)){
			$sPassword = escapeshellarg($sRequested["POST"]["password"]);
			$sServer = new Server($sVPS->sServerId);
			$sSSH = Server::server_connect($sServer);
			$sVNCPort = ($sVPS->sVNCPort - 5900);
			$sLog[] = array("command" => "virsh qemu-monitor-command kvm{$sVPS->sContainerId} --hmp change vnc :{$sVNCPort};virsh qemu-monitor-command kvm{$sVPS->sContainerId} --hmp change vnc password obfuscated;", "result" => $sSSH->exec("virsh qemu-monitor-command kvm{$sVPS->sContainerId} --hmp change vnc :{$sVNCPort};virsh qemu-monitor-command kvm{$sVPS->sContainerId} --hmp change vnc password {$sPassword};"));
			$sSave = VPS::save_vps_logs($sLog, $sVPS);
			return $sArray = array("json" => 1, "type" => "success", "result" => "VNC password set, you can now connect!");
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Your password must be at least 5 characters!");
		}
	}
	
	public function database_kvm_mount($sUser, $sVPS, $sRequested){
		if(is_numeric($sRequested["GET"]["template"])){
			$sTemplate = new Template($sRequested["GET"]["template"]);
			$sVPS->uTemplateId = $sTemplate->sId;
			$sVPS->InsertIntoDatabase();
			$this->kvm_config($sUser, $sVPS, $sRequested);
			return true;
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Invalid ISO selected, please try again!");
		}
	}
	
	public function kvm_mount($sUser, $sVPS, $sRequested){
		$sServer = new Server($sVPS->sServerId);
		$sSSH = Server::server_connect($sServer);
		$sTemplate = new Template($sVPS->sTemplateId);
		$sLog[] = array("command" => "virsh attach-disk kvm{$sVPS->sContainerId} /var/feathur/data/templates/kvm/{$sTemplate->sPath}.iso hdc --type cdrom;", "result" => $sSSH->exec("virsh attach-disk kvm{$sVPS->sContainerId} /var/feathur/data/templates/kvm/{$sTemplate->sPath}.iso hdc --type cdrom;"));
		$sSave = VPS::save_vps_logs($sLog, $sVPS);
		return $sArray = array("json" => 1, "type" => "success", "result" => "{$sTemplate->sName} has been mounted!");
	}
	
	public function kvm_config($sUser, $sVPS, $sRequested){
		$sServer = new Server($sVPS->sServerId);
		$sSSH = Server::server_connect($sServer);
		$sIPList = VPS::list_ipspace($sVPS);
		$sMemory = ($sVPS->sRAM * 1024);
		$sHardLimit = ($sMemory + 51200);
		$sCPUs = ($sVPS->sCPULimit / 100);
		
		
		$sTemplateId = $sVPS->sTemplateId;
		if(!empty($sTemplateId)){	
			$sTemplate = new Template($sVPS->sTemplateId);
		}
			
		$sVPSConfig .= "<domain type='kvm'>";
		$sVPSConfig .= "<name>kvm{$sVPS->sContainerId}</name>";
		$sVPSConfig .= "<memory>{$sMemory}</memory>";
		$sVPSConfig .= "<currentMemory>{$sMemory}</currentMemory>";
		$sVPSConfig .= "<memtune><hard_limit>{$sHardLimit}</hard_limit></memtune>";
		$sVPSConfig .= "<vcpu>{$sCPUs}</vcpu>";
		$sVPSConfig .= "<cpu><topology sockets='1' cores='{$sCPUs}' threads='1'/></cpu>";
		$sVPSConfig .= "<os><type machine='pc'>hvm</type><boot dev='{$sVPS->sBootOrder}'/></os>";
		$sVPSConfig .= "<clock sync='localtime'/>";
		$sVPSConfig .= "<devices><emulator>/usr/bin/qemu</emulator><disk type='file' device='disk'><source file='/dev/{$sServer->sVolumeGroup}/kvm{$sVPS->sContainerId}_img'/><target dev='hda' bus='ide'/></disk><disk type='file' device='cdrom'>";
			
		if(isset($sTemplate)){
			$sVPSConfig .= "<source file='/var/feathur/data/templates/kvm/{$sTemplate->sPath}.iso'/>";
		}
			
		$sVPSConfig .= "<target dev='hdc'/><readonly/></disk>";
		$sVPSConfig .= "<interface type='bridge'><source bridge='br0'/><target dev='kvm{$sVPS->sContainerId}.0'/><mac address='{$sVPS->sMac}'/></interface>";
		$sVPSConfig .= "<graphics type='vnc' port='{$sVPS->sVNCPort}' passwd='' listen='127.0.0.1'/>";
		$sVPSConfig .= "<input type='tablet'/><input type='mouse'/></devices><features><acpi/><apic/></features></domain>";
		$sVPSConfig = escapeshellarg($sVPSConfig);
			
		$sBlock = new Block($sIPList[0]["block"]);
		$sDHCP .= "host kvm{$sVPS->sContainerId}.0 { hardware ethernet {$sVPS->sMac}; option routers {$sBlock->sGateway}; option subnet-mask {$sBlock->sNetmask}; fixed-address {$sIPList[0]["ip"]}; option domain-name-servers {$sVPS->sNameserver}; }";
		$sDHCP = escapeshellarg($sDHCP);
			
		$sCommandList .= "mkdir /var/feathur/;mkdir /var/feathur/configs/;echo {$sVPSConfig} > /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml; echo {$sDHCP} > /var/feathur/configs/kvm{$sVPS->sContainerId}-dhcp.conf;cat /var/feathur/configs/dhcpd.head /var/feathur/configs/*-dhcp.conf > /etc/dhcp/dhcpd.conf;service isc-dhcp-server restart;";
			
		$sLog[] = array("command" => str_replace($sPassword, "obfuscated", $sCommandList), "result" => $sSSH->exec($sCommandList));
		$sSave = VPS::save_vps_logs($sLog, $sVPS);
	}
	
	public function database_kvm_bootorder($sUser, $sVPS, $sRequested){
		if($sRequested["GET"]["order"] == 'hd'){
			$sVPS->uBootOrder = "hd";
			$sVPS->InsertIntoDatabase();
			$this->kvm_config($sUser, $sVPS, $sRequested);
			return $sArray = array("json" => 1, "type" => "success", "result" => "Config updated, please reboot your VPS!");
		} elseif($sRequested["GET"]["order"] == 'cd'){
			$sVPS->uBootOrder = "cdrom";
			$sVPS->InsertIntoDatabase();
			$this->kvm_config($sUser, $sVPS, $sRequested);
			return $sArray = array("json" => 1, "type" => "success", "result" => "Config updated, please reboot your VPS!");
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Invalid boot order selected!");
		}
	}
	
	public function kvm_bootorder($sUser, $sVPS, $sRequested){
		return true;
	}
	
	public function database_kvm_statistics($sUser, $sVPS, $sRequested){
		return true;
	}
	
	public function kvm_statistics($sUser, $sVPS, $sRequested){
		global $database;
		$sServer = new Server($sVPS->sServerId);
		$sSSH = Server::server_connect($sServer);
		$sLog[] = array("command" => "virsh list | grep kvm{$sVPS->sContainerId}", "result" => $sSSH->exec("virsh list | grep kvm{$sVPS->sContainerId}"));
		if(strpos($sLog[0]["result"], 'running') === false) {
			return $sArray = array("json" => 1, "type" => "status", "result" => "offline", "hostname" => $sVPS->sHostname);
		} else {
			return $sArray = array("json" => 1, "type" => "status", "result" => "online", "hostname" => $sVPS->sHostname);
		}
	}
	
	public function database_kvm_getrdns($sUser, $sVPS, $sRequested){
		$sIP = new IP($sRequested["GET"]["ip"]);
		if($sIP->sVPSId == $sVPS->sId){
			return $sArray = RDNS::pull_rdns($sIP);
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "That IP does not belong to you.");
		}
	}
	
	public function kvm_getrdns($sUser, $sVPS, $sRequested){
		return true;
	}
	
	public function database_kvm_setrdns($sUser, $sVPS, $sRequested){
		$sIP = new IP($sRequested["GET"]["ip"]);
		if($sIP->sVPSId == $sVPS->sId){
			return RDNS::add_rdns($sIP, $sRequested["GET"]["hostname"]);
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "That IP does not belong to you.");
		}
	}
	
	public function kvm_setrdns($sUser, $sVPS, $sRequested){
		return true;
	}
	
	public function database_kvm_hostname($sUser, $sVPS, $sRequested){
		$sHostname = preg_replace('/[^A-Za-z0-9-.]/', '', $sRequested["GET"]["hostname"]);
		if(!empty($sHostname)){
			$sVPS->uHostname = $sHostname;
			$sVPS->InsertIntoDatabase();
			return $sArray = array("json" => 1, "type" => "success", "result" => "Hostname has been updated successfully.");
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Hostname can not be blank!");
		}
	}
	
	public function kvm_hostname($sUser, $sVPS, $sRequested){
		return true;
	}
	
	public function database_kvm_primaryip($sUser, $sVPS, $sRequested){
		$sIP = new IP($sRequested["GET"]["ip"]);
		if($sIP->sVPSId == $sVPS->sId){
			$sVPS->uPrimaryIP = $sIP->sIPAddress;
			$sVPS->InsertIntoDatabase();
			return true;
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "That IP does not belong to you.");
		}
	}
	
	public function kvm_primaryip($sUser, $sVPS, $sRequested){
		$sIP = new IP($sRequested["GET"]["ip"]);
		$sBlock = new Block($sIP->sBlockId);
		$sServer = new Server($sVPS->sServerId);
		$sSSH = Server::server_connect($sServer);
		$sDHCP .= "host kvm{$sVPS->sContainerId}.0 { hardware ethernet {$sVPS->sMac}; option routers {$sBlock->sGateway}; option subnet-mask {$sBlock->sNetmask}; fixed-address {$sIP->sIPAddress}; option domain-name-servers {$sVPS->sNameserver}; }";
		$sDHCP = escapeshellarg($sDHCP);	
		$sCommandList .= "echo {$sDHCP} > /var/feathur/configs/kvm{$sVPS->sContainerId}-dhcp.conf;cat /var/feathur/configs/dhcpd.head /var/feathur/configs/*-dhcp.conf > /etc/dhcp/dhcpd.conf;service isc-dhcp-server restart;";
		$sLog[] = array("command" => $sCommandList, "result" => $sSSH->exec($sCommandList));
		$sSave = VPS::save_vps_logs($sLog, $sVPS);
		return $sArray = array("json" => 1, "type" => "success", "result" => "Primary IP changed to {$sIP->sIPAddress}");
	}
}