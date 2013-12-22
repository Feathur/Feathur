<?php
if($sUser->sPermissions != 7){
	die("Sorry you've accessed our system without permission");
}

if($sType == search){
	$sPage = "search";
	if(strpos($sSearch,'server=') !== false) {
		$sSearchServer = str_replace("server=", "", $sSearch);
		if(is_numeric($sSearchServer)){
			$sServers = $database->CachedQuery("SELECT * FROM servers WHERE `id` = :ServerId", array('ServerId' => $sSearchServer));
			$sVPS = $database->CachedQuery("SELECT * FROM vps WHERE `server_id` = :ServerId", array('ServerId' => $sSearchServer));
		} else {
			$sServers = $database->CachedQuery("SELECT * FROM servers WHERE (`name` LIKE :ServerId || `id` = :ServerId)", array('ServerId' => $sSearchServer));
			if(!empty($sServers)){
				foreach($sServers->data as $value){
					$sQuery = $database->CachedQuery("SELECT * FROM vps WHERE (`server_id` LIKE :ServerId)", array('ServerId' => $value["id"]));
					$sServerVPS[] = $sQuery->uData;
				}
			}
		}
	} elseif(strpos($sSearch,'user=') !== false) {
		$sSearchUser = str_replace("user=", "", $sSearch);
		if(is_numeric($sSearchUser)){
			$sUsers = $database->CachedQuery("SELECT * FROM accounts WHERE `id` = :UserId", array('UserId' => $sSearchUser));
			$sVPS = $database->CachedQuery("SELECT * FROM vps WHERE `user_id` = :UserId", array('UserId' => $sSearchUser));
			$sResultType = "numericuser";
		} else {
			$sSearchUser = "%{$sSearchUser}%";
			$sUsers = $database->CachedQuery("SELECT * FROM accounts WHERE (`username` LIKE :SearchUser || `email_address` LIKE :SearchUser)", array('SearchUser' => $sSearchUser));
			if(!empty($sUsers)){
				foreach($sUsers->data as $value){
					$sQuery = $database->CachedQuery("SELECT * FROM vps WHERE `user_id` = :UserId", array('UserId' => $value["id"]));
					$sUserVPS[] = $sQuery->uData;
				}
			}
		}
	} elseif(strpos($sSearch,'type=') !== false) {
		$sSearchType = str_replace("type=", "", $sSearch);
		$sVPS = $database->CachedQuery("SELECT * FROM vps WHERE `type` = :Type", array('Type' => $sSearchType));
	} elseif(strpos($sSearch,'suspended') !== false) {
		$sVPS = $database->CachedQuery("SELECT * FROM vps WHERE `suspended` = 1", array());
	} else {
		$sLike = "%{$sSearch}%";
		$sVPS = $database->CachedQuery("SELECT * FROM vps WHERE (`container_id` LIKE :Search || `hostname` LIKE :Search || `primary_ip` LIKE :Search || `server_id` LIKE :Search)", array('Search' => $sLike));
		$sUsers = $database->CachedQuery("SELECT * FROM accounts WHERE (`username` LIKE :Search || `email_address` LIKE :Search)", array('Search' => $sLike));
		$sServers = $database->CachedQuery("SELECT * FROM servers WHERE (`name` LIKE :ServerId || `id` = :ServerId)", array('ServerId' => $sLike));
	}
	
} elseif($sType == vps){
	$sPage = "listvps";
	$sPageType = "vps";
	$sVPS = $database->CachedQuery("SELECT * FROM vps", array());
} elseif($sType == users){
	$sPage = "listusers";
	$sPageType = "users";
	$sUsers = $database->CachedQuery("SELECT * FROM accounts", array());
} elseif($sType == servers){
	$sPage = "listservers";
	$sPageType = "servers";
	$sServers = $database->CachedQuery("SELECT * FROM servers", array());
} else {
	header("Location: admin.php");
}


if(!empty($sServers)){
	foreach($sServers->data as $value){
		$sResult[] = array("result_type" => "server", "id" => $value["id"], "name" => $value["name"], "type" => $value["type"], "ip_address" => $value["ip_address"]);
		$sServerCount++;
	}
}
if(!empty($sVPS->data)){
	foreach($sVPS->data as $value){
		if(!empty($value["user_id"])){
			$sTempUser = new User($value["user_id"]);
			$sUsername = $sTempUser->sUsername;
			$sUserId = $sTempUser->sId;
		} else {
			$sUsername = "N/A";
			$sUserId = "0";
		}
		$sResult[] = array("result_type" => "vps", "id" => $value["id"], "user_id" => $sUserId, "username" => $sUsername, "server_id" => $value["server_id"], "hostname" => $value["hostname"], "primary_ip" => $value["primary_ip"], "type" => $value["type"], "suspended" => $value["suspended"]);
		$sVPSCount++;
	}
}
	
if(!empty($sServerVPS)){
	foreach($sServerVPS as $data){
		if(!empty($data)){
			foreach($data as $value){
				$sTempUser = new User($value["user_id"]);
				$sResult[] = array("result_type" => "vps", "id" => $value["id"], "user_id" => $value["user_id"], "username" => $sTempUser->sUsername, "server_id" => $value["server_id"], "hostname" => $value["hostname"], "primary_ip" => $value["primary_ip"], "type" => $value["type"], "suspended" => $value["suspended"]);
				$sVPSCount++;
			}
		}
	}
}
	
if(!empty($sUsers)){
	foreach($sUsers->data as $value){
		$sResult[] = array("result_type" => "user", "id" => $value["id"], "username" => $value["username"], "email_address" => $value["email_address"]);
		$sUserCount++;
	}
}
	
if(!empty($sUserVPS)){
	foreach($sUserVPS as $data){
		if(!empty($data)){
			foreach($data as $value){
				$sResult[] = array("result_type" => "vps", "id" => $value["id"], "user_id" => $value["user_id"], "server_id" => $value["server_id"], "hostname" => $value["hostname"], "primary_ip" => $value["primary_ip"], "type" => $value["type"], "suspended" => $value["suspended"]);
				$sUserCount++;
			}
		}
	}
}

$sContent = Templater::AdvancedParse($sAdminTemplate->sValue.'/list', $locale->strings, array("Result" => $sResult, "ServerCount" => $sServerCount, "VPSCount" => $sVPSCount, "UserCount" => $sUserCount));