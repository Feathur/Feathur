<?php
require_once('./includes/loader.php');

/*
 * If email and password aren't empty, try to login
 */

if (!empty($_POST['email']) && !empty($_POST['password']))
{
  $sUser = User::login($_POST['email'], $_POST['password'], 1);
  if (is_array($sUser)) die(json_encode($sUser));
} else {
  die(json_encode(array('json' => 1, 'type' => 'result', 'result' => 'The username and password can not be blank!')));
}

$sAction = preg_replace('/[^\w\d]/', '', $_POST['action']);
$sEmail  = preg_replace('/[^\w\d_\-\._@+]/', '', $_POST['useremail']);
$sServerGroup = preg_replace('/[^\d\w\.\-_]/', '', $_POST['server']);
$sTemplate = preg_replace('/[^\w\d\s\-\._\(\)]/', '', $_POST['template']);
$iVPSid  = abs((int) $_POST['vpsid']);

/* 
 * Handle invalid actions
 */

if (empty($sAction)) die(json_encode(array('json' => 1, 'type' => 'result', 'result' => 'You must submit an action!')));


/*
 * If user has admin permissions
 */

if ($sUser->sPermissions == 7)
{

  /*
   *  Return a list of servers
   */
   
  if ($sAction == 'listservers') die(json_encode(VPS::array_servers()));
  if ($sAction == 'listgroups') die(json_encode(Group::array_groups()));
  /*
   *  Create a VPS
   */
   
  if ($sAction == 'createvps')
  {
	// Determine if this is a server or a group.
	// If this is numeric it's a server ID.
	if (!is_numeric($sServerGroup))
	{
	  // If the string begins with { it's a group.
	  if (strpos($sServerGroup,'Group') !== false) {
		$sCleanup = explode("-", $sServerGroup);
		$uGroupId = preg_replace("[^0-9]","",$sCleanup[0]);
		$sServer = new Server(ServerGroups::select_server($uGroupId));
	  } else {
		  // If not it's a server IP and we need to look it up.
		  $sServer = $sServerGroup;
		  if($sServers = $database->CachedQuery('SELECT * FROM servers WHERE `ip_address` = :ServerIP', array(':ServerIP' => $sServer)))
		  {
			$sServer = new Server($sServers->data[0]['id']);
			$sRequested['POST']['server'] = $sServers->data[0]['id'];
		  } else {
			die(json_encode(array('result' => 'Unfortunately no server matches your query.')));
		  }
	  }
	} else {
		$sServer = new Server($sServer);
	}
	
	if ($sCheckUsers = $database->CachedQuery('SELECT * FROM accounts WHERE `email_address` = :UserEmail', array(':UserEmail' => $sEmail)))
	{
	  $sActionUser = new User($sCheckUsers->data[0]['id']);
	  $sRequested['POST']['user'] = $sActionUser->sId;
	} else {
	  $sActionUser = User::generate_user($sEmail, $_POST['username'], 1);
	  if (is_array($sActionUser)) die(json_encode($sActionUser));
	  $sRequested['POST']['user'] = $sActionUser->sId;
	}
		
	if ($sTemplates = $database->CachedQuery('SELECT * FROM templates WHERE `name` = :Template AND `type` = :Type', array(':Template' => $sTemplate, ':Type' => $sServer->sType)))
	{
	  $sRequested['POST']['template'] = $sTemplates->data[0]['id'];
	} else {
	  if ($sTemplates = $database->CachedQuery('SELECT * FROM templates WHERE `type` = :Type ORDER BY id ASC', array(':Type' => $sServer->sType)))
	  {
		$sRequested['POST']['template'] = $sTemplates->data[0]['id'];
	  } else {
		die(json_encode(array('result' => 'Unfortunately no templates exist, VPS creation failed!')));
	  }
		
	  $sServerType = new $sServer->sType;
	  $sMethod = "database_{$sServer->sType}_create";
	  $sSecond = "{$sServer->sType}_create";
	  $sCreate = $sServerType->$sMethod($sUser, $sRequested);
	  if (is_array($sCreate)) die(json_encode($sCreate));
	  $sFinish = $sServerType->$sSecond($sUser, $sRequested);
	  if (is_array($sFinish)) die(json_encode($sFinish));
	}
  }	

  /*
   *  Terminate a VPS
   */

  if ($sAction == 'terminatevps')
  {
	if ($sCheckUser = $database->CachedQuery('SELECT * FROM accounts WHERE `email_address` = :UserEmail', array(':UserEmail' => $sEmail)))
	{
	  $sActionUser = new User($sCheckUser->data[0]['id']);
	  if ($sVPS = $database->CachedQuery('SELECT * FROM vps WHERE `user_id` = :UserId AND `id` = :Id', array(':UserId' => $sActionUser->sId, ':Id' => $iVPSid)))
	  {
		$sVPS = new VPS($sVPS->data[0]['id']);
		$sServer = new Server($sVPS->sServerId);
		$sServerType = new $sServer->sType;
		$sMethod = "database_{$sServer->sType}_terminate";
		$sSecond = "{$sServer->sType}_terminate";
		$sTerminate = $sServerType->$sMethod($sUser, $sVPS, $sRequested);
		if (is_array($sTerminate)) die(json_encode($sTerminate));
		$sFinish = $sServerType->$sSecond($sUser, $sVPS, $sRequested);
		if (is_array($sFinish)) die(json_encode($sFinish));
	  } else {
		die(json_encode(array('type' => 'success', 'result' => 'The VPS Id is either invalid or does not belong to this user.')));
	  }
	} else {
	  die(json_encode(array('result' => 'Invalid user email, manual termination required.')));
	}
  }
  
  /*
   *  Suspend a VPS
   */

  if ($sAction == 'suspendvps')
  {
	if($sCheckUser = $database->CachedQuery('SELECT * FROM accounts WHERE `email_address` = :UserEmail', array(':UserEmail' => $sEmail)))
	{
	  $sActionUser = new User($sCheckUser->data[0]['id']);
	  if ($sVPS = $database->CachedQuery('SELECT * FROM vps WHERE `user_id` = :UserId AND `id` = :Id', array(':UserId' => $sActionUser->sId, ':Id' => $iVPSid)))
	  {
		$sVPS = new VPS($sVPS->data[0]['id']);
		$sServer = new Server($sVPS->sServerId);
		$sServerType = new $sServer->sType;
		$sMethod = "database_{$sServer->sType}_suspend";
		$sSecond = "{$sServer->sType}_suspend";
		$sSuspend = $sServerType->$sMethod($sUser, $sVPS, $sRequested);
		if (is_array($sSuspend)) die(json_encode($sSuspend));
		$sFinish = $sServerType->$sSecond($sUser, $sVPS, $sRequested);
		if(is_array($sFinish)) die(json_encode($sFinish));
	  } else {
		die(json_encode(array('result' => 'The VPS Id is either invalid or does not belong to this user.')));
	  }
	} else {
	  die(json_encode(array("result" => "Invalid user email, manual suspension required.")));
	}
  }
  
  /*
   *  Unsuspend a VPS
   */

  if ($sAction == 'unsuspendvps')
  {
	if ($sCheckUser = $database->CachedQuery('SELECT * FROM accounts WHERE `email_address` = :UserEmail', array(':UserEmail' => $sEmail)))
	{
	  $sActionUser = new User($sCheckUser->data[0]['id']);
	  if ($sVPS = $database->CachedQuery("SELECT * FROM vps WHERE `user_id` = :UserId AND `id` = :Id", array(':UserId' => $sActionUser->sId, ':Id' => $iVPSid)))
	  {
	    $sVPS = new VPS($sVPS->data[0]['id']);
		$sServer = new Server($sVPS->sServerId);
		$sServerType = new $sServer->sType;
		$sMethod = "database_{$sServer->sType}_unsuspend";
		$sSecond = "{$sServer->sType}_unsuspend";
		$sUnsuspend = $sServerType->$sMethod($sUser, $sVPS, $sRequested);
		if (is_array($sUnsuspend)) die(json_encode($sUnsuspend));
		$sFinish = $sServerType->$sSecond($sUser, $sVPS, $sRequested);
		if (is_array($sFinish)) die(json_encode($sFinish));
	  } else {
		die(json_encode(array('result' => 'The VPS Id is either invalid or does not belong to this user.')));
	  }
	} else {
	  die(json_encode(array('result' => 'Invalid user email, manual unsuspension required.')));
	}
  }
} else {

  /*
   *  Generic failure message
   */

  die(json_encode(array('result' => 'API access denied.')));
}