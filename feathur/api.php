<?php
include('./includes/loader.php');

if((!empty($_POST['email'])) && (!empty($_POST['password']))){
	$sUser = User::login($_POST['email'], $_POST['password'], 1);
	if(is_array($sUser)){
		echo json_encode($sUser);
		die();
	}
} else {
	echo json_encode(array("json" => 1, "type" => "result", "result" => "The username and password can not be blank!"));
	die();
}

$sAction = $_POST['action'];

if(empty($sAction)){
	echo json_encode(array("json" => 1, "type" => "result", "result" => "You must submit an action!"));
	die();
}

if($sUser->sPermissions == 7){
	if($sAction == 'listservers'){
		echo json_encode(VPS::array_servers());
		die();
	}
	
	if($sAction == 'createvps'){
		
		// Get server information
		if(!is_numeric($sRequested["POST"]["server"])){
			if($sServers = $database->CachedQuery("SELECT * FROM servers WHERE `ip_address` = :ServerIP", array(':ServerIP' => $_POST['server']))){
				$sServer = new Server($sServers->data[0]["id"]);
				$sRequested["POST"]["server"] = $sServers->data[0]["id"];
			} else {
				echo json_encode(array("result" => "Unfortunatly no server matches your query."));
				die();
			}
		}
		
		// Get user information
		if($sCheckUsers = $database->CachedQuery("SELECT * FROM accounts WHERE `email_address` = :UserEmail", array(':UserEmail' => $_POST['useremail']))){
			$sActionUser = new User($sCheckUsers->data[0]["id"]);
			$sRequested["POST"]["user"] = $sActionUser->sId;
		} else {
			$sActionUser = User::generate_user($_POST['useremail'], $_POST['username'], 1);
			if(is_array($sActionUser)){
				echo json_encode($sActionUser);
				die();
			}
			$sRequested["POST"]["user"] = $sActionUser->sId;
		}
		
		// Get template info.
		if($sTemplates = $database->CachedQuery("SELECT * FROM templates WHERE `name` = :Template AND `type` = :Type", array(':Template' => $_POST['template'], ':Type' => $sServer->sType))){
			$sRequested["POST"]["template"] = $sTemplates->data[0]["id"];
		} else {
			if($sTemplates = $database->CachedQuery("SELECT * FROM templates WHERE `type` = :Type ORDER BY id ASC", array(':Type' => $sServer->sType))){
				$sRequested["POST"]["template"] = $sTemplates->data[0]["id"];
			} else {
				echo json_encode(array("result" => "Unfortunatly no templates exist, vps creation failed!"));
				die();
			}
		}
		
		$sServerType = new $sServer->sType;
		$sMethod = "database_{$sServer->sType}_create";
		$sSecond = "{$sServer->sType}_create";
		$sCreate = $sServerType->$sMethod($sUser, $sRequested);
		if(is_array($sCreate)){
			echo json_encode($sCreate);
			die();
		}
		$sFinish = $sServerType->$sSecond($sUser, $sRequested);
		if(is_array($sFinish)){
			echo json_encode($sFinish);
			die();
		}
	}
	
	if($sAction == 'terminatevps'){
		if($sCheckUser = $database->CachedQuery("SELECT * FROM accounts WHERE `email_address` = :UserEmail", array(':UserEmail' => $_POST['useremail']))){
			$sActionUser = new User($sCheckUser->data[0]["id"]);
			if($sVPS = $database->CachedQuery("SELECT * FROM vps WHERE `user_id` = :UserId AND `id` = :Id", array(':UserId' => $sActionUser->sId, ':Id' => $_POST['vpsid']))){
				$sVPS = new VPS($sVPS->data["0"]["id"]);
				$sServer = new Server($sVPS->sServerId);
				$sServerType = new $sServer->sType;
				$sMethod = "database_{$sServer->sType}_terminate";
				$sSecond = "{$sServer->sType}_terminate";
				$sTerminate = $sServerType->$sMethod($sUser, $sVPS, $sRequested);
				if(is_array($sTerminate)){
					echo json_encode($sTerminate);
					die();
				}
				$sFinish = $sServerType->$sSecond($sUser, $sVPS, $sRequested);
				if(is_array($sFinish)){
					echo json_encode($sFinish);
					die();
				}
			} else {
				echo json_encode(array("result" => "The VPS Id is either invalid or does not belong to this user."));
				die();
			}
		} else {
			echo json_encode(array("result" => "Invalid user email, manual termination required."));
			die();
		}
	}
	
	if($sAction == 'suspendvps'){
		if($sCheckUser = $database->CachedQuery("SELECT * FROM accounts WHERE `email_address` = :UserEmail", array(':UserEmail' => $_POST['useremail']))){
			$sActionUser = new User($sCheckUser->data[0]["id"]);
			if($sVPS = $database->CachedQuery("SELECT * FROM vps WHERE `user_id` = :UserId AND `id` = :Id", array(':UserId' => $sActionUser->sId, ':Id' => $_POST['vpsid']))){
				$sVPS = new VPS($sVPS->data["0"]["id"]);
				$sServer = new Server($sVPS->sServerId);
				$sServerType = new $sServer->sType;
				$sMethod = "database_{$sServer->sType}_suspend";
				$sSecond = "{$sServer->sType}_suspend";
				$sSuspend = $sServerType->$sMethod($sUser, $sVPS, $sRequested);
				if(is_array($sSuspend)){
					echo json_encode($sSuspend);
					die();
				}
				$sFinish = $sServerType->$sSecond($sUser, $sVPS, $sRequested);
				if(is_array($sFinish)){
					echo json_encode($sFinish);
					die();
				}
			} else {
				echo json_encode(array("result" => "The VPS Id is either invalid or does not belong to this user."));
				die();
			}
		} else {
			echo json_encode(array("result" => "Invalid user email, manual suspension required."));
			die();
		}
	}
	
	if($sAction == 'unsuspendvps'){
		if($sCheckUser = $database->CachedQuery("SELECT * FROM accounts WHERE `email_address` = :UserEmail", array(':UserEmail' => $_POST['useremail']))){
			$sActionUser = new User($sCheckUser->data[0]["id"]);
			if($sVPS = $database->CachedQuery("SELECT * FROM vps WHERE `user_id` = :UserId AND `id` = :Id", array(':UserId' => $sActionUser->sId, ':Id' => $_POST['vpsid']))){
				$sVPS = new VPS($sVPS->data["0"]["id"]);
				$sServer = new Server($sVPS->sServerId);
				$sServerType = new $sServer->sType;
				$sMethod = "database_{$sServer->sType}_unsuspend";
				$sSecond = "{$sServer->sType}_unsuspend";
				$sUnsuspend = $sServerType->$sMethod($sUser, $sVPS, $sRequested);
				if(is_array($sUnsuspend)){
					echo json_encode($sUnsuspend);
					die();
				}
				$sFinish = $sServerType->$sSecond($sUser, $sVPS, $sRequested);
				if(is_array($sFinish)){
					echo json_encode($sFinish);
					die();
				}
			} else {
				echo json_encode(array("result" => "The VPS Id is either invalid or does not belong to this user."));
				die();
			}
		} else {
			echo json_encode(array("result" => "Invalid user email, manual unsuspension required."));
			die();
		}
	}
	
} else {

}

