<?php
class kvm {
	
	public function kvm_check_suspended($sVPS){
		$sSuspended = $sVPS->sSuspended;
		if($sSuspended == 1){
			return true;
		} else {
			return false;
		}
	}

	public function database_kvm_create($sUser, $sRequested, $sAPI = 0){
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
			$uIPv6Allowed = $sRequested["POST"]["ipv6allowed"];
			$uBandwidthLimit = $sRequested["POST"]["bandwidthlimit"];
			if((!empty($uServer)) && (is_numeric($uServer))){
				if((!empty($uUser)) && (is_numeric($uUser))){
					if((!empty($uRAM)) && (is_numeric($uRAM))){
						if((!empty($uDisk)) && (is_numeric($uDisk))){
							if((!empty($uCPULimit)) && (is_numeric($uCPULimit))){
								if((!empty($uBandwidthLimit)) && (is_numeric($uBandwidthLimit))){
									$sServer = new Server($uServer);
									$sOwner = new User($uUser);
									
									if(!empty($uTemplate)){
										$sTemplate = new Template($uTemplate);
									}
									if(empty($sAPI)){
										$sIPCheck = VPS::check_ipspace($sServer->sId, $uIPAddresses);
										if(is_array($sIPCheck)){
											return $sIPCheck;
										}
									}
		
									if(empty($uHostname)){
										$uHostname = "vps.example.com";
									}
										
									if(empty($uNameserver)){
										$uNameserver = "8.8.8.8";
									}
									
									while($sTotalMacs < $uIPAddresses){
										if(empty($sTotalMacs)){
											$sMac = generate_mac();
										} else {
											$sMac .= ",".generate_mac();
										}
										$sTotalMacs++;
									}
									
									// VPS Database setup
									$sVPSId = Core::GetSetting('container_id');
									$sUpdate = Core::UpdateSetting('container_id', ($sVPSId->sValue + 1));
									$sVPS = new VPS(0);
									$sVPS->uType = $sServer->sType;
									$sVPS->uHostname = preg_replace('/[^A-Za-z0-9-.]/', '', $uHostname);
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
									$sVPS->uVNCPort = ($sVPSId->sValue + 5900);
									$sVPS->uBootOrder = "hd";
									$sVPS->uIPv6 = $uIPv6Allowed;
									$sVPS->InsertIntoDatabase();
									
									if($sBlocks = $database->CachedQuery("SELECT * FROM server_blocks WHERE `server_id` = :ServerId AND `ipv6` = 0", array('ServerId' => $sServer->sId))){
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
									
									if(!empty($sAPI)){
										return $sVPS->sId;
									}
									return true;
								} else {
									return $sArray = array("json" => 1, "type" => "caution", "result" => "You must input the bandwidth limit!");
								}
							} else {
								return $sArray = array("json" => 1, "type" => "caution", "result" => "You must input the CPU limit!");
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
			$sCreate = $this->kvm_config($sUser, $sVPS, $sRequested);
			$sDHCP = $this->kvm_dhcp($sUser, $sVPS, $sRequested);
			
			// Load up settings.
			$sVPSTemplate = $sVPS->sTemplateId;
			if(!empty($sVPSTemplate)){
				try {
					$sTemplate = new Template($sVPS->sTemplateId);

					$sTemplatePath = escapeshellarg($sTemplate->sPath);
					$sTemplateURL = escapeshellarg($sTemplate->sURL);
					
					// Check to make sure the template is on the server and is within 5 MB of the target size.
					$sCheckSynced = $sSSH->exec("cd /var/feathur/data/templates/kvm/;ls -nl {$sTemplatePath} | awk '{print $5}'");
					$sUpper = $sTemplate->sSize + 5242880;
					$sLower = $sTemplate->sSize - 5242880;
					if(strpos($sCheckSynced, 'No such file or directory') !== false) { 
						$sSync = true;
					}

					if(($sCheckSynced < $sLower) || ($sCheckSynced > $sUpper)){
						$sSync = true;
						$sCleanup = $sSSH->exec("cd /var/feathur/data/templates/kvm/;rm -rf {$sTemplatePath};");
					}
					
					if($sSync === true){
						$sVPS->uISOSyncing = 1;
						$sVPS->InsertIntoDatabase();
						$sCommandList = "screen -dmS templatesync{$sVPS->sContainerId} bash -c \"cd /var/feathur/data/templates/kvm/;wget -O {$sTemplatePath} {$sTemplateURL};lvcreate -n kvm{$sVPS->sContainerId}_img -L {$sVPS->sDisk}G {$sServer->sVolumeGroup};virsh create /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml;virsh autostart kvm{$sVPS->sContainerId}\";";
						$sExecute = $sSSH->exec($sCommandList);
						$sLog[] = array("command" => $sCommandList, "result" => "Screened build of KVM VPS");
						$sSave = VPS::save_vps_logs($sLog, $sVPS);
						return $sArray = array("json" => 1, "type" => "success", "result" => "VPS has been created!", "reload" => 1, "vps" => $sVPS->sId);
					}
				} catch (Exception $e) {
					$sVPS->uTemplate = 0;
					$sVPS->InsertIntoDatabase();
					$sVPSTemplate = "404";
					$sChange = $this->kvm_config($sUser, $sVPS, $sRequested, $_SESSION['vnc_password']);
				}
			} 
			
			$sCommandList = "lvcreate -n kvm{$sVPS->sContainerId}_img -L {$sVPS->sDisk}G {$sServer->sVolumeGroup};";
			$sCommandList .= "virsh create /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml;virsh autostart kvm{$sVPS->sContainerId};";
			$sLog[] = array("command" => $sCommandList, "result" => $sSSH->exec($sCommandList));
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
		$sCreate = $this->kvm_config($sUser, $sVPS, $sRequested, $_SESSION["vnc_password"]);
		$sDHCP = $this->kvm_dhcp($sUser, $sVPS, $sRequested);
		
		if($sVPS->sISOSyncing == 1){
			$sCheckScreen = $sSSH->exec("if screen -list | grep -q 'templatesync{$sVPS->sContainerId}'; then echo 'exists'; fi");
			if(strpos($sCheckScreen, 'exists') !== false) {
				return $sArray = array("json" => 1, "type" => "success", "result" => "Template is still syncing...");
			} else {
				$sVPS->uISOSyncing = 0;
				$sVPS->InsertIntoDatabase();
				$sCommandList .= "virsh create /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml;virsh autostart kvm{$sVPS->sContainerId};";
				
				$sLog[] = array("command" => $sCommandList, "result" => $sSSH->exec($sCommandList));
				$sSave = VPS::save_vps_logs($sLog, $sVPS);
				
				
			}
		}
		
		$sVersion = $sSSH->exec("virsh --version");
		$sVersion = explode(".", $sVersion);
		if($sVersion[0] != 1){
			$sChange = $this->kvm_config($sUser, $sVPS, $sRequested, $_SESSION['vnc_password']);
		}
		
		// Load up settings.
		$sVPSTemplate = $sVPS->sTemplateId;
		if(!empty($sVPSTemplate)){
			try {
				$sTemplate = new Template($sVPS->sTemplateId);
				$sVPSTemplate = $sTemplate->sPath;
				
				$sTemplatePath = escapeshellarg($sTemplate->sPath);
				$sTemplateURL = escapeshellarg($sTemplate->sURL);
				
				// Check to make sure the template is on the server and is within 5 MB of the target size.
				$sCheckSynced = $sSSH->exec("cd /var/feathur/data/templates/kvm/;ls -nl {$sTemplatePath} | awk '{print $5}'");
				if(strpos($sCheckSynced, 'No such file or directory') !== false) { 
					$sSync = true;
				}
				
				if($sSync === true){
					$sVPS->uISOSyncing = 1;
					$sVPS->InsertIntoDatabase();
					$sCommandList .= "screen -dmS templatesync{$sVPS->sContainerId} bash -c \"cd /var/feathur/data/templates/kvm/;wget -O {$sTemplatePath} {$sTemplateURL};virsh create /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml;virsh autostart kvm{$sVPS->sContainerId};\";";
				} else {
					$sCommandList .= "virsh create /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml;virsh autostart kvm{$sVPS->sContainerId};";
				}
			} catch (Exception $e) {
				$sVPS->uTemplate = 0;
				$sVPS->InsertIntoDatabase();
				$sVPSTemplate = "404";
				$sChange = $this->kvm_config($sUser, $sVPS, $sRequested, $_SESSION['vnc_password']);
				$sCommandList .= "virsh create /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml;virsh autostart kvm{$sVPS->sContainerId};";
			}
		} else {
			$sChange = $this->kvm_config($sUser, $sVPS, $sRequested, $_SESSION['vnc_password']);
			$sCommandList .= "virsh create /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml;virsh autostart kvm{$sVPS->sContainerId};";
		}
		
		$sLog[] = array("command" => $sCommandList, "result" => $sSSH->exec($sCommandList));
		$sSave = VPS::save_vps_logs($sLog, $sVPS);
		
		// Return output.
		if($sSync === true){
			return $sArray = array("json" => 1, "type" => "success", "result" => "Template syncing VPS will start in ~3 minutes.");
		}
		
		if(strpos($sLog[0]["result"], 'created from') !== false) {
			return $sArray = array("json" => 1, "type" => "success", "result" => "VPS booted successfully.");
		}
		
		if(strpos($sLog[0]["result"], "cannot open file '/dev") !== false) {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Virtual disk does not exist, contact support.");
		}
		
		return $sArray = array("json" => 1, "type" => "error", "result" => "An unknown error occured, contact support if your VPS fails to boot.");
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
		$sShutdown = $this->kvm_shutdown($sUser, $sVPS, $sRequested);
		$sStartup = $this->kvm_boot($sUser, $sVPS, $sRequested);
		if(is_array($sStartup)){
			return $sStartup;
		}
		
		return $sArray = array("json" => 1, "type" => "error", "result" => "An unknown error occured. Please contact support.");
	}
	
	public function database_kvm_password($sUser, $sVPS, $sRequested){
		return true;
	}
	
	public function kvm_password($sUser, $sVPS, $sRequested){
		if((!empty($sRequested["POST"]["password"])) && ((strlen($sRequested["POST"]["password"])) >= 5)){
			// Make sure this is a valid password
			if(strlen($sRequested["POST"]["password"]) >= 9){
				return $sArray = array("json" => 1, "type" => "error", "result" => "Your password must be less than 9 characters.");
			}
			
			if(!ctype_alnum($sRequested["POST"]["password"])){
				return $sArray = array("json" => 1, "type" => "error", "result" => "Your password can not contain special characters.");
			}
			
			// Connect to server and set session variables.
			$sServer = new Server($sVPS->sServerId);
			$sSSH = Server::server_connect($sServer);
			$_SESSION["vnc_password"] = $sRequested["POST"]["password"];
			$_SESSION["vnc_vps"] = $sVPS->sId;
			
			// Check to see if virsh is at least version 1.0. Password setting is bugged on previous versions.
			// Rather not save password to text file if we don't have to, but will if need be.
			$sLog[] = array("command" => "virsh --version", "result" => $sSSH->exec("virsh --version"));
			$sVersion = explode(".", $sLog[0]["result"]);
			if($sVersion[0] == 1){
				$sVNCPort = ($sVPS->sVNCPort - 5900);
				$sPassword = escapeshellarg($sRequested["POST"]["password"]);
				$sLog[] = array("command" => "virsh qemu-monitor-command kvm{$sVPS->sContainerId} --hmp change vnc :{$sVNCPort};virsh qemu-monitor-command kvm{$sVPS->sContainerId} --hmp change vnc password obfuscated;", "result" => $sSSH->exec("virsh qemu-monitor-command kvm{$sVPS->sContainerId} --hmp change vnc :{$sVNCPort};virsh qemu-monitor-command kvm{$sVPS->sContainerId} --hmp change vnc password {$sPassword};"));
				$sSuccess = "VNC password set, you can now connect.";
			} else {
				$sChange = $this->kvm_config($sUser, $sVPS, $sRequested, $sRequested["POST"]["password"]);
				$sSuccess = "Reboot your VPS, then you can connect to VNC.";
			}
			$sSave = VPS::save_vps_logs($sLog, $sVPS);
			return $sArray = array("json" => 1, "type" => "success", "result" => $sSuccess);
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Your password must be at least 5 characters.");
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
		$sLog[] = array("command" => "virsh attach-disk kvm{$sVPS->sContainerId} /var/feathur/data/templates/kvm/{$sTemplate->sPath} hdc --type cdrom;", "result" => $sSSH->exec("virsh attach-disk kvm{$sVPS->sContainerId} /var/feathur/data/templates/kvm/{$sTemplate->sPath}.iso hdc --type cdrom;"));
		$sUpdateConfig = $this->kvm_config($sUser, $sVPS, $sRequested);
		$sSave = VPS::save_vps_logs($sLog, $sVPS);
		return $sArray = array("json" => 1, "type" => "success", "result" => "{$sTemplate->sName} has been mounted, please reboot your VPS.");
	}
	
	public function kvm_config($sUser, $sVPS, $sRequested, $sPassword = 0){
		$sCheck = $this->kvm_check_suspended($sVPS);
		if($sCheck == true){
			echo json_encode(array("result" => "This VPS is Suspended!", "type" => "success", "json" => 1));
			die();
		}
		
		$sServer = new Server($sVPS->sServerId);
		$sSSH = Server::server_connect($sServer);
		$sIPList = VPS::list_ipspace($sVPS);
		$sMemory = ($sVPS->sRAM * 1024);
		$sHardLimit = ($sMemory + 51200);
		$sCPUs = $sVPS->sCPULimit;
		
		$sTemplateId = $sVPS->sTemplateId;
		if(!empty($sTemplateId)){	
			try {
				$sTemplate = new Template($sVPS->sTemplateId);
			} catch (Exception $e) {
				$sVPS->uTemplate = 0;
				$sVPS->InsertIntoDatabase();
			}
		}
		
		$sQEMUPath = $sServer->sQEMUPath;
		if(!empty($sQEMUPath)){
			$sQEMUPath = "<emulator>{$sQEMUPath}</emulator>";
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
		
		$sDiskDriver = $sVPS->sDiskDriver;
		if((empty($sDiskDriver)) || ($sDiskDriver == 'ide')){
			$sTarget = "<target dev='hda' bus='ide'/>";
		} elseif($sDiskDriver == 'scsi'){
			$sTarget = "<target dev='sdg' bus='scsi'/>";
		} elseif($sDiskDriver == 'virtio'){
			$sTarget = "<target dev='vda' bus='virtio'/>";
		} else {
			$sTarget = "<target dev='hda' bus='ide'/>";
		}
		
		if(isset($sVPS->sSecondaryDrive)){
			$sSecondary = "<disk type='file' device='disk'><source file='{$sVPS->sSecondaryDrive}'/>{$sTarget}</disk>";
		}
		
		$sVPSConfig .= "<devices>{$sQEMUPath}<disk type='file' device='disk'><source file='/dev/{$sServer->sVolumeGroup}/kvm{$sVPS->sContainerId}_img'/>{$sTarget}</disk>{$sSecondary}<disk type='file' device='cdrom'>";
			
		if(isset($sTemplate)){
			$sVPSConfig .= "<source file='/var/feathur/data/templates/kvm/{$sTemplate->sPath}'/>";
		}
			
		$sVPSConfig .= "<target dev='hdc'/><readonly/></disk>";
		
		$sNetworkDriver = $sVPS->sNetworkDriver;
		if(empty($sNetworkDriver)){
			$sVPS->uNetworkDriver = "e1000";
			$sVPS->InsertIntoDatabase();
		}
		
		$sIPCount = count($sIPList);
		$sMacList = explode(",", $sVPS->sMac);
		$sCurrent = 0;
		if($sIPCount >= 1){
			foreach($sIPList as $sKey => $sValue){
				$sVPSConfig .= "<interface type='bridge'><source bridge='br0'/><target dev='kvm{$sVPS->sContainerId}.{$sCurrent}'/><mac address='{$sMacList[$sCurrent]}'/><model type='{$sVPS->sNetworkDriver}' /></interface>";
				$sCurrent++;
			}
		}
		
		$sPrivateNetwork = $sVPS->sPrivateNetwork;
		if($sPrivateNetwork == 1){
			if(empty($sMacList[$sCurrent])){
				$sMac = $sVPS->sMac;
				$sNewMac = generate_mac();
				$sVPS->uMac = $sMac.','.$sNewMac;
				$sVPS->InsertIntoDatabase();
				$sMac = $sNewMac;
			} else {
				$sMac = $sMacList[$sCurrent];
			}
			$sVPSConfig .= "<interface type='bridge'><source bridge='pb{$sVPS->sUserId}'/><target dev='kvm{$sVPS->sContainerId}.{$sCurrent}'/><mac address='{$sMac}'/><model type='{$sVPS->sNetworkDriver}' /></interface>";
			$sPrivateNetworkCommands = "brctl addbr pb{$sVPS->sUserId}; brctl addif pb{$sVPS->sUserId} kvm{$sVPS->sContainerId}.{$sCurrent};";
		}
		
		if(empty($sPassword)){
			$sVPSConfig .= "<graphics type='vnc' port='{$sVPS->sVNCPort}' passwd='' listen='127.0.0.1'/>";
		} else {
			$sPassword = preg_replace("/[^A-Za-z0-9]/", '', $sPassword);
			$sPassword = escapeshellarg($sPassword);
			$sVPSConfig .= "<graphics type='vnc' port='{$sVPS->sVNCPort}' passwd={$sPassword} listen='0.0.0.0'/>";
		}
		$sVPSConfig .= "<input type='tablet'/><input type='mouse'/></devices><features><acpi/><apic/></features></domain>";

		$sBlock = new Block($sIPList[0]["block"]);
		$sDHCP .= "host kvm{$sVPS->sContainerId}.0 { hardware ethernet {$sVPS->sMac}; option routers {$sBlock->sGateway}; option subnet-mask {$sBlock->sNetmask}; fixed-address {$sIPList[0]["ip"]}; option domain-name-servers {$sVPS->sNameserver}; }";
		$sDHCP = escapeshellarg($sDHCP);
		
		$sHead = escapeshellarg(file_get_contents('/var/feathur/feathur/includes/configs/dhcp.head'));
		$sCommandList .= "mkdir /var/feathur/;mkdir /var/feathur/configs/; echo \"{$sVPSConfig}\" > /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml; echo {$sHead} > /var/feathur/configs/dhcp.head;echo {$sDHCP} > /var/feathur/configs/kvm{$sVPS->sContainerId}-dhcp.conf;cat /var/feathur/configs/dhcpd.head /var/feathur/configs/*-dhcp.conf > /etc/dhcp/dhcpd.conf;service isc-dhcp-server restart;{$sPrivateNetworkCommands}";
		
		if($sRequested["GET"]["diskchanged"] == 1){
			$sCommandList .= "lvextend --size {$sVPS->sDisk}G /dev/{$sServer->sVolumeGroup}/kvm{$sVPS->sContainerId}_img;";
		}
		
		if(empty($sPassword)){
			$sLog[] = array("command" => $sCommandList, "result" => $sSSH->exec($sCommandList));
		} else {
			$sLog[] = array("command" => str_replace($sPassword, "obfuscated", $sCommandList), "result" => $sSSH->exec($sCommandList));
		}
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
		global $sTemplate;
		global $database;
		global $locale;
		$sServer = new Server($sVPS->sServerId);
		$sSSH = Server::server_connect($sServer);
		
		$sLog[] = array("command" => "virsh list | grep kvm{$sVPS->sContainerId}", "result" => $sSSH->exec("virsh list | grep kvm{$sVPS->sContainerId}"));
		if($sTemplates = $database->CachedQuery("SELECT * FROM templates WHERE `id` = :TemplateId", array('TemplateId' => $sVPS->sTemplateId))){
			$sVPSTemplate = new Template($sVPS->sTemplateId);
			$sTemplateName = $sVPSTemplate->sName;
		} else {
			$sTemplateName = "N/A";
		}
		
		if($sVPS->sISOSyncing == 1){
			$sTemplatePath = escapeshellarg($sVPSTemplate->sPath);
				
			// Check syncing progress.
			$sCheckSync = $sSSH->exec("cd /var/feathur/data/templates/kvm/;ls -nl {$sTemplatePath} | awk '{print $5}'");
			$sUpper = $sVPSTemplate->sSize + 5242880;
			
			if($sCheckSync < ($sVPSTemplate->sSize - 5242880)){
				if($sCheckSync > 500){
					$sISOSync = 1;
					$sPercentSync = round(((100 / ($sVPSTemplate->sSize)) * $sCheckSync), 0);
				} else {
					$sISOSync = 1;
					$sPercentSync = 0;
				}
			} elseif($sCheckSync > $sUpper){
				$sISOSync = 1;
				$sSyncError = 1;
			} else {
				$sVPS->uISOSyncing = 0;
				$sVPS->InsertIntoDatabase();
				$sISOSync = 0;
				$sSyncError = 0;
			}
			
			if(strpos($sCheckSync, 'No such file or directory') !== false) {
				$sISOSync = 1;
				$sSyncError = 1;
			}
		}
		
		if($sVPS->sBandwidthUsage > 0){
			$sBandwidthUsage = FormatBytes($sVPS->sBandwidthUsage, 0);
		} else {
			$sBandwidthUsage = "0 KB";
		}
			
		$sPercentBandwidth = round(((100 / ($sVPS->sBandwidthLimit * 1024 * 1024)) * $sVPS->sBandwidthUsage), 0);
		if(empty($sPercentBandwidth)){
			$sPercentBandwidth = 0;
		}
		
		$sMac = explode(",", $sVPS->sMac);
		$sIPList = VPS::list_ipspace($sVPS);
		
		$sStatistics = array("info" => array("ram" => $sVPS->sRAM, 
											"disk" => $sVPS->sDisk, 
											"cpulimit" => $sVPS->sCPULimit,
											"bandwidth_usage" => $sBandwidthUsage,
											"bandwidth_limit" => FormatBytes($sVPS->sBandwidthLimit * 1024, 0),
											"percent_bandwidth" => round(((100 / ($sVPS->sBandwidthLimit * 1024)) * $sVPS->sBandwidthUsage), 0),
											"template" => $sTemplateName,
											"hostname" => $sVPS->sHostname,
											"primary_ip" => $sVPS->sPrimaryIP,
											"gateway" => $sIPList[0]["gateway"],
											"netmask" => $sIPList[0]["netmask"],
											"mac" => $sMac[0],
											"iso_sync" => $sISOSync,
											"sync_error" => $sSyncError,
											"percent_sync" => $sPercentSync,
							));
		$sStatistics = Templater::AdvancedParse($sTemplate->sValue.'/'.$sVPS->sType.'.statistics', $locale->strings, array("Statistics" => $sStatistics));
		if(strpos($sLog[0]["result"], 'running') === false) {
			return $sArray = array("json" => 1, "type" => "status", "result" => "offline", "hostname" => $sVPS->sHostname, "content" => $sStatistics);
		} else {
			if($sVPS->sISOSyncing == 1){
					$sVPS->uISOSyncing = 0;
					$sVPS->InsertIntoDatabase();
			}
			return $sArray = array("json" => 1, "type" => "status", "result" => "online", "hostname" => $sVPS->sHostname, "content" => $sStatistics);
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
		$sUpdate = $this->kvm_config($sUser, $sVPS, $sRequested);
		$sUpdate = $this->kvm_dhcp($sUser, $sVPS, $sRequested);
		return $sArray = array("json" => 1, "type" => "success", "result" => "Primary IP changed to {$sIP->sIPAddress}");
	}
	
	public function database_kvm_suspend($sUser, $sVPS, $sRequested){
		$sUserPermissions = $sUser->sPermissions;
		if($sUserPermissions == 7){
			$sVPS->uSuspended = 1;
			$sVPS->uSuspendingAdmin = $sUser->sId;
			$sVPS->InsertIntoDatabase();
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Permissions invalid for selected action.");
		}
	}
	
	public function kvm_suspend($sUser, $sVPS, $sRequested){
		$sUserPermissions = $sUser->sPermissions;
		if($sUserPermissions == 7){
			$sServer = new Server($sVPS->sServerId);
			$sSSH = Server::server_connect($sServer);
			$sLog[] = array("command" => "virsh destroy kvm{$sVPS->sContainerId}", "result" => $sSSH->exec("virsh destroy kvm{$sVPS->sContainerId}"));
			$sLog[] = array("command" => "mv /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml /var/feathur/configs/kvm{$sVPS->sContainerId}-vps-suspended.xml", "result" => $sSSH->exec("mv /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml /var/feathur/configs/kvm{$sVPS->sContainerId}-vps-suspended.xml"));
			$sLog[] = array("command" => "virsh autostart --disabled kvm{$sVPS->sContainerId}", "result" => $sSSH->exec("virsh autostart --disabled kvm{$sVPS->sContainerId}"));
			$sSave = VPS::save_vps_logs($sLog, $sVPS);
			return $sArray = array("json" => 1, "type" => "success", "result" => "User's VPS has been suspended!", "reload" => 1);
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "You might want to try a different profession.", "reload" => 1);
		}
	}
	
	public function database_kvm_unsuspend($sUser, $sVPS, $sRequested){
		$sUserPermissions = $sUser->sPermissions;
		if($sUserPermissions == 7){
			$sVPS->uSuspended = 0;
			$sVPS->uSuspendingAdmin = 0;
			$sVPS->InsertIntoDatabase();
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Permissions invalid for selected action.");
		}
	}
	
	public function kvm_unsuspend($sUser, $sVPS, $sRequested){
		$sUserPermissions = $sUser->sPermissions;
		if($sUserPermissions == 7){
			$sServer = new Server($sVPS->sServerId);
			$sSSH = Server::server_connect($sServer);
			$sLog[] = array("command" => "mv /var/feathur/configs/kvm{$sVPS->sContainerId}-vps-suspended.xml /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml", "result" => $sSSH->exec("mv /var/feathur/configs/kvm{$sVPS->sContainerId}-vps-suspended.xml /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml"));
			$sLog[] = array("command" => "virsh create /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml;", "result" => $sSSH->exec("virsh create /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml;"));
			$sLog[] = array("command" => "virsh autostart kvm{sVPS->sContainerId}", "result" => $sSSH->exec("virsh autostart kvm{$sVPS->sContainerId}"));
			$sSave = VPS::save_vps_logs($sLog, $sVPS);
			return $sArray = array("json" => 1, "type" => "success", "result" => "User's VPS has been unsuspended!", "reload" => 1);
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "You might want to try a different profession.", "reload" => 1);
		}
	}
	
	public function kvm_dhcp($sUser, $sVPS, $sRequested){
		$sIPList = VPS::list_ipspace($sVPS);
		$sIPCount = count($sIPList);
		$sServer = new Server($sVPS->sServerId);
		$sSSH = Server::server_connect($sServer);
		$sMacList = explode(",", $sVPS->sMac);
		$sCurrent = 0;
		if($sIPCount >= 1){
			foreach($sIPList as $sKey => $sValue){
				$sBlock = new Block($sValue["block"]);
				$sDHCP .= "host kvm{$sVPS->sContainerId}.{$sCurrent} { hardware ethernet {$sMacList[$sCurrent]}; option routers {$sBlock->sGateway}; option subnet-mask {$sBlock->sNetmask}; fixed-address {$sValue["ip"]}; option domain-name-servers {$sVPS->sNameserver}; } ";
				$sCurrent++;
			}
			$sDHCP = escapeshellarg($sDHCP);
		} else {
			$sDHCP = "''";
		}
		
		$sCommandList .= "echo {$sDHCP} > /var/feathur/configs/kvm{$sVPS->sContainerId}-dhcp.conf;cat /var/feathur/configs/dhcpd.head /var/feathur/configs/*-dhcp.conf > /etc/dhcp/dhcpd.conf;service isc-dhcp-server restart;";
		
		$sLog[] = array("command" => $sCommandList, "result" => $sSSH->exec($sCommandList));
		$sSave = VPS::save_vps_logs($sLog, $sVPS);
	}
	
	public function database_kvm_addip($sUser, $sVPS, $sRequested){
		$sUserPermissions = $sUser->sPermissions;
		if($sUserPermissions == 7){
			$sIPs = $sRequested["GET"]["ip"];
			global $database;
			if($sBlocks = $database->CachedQuery("SELECT * FROM server_blocks WHERE `server_id` = :ServerId AND `ipv6` = 0", array('ServerId' => $sVPS->sServerId))){
				foreach($sBlocks->data as $key => $value){
					if($sIP = $database->CachedQuery("SELECT * FROM ipaddresses WHERE `block_id` = :BlockId AND `vps_id` = 0", array('BlockId' => $value["block_id"]))){
						foreach($sIP->data as $subvalue){
							if($sCurrentIPs < $sIPs){
								$sUpdate = $database->CachedQuery("UPDATE ipaddresses SET `vps_id` = :VPSId WHERE `id` = :Id", array('VPSId' => $sVPS->sId, 'Id' => $subvalue["id"]));
								if((empty($sVPS->sPrimaryIP)) || (!$sData = $database->CachedQuery("SELECT * FROM ipaddresses WHERE `vps_id` = :VPSId AND `ip_address` = :PrimaryIP", array('VPSId' => $sVPS->sId, 'PrimaryIP' => $sVPS->sPrimaryIP)))){
									$sVPS->uPrimaryIP = $subvalue["ip_address"];
									$sVPS->InsertIntoDatabase();
								}
								$sMacTotal = count(explode(",", $sVPS->sMac));
								$sIPList = VPS::list_ipspace($sVPS);
								$sIPCount = count($sIPList);
								if($sIPCount > $sMacTotal){
									$sMac = generate_mac();
									$sCurrentMac = $sVPS->sMac;
									if(empty($sCurrentMac)){
										$sVPS->uMac = $sMac;
									} else {
										$sVPS->uMac = $sVPS->sMac.",".$sMac;
									}
									$sVPS->InsertIntoDatabase();
								}
								$sCurrentIPs++;
							}
						}																					
					}
				}
				if(empty($sCurrentIPs)){
					return $sArray = array("json" => 1, "type" => "error", "result" => "Unfortunatly there are 0 free IPs!");
				}
				return true;
			} else {
				return $sResult = array("result" => "Unfortunatly there are no blocks assigned to this server?", "json" => 1, "type" => "error");
			}
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Insufficient permissions for this action.");
		}
	}
	
	public function kvm_addip($sUser, $sVPS, $sRequested){
		$sUserPermissions = $sUser->sPermissions;
		if($sUserPermissions == 7){
			global $database;
			$sIPs = $database->CachedQuery("SELECT * FROM ipaddresses WHERE `vps_id` = :VPSId", array('VPSId' => $sVPS->sId));
			$sTotal = count($sIPs->data);
			$sCreate = $this->kvm_config($sUser, $sVPS, $sRequested);
			$sReload = $this->kvm_dhcp($sUser, $sVPS, $sRequested);
			return $sArray = array("json" => 1, "type" => "success", "result" => "VPS now has: {$sTotal} IPv4", "reload" => 1);
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Insufficient permissions for this action.");
		}
	}
	
	public function database_kvm_update($sUser, $sVPS, $sRequested){
		$sUserPermissions = $sUser->sPermissions;
		global $sRequested;
		if($sUserPermissions == 7){
			if((ctype_digit($sRequested["GET"]["ram"])) && (!empty($sRequested["GET"]["ram"]))) {
				if((ctype_digit($sRequested["GET"]["disk"])) && (!empty($sRequested["GET"]["disk"]))) {
					if($sRequested["GET"]["disk"] >= $sVPS->sDisk){
						if((ctype_digit($sRequested["GET"]["cpulimit"])) && (!empty($sRequested["GET"]["cpulimit"]))) {
							if((ctype_digit($sRequested["GET"]["bandwidth"])) && (!empty($sRequested["GET"]["bandwidth"]))) {
								if($sRequested["GET"]["disk"] > $sVPS->sDisk){
									$sRequested["GET"]["diskchanged"] = 1;
								}
								$sIPv6Allowed = $sRequested["GET"]["ipv6allowed"];
								$sVPS->uRAM = $sRequested["GET"]["ram"];
								$sVPS->uDisk = $sRequested["GET"]["disk"];
								$sVPS->uCPULimit = $sRequested["GET"]["cpulimit"];
								$sVPS->uBandwidthLimit = $sRequested["GET"]["bandwidth"];
								$sVPS->uIPv6 = $sIPv6Allowed;
								$sVPS->InsertIntoDatabase();
								return true;
							} else {
								return $sArray = array("json" => 1, "type" => "error", "result" => "Bandwidth limit must be greater than 0.");
							}
						} else {
							return $sArray = array("json" => 1, "type" => "error", "result" => "CPU Limit must be greater than 0.");
						}
					} else {
						return $sArray = array("json" => 1, "type" => "error", "result" => "Disk space can not be decreased!");
					}
				} else {
					return $sArray = array("json" => 1, "type" => "error", "result" => "Disk must be a number greater than 0.");
				}
			} else {
				return $sArray = array("json" => 1, "type" => "error", "result" => "RAM must be a number greater than 0.");
			}
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Insufficient permissions for this action.");
		}
	}
	
	public function kvm_update($sUser, $sVPS, $sRequested){
		$sUserPermissions = $sUser->sPermissions;
		if($sUserPermissions == 7){
			$sUpdate = $this->kvm_config($sUser, $sVPS, $sRequested);
			return $sArray = array("json" => 1, "type" => "success", "result" => "VPS specifications updated. Reboot required.");
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Insufficient permissions for this action.");
		}
	}
	
	public function database_kvm_removeip($sUser, $sVPS, $sRequested){
		$sUserPermissions = $sUser->sPermissions;
		global $database;
		if($sUserPermissions == 7){
			$sRemove = $sRequested["GET"]["ip"];
			if($sIP = $database->CachedQuery("SELECT * FROM ipaddresses WHERE `id` = :IPId AND `vps_id` = :VPSId", array('IPId' => $sRemove, 'VPSId' => $sVPS->sId))){
				if($sVPS->sPrimaryIP == $sIP->data[0]["id"]){
					$sVPS->sPrimaryIP = "";
					$sVPS->InsertIntoDatabase();
				}
				$sUpdateIPs = $database->CachedQuery("UPDATE ipaddresses SET `vps_id` = 0 WHERE `id` = :IPId", array('IPId' => $sIP->data[0]["id"]));
				return true;
			} else {
				return $sArray = array("json" => 1, "type" => "error", "result" => "That IP is not assigned to this VPS.");
			}
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Insufficient permissions for this action.");
		}
	}
	
	public function kvm_removeip($sUser, $sVPS, $sRequested){
		$sUserPermissions = $sUser->sPermissions;
		if($sUserPermissions == 7){
			$sReload = $this->kvm_dhcp($sUser, $sVPS, $sRequested);
			return $sArray = array("json" => 1, "type" => "success", "result" => "IP Address sucsessfully removed.", "reload" => 1);
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Insufficient permissions for this action.");
		}
	}
	
	public function database_kvm_assignip($sUser, $sVPS, $sRequested){
		$sUserPermissions = $sUser->sPermissions;
		$sVPSUser = new User($sVPS->sUserId);
		if($sUserPermissions == 7){
			global $database;
			$sIP = $sRequested["GET"]["ip"];
			$sCheckExisting = $database->CachedQuery("SELECT * FROM ipaddresses WHERE `ip_address` = :IPAddress", array('IPAddress' => $sIP));
			if(!empty($sCheckExisting)){
				foreach($sCheckExisting->data as $value){
					if(empty($value["vps_id"])){
						$sIPList[] = array("id" => $value["id"], "ip_address" => $value["ip_address"]);
					}
					$sTotalIPs++;
				}
			}
				
			$sAvailableIPs = count($sIPList);
			if($sAvailableIPs == 1){
				$sUpdate = $database->CachedQuery("UPDATE ipaddresses SET `vps_id` = :VPSId WHERE `id` = :Id", array('VPSId' => $sVPS->sId, 'Id' => $sIPList[0]["id"]));
				return true;
			} elseif((empty($sTotalIPs)) && (empty($sAvailableIPs))){
				$sIPExpand = explode(".", $sIP);
				if($sFindBlock = $database->CachedQuery("SELECT * FROM ipaddresses WHERE `ip_address` LIKE :IPAddress", array('IPAddress' => "%{$sIPExpand[0]}.{$sIPExpand[1]}.{$sIPExpand[2]}%"))){
					$sIPAdd = new IP(0);
					$sIPAdd->uIPAddress = $sIP;
					$sIPAdd->uVPSId = $sVPS->sId;
					$sIPAdd->uBlockId = $sFindBlock->data[0]["block_id"];
					$sIPAdd->InsertIntoDatabase();
					return true;
				} else {
					$sIPAdd = new IP(0);
					$sIPAdd->uIPAddress = $sIP;
					$sIPAdd->uVPSId = $sVPS->sId;
					$sIPAdd->uBlockId = 0;
					$sIPAdd->InsertIntoDatabase();
					return true;
				}
			} elseif((!empty($sTotalIPs)) && ($sAvailableIPs != 1)){
				return $sArray = array("json" => 1, "type" => "error", "result" => "The IP you gave is assigned to a VPS!");
			} else {
				return $sArray = array("json" => 1, "type" => "error", "result" => "An unknown error occured!");
			}
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Insufficient permissions for this action.");
		}
	}
	
	public function kvm_assignip($sUser, $sVPS, $sRequested){
		$sUserPermissions = $sUser->sPermissions;
		if($sUserPermissions == 7){
			$sReload = $this->kvm_dhcp($sUser, $sVPS, $sRequested);
			return $sArray = array("json" => 1, "type" => "success", "result" => "IP {$sRequested["GET"]["ip"]} assigned to VPS!", "reload" => 1);
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Insufficient permissions for this action.");
		}
	}
	
	public function database_kvm_terminate($sUser, $sVPS, $sRequested){
		global $database;
		$sUserPermissions = $sUser->sPermissions;
		if($sUserPermissions == 7){
			$sServer = new Server($sVPS->sServerId);
			$sSSH = Server::server_connect($sServer);
			$sCommandList .= "virsh autostart --disabled kvm{$sVPS->sContainerId}}";
			$sCommandList .= "virsh destroy kvm{$sVPS->sContainerId};";
			$sCommandList .= "rm -rf /var/feathur/configs/kvm{$sVPS->sContainerId}-vps.xml;rm -rf /var/feathur/configs/kvm{$sVPS->sContainerId}-dhcp.conf;";
			$sCommandList .= "cat /var/feathur/configs/dhcpd.head /var/feathur/configs/*-dhcp.conf > /etc/dhcp/dhcpd.conf;service isc-dhcp-server restart;";
			$sCommandList .= "dd if=/dev/zero of=/dev/{$sServer->sVolumeGroup}/kvm{$sVPS->sContainerId}_img;";
			$sCommandList .= "lvremove -f {$sServer->sVolumeGroup}/kvm{$sVPS->sContainerId}_img;exit;";
			$sCommandList = escapeshellarg($sCommandList);
			$sLog[] = array("command" => "VPS Termination via Screen", "result" => $sSSH->exec("screen -dm -S {$sVPS->sContainerId} bash -c {$sCommandList};"));
			$sSave = VPS::save_vps_logs($sLog, $sVPS);
			$sIPList = VPS::list_ipspace($sVPS);
			foreach($sIPList as $sIPData){
				$sCleanUP = RDNS::add_rdns($sIPData["ip"], ' ');
			}
			$sTerminate = $database->CachedQuery("DELETE FROM vps WHERE `id` = :VPSId", array('VPSId' => $sVPS->sId));
			$sCleanIPs = $database->CachedQuery("UPDATE ipaddresses SET `vps_id` = 0 WHERE `vps_id` = :VPSId", array('VPSId' => $sVPS->sId));
			return $sArray = array("json" => 1, "type" => "error", "result" => "This VPS has been terminated.", "reload" => 1);
		} else {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Insufficient permissions for this action.", "reload" => 1);
		}
	}
	
	public function kvm_terminate($sUser, $sVPS, $sRequested){
		return true;
	}
	
	public function database_kvm_changenic($sUser, $sVPS, $sRequested){
		$sNICList = array("rtl8139", "e1000", "virtio", "ne2k_pci", "pcnet");
		foreach($sNICList as $sNIC){
			if($sNIC == $sRequested["GET"]["nic"]){
				$sVPS->uNetworkDriver = $sNIC;
				$sVPS->InsertIntoDatabase();
				$sUpdated = 1;
			}
		}
		
		if($sUpdated == 1){
			$sUpdate = $this->kvm_config($sUser, $sVPS, $sRequested);
			return $sArray = array("json" => 1, "type" => "success", "result" => "Network card has been updated, reboot required.");
		}
		
		return $sArray = array("json" => 1, "type" => "error", "result" => "No network card matching that name found.", "reload" => 1);
	}
	
	public function kvm_changenic($sUser, $sVPS, $sRequested){
		return true;
	}
	
	public function database_kvm_changedisk($sUser, $sVPS, $sRequested){
		$sDiskList = array("ide", "scsi", "virtio");
		foreach($sDiskList as $sDisk){
			if($sDisk == $sRequested["GET"]["disk"]){
				$sVPS->uDiskDriver = $sDisk;
				$sVPS->InsertIntoDatabase();
				$sUpdated = 1;
			}
		}
		
		if($sUpdated == 1){
			$sUpdate = $this->kvm_config($sUser, $sVPS, $sRequested);
			return $sArray = array("json" => 1, "type" => "success", "result" => "Disk driver has been updated, reboot required.");
		}
		
		return $sArray = array("json" => 1, "type" => "error", "result" => "No disk driver matching that name found.", "reload" => 1);
	}
	
	public function kvm_changedisk($sUser, $sVPS, $sRequested){
		return true;
	}
}
