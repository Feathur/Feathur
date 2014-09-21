<?php

if ($sUser->sPermissions != 7) die("Sorry you've accessed our system without permission");

$sPage		= 'create';
$sPageType	= 'vps';

if ($sAction == 'load_form')
{
  $sType = $_GET['type'];
  $sTemplates = $database->CachedQuery("SELECT * FROM templates WHERE `type` = :Type", array('Type' => $sType));
  if (!empty($sTemplates))
  {
	foreach($sTemplates->data as $value)
	{
	  $sTemplateList[] = array("id" => $value["id"], "name" => $value["name"]);
	}
  }
  $sContent = Templater::AdvancedParse(
				$sAdminTemplate->sValue.'/create.'.$sType.'.form',
				$locale->strings,
				array('TemplateList' => $sTemplateList)
			  );
  $sArray = array('content' => $sContent);
  die(json_encode($sArray));
}

if ($sAction == 'create')
{
  if (is_numeric($sRequested['POST']['server']))
  {
	$sServer = new Server($sRequested['POST']['server']);
	$sServerType = new $sServer->sType;
	$sMethod = "database_{$sServer->sType}_create";
	$sSecond = "{$sServer->sType}_create";
	$sCreate = $sServerType->$sMethod($sUser, $sRequested);
	if (is_array($sCreate)) die(json_encode($sCreate));
	$sFinish = $sServerType->$sSecond($sUser, $sRequested);
	if (is_array($sFinish)) die(json_encode($sFinish));
  } else {
	die(json_encode(array("json" => 1, "type" => "error", "result" => "Server selection missing in creation call.")));
  }
}

$sUsers = $database->CachedQuery("SELECT * FROM accounts ORDER BY `email_address` ASC", array());
foreach ($sUsers->data as $value)
{
  $sUserList[] = array("id" => $value["id"], "email" => $value["email_address"]);
}

if ($sServers = $database->CachedQuery("SELECT * FROM servers", array()))
{
  foreach($sServers->data as $value)
  {
	$sServerList[] = array("id" => $value["id"], "name" => $value["name"], "type" => $value["type"]);
  }
}

$sContent = Templater::AdvancedParse(
			  $sAdminTemplate->sValue.'/createvps',
			  $locale->strings,
			  array('UserList' => $sUserList, 'ServerList' => $sServerList)
			);
