<?php

/*
 * See if we're running as CLI
 */

if(php_sapi_name() != 'cli') die('Unfortunately this script must be executed via CLI.');

/*
 * Stage execution
 */

chdir('/var/feathur/feathur/');
require_once('./includes/loader.php');
error_reporting(E_ALL ^ E_NOTICE);
$sTime = time();

/*
 * Set PHP limits
 */

@set_time_limit(6000);
@ini_set('memory_limit','512M');

/*
 * Attempt SSH connection
 */

$sLocalSSH = new Net_SSH2('127.0.0.1');
$sLocalKey = new Crypt_RSA();
$sLocalKey->loadKey(file_get_contents($cphp_config->settings->rootkey));
if (! $sLocalSSH->login("root", $sLocalKey)) die("Cannot connect to this server, check local key.");

/*
 * Clean up screen sessions
 */

$sClean = $sLocalSSH->exec("killall --older-than 10m screen");

/*
 * Verify templates
 */

echo "Begining template validity checks...\n";
$sTemplateSync	= Core::GetSetting('last_template_sync');
$sBefore		= time() - 900;
$sTemplateSync	= $sTemplateSync->sValue;
$sTimestamp		= time();

if ($sTemplateSync < $sBefore)
{

  if ($sTemplateList = $database->CachedQuery('SELECT * FROM `templates` WHERE `disabled` < 2', array()))
  {

    foreach ($sTemplateList->data as $sTemplate)
	{
	  $sTemplate = new Template($sTemplate['id']);

	  try {
		$sTemplateData = array_change_key_case(get_headers($sTemplate->sURL, TRUE));
		if (!isset($sTemplateData['content-length']) || empty($sTemplateData['content-length']))
		{
		  throw new Exception("ISO {$sTemplate->sURL} is invalid\n");
		}
	  } catch (Exception $e) {
		$sDisable = 1;
	  }

	  if (($sTemplate->sDisabled == 0) && ($sDisable == 1))
	  {
		$sTemplate->uDisabled = 1;
		$sTemplate->InsertIntoDatabase();
	  } else {
		$sTemplate->uDisabled = 0;
		$sTemplate->InsertIntoDatabase();
	  }

	  unset($sDisable);
	}
  }
  $sUpdateTimestamp = Core::UpdateSetting('last_template_sync', time());
} else {
  echo "Template URLs have been checked recently, skipping...\n";
}

/*
 * Server status tracker
 */

if ($sServerList = $database->CachedQuery('SELECT * FROM servers', array()))
{
  foreach($sServerList->data as $sServer)
  {
	if ($sTotal == 5)
	{
	  $sLocalSSH->exec($sCommandList);
	  unset($sCommandList);
	  unset($sTotal);
	  echo "Dispatched 5 uptime checkers...\n";
	  sleep(5);
	}

	$sServer = new Server($sServer['id']);
	$sCommandList .= "screen -dm -S uptracker bash -c 'cd /var/feathur/feathur/scripts/;php pull_server.php {$sServer->sId} >> /var/feathur/data/status.log;exit;';";
	$sTotal++;

	$sBefore = time() - 300;
	$sUptime = $sServer->sLastCheck;
	$sStatus = $sServer->sStatus;
	if (($sBefore > $sUptime) && ($sStatus === true))
	{
	  $sStatusWarning = $sServer->sStatusWarning;
	  if ($sStatusWarning === false)
	  {
		if ($sAdminList = $database->CachedQuery('SELECT * FROM `accounts` WHERE `permissions` = :Permissions', array('Permissions' => 7)))
		{
		  foreach ($sAdminList->data as $sAdmin)
		  {
			$sVariable = array('server' => $sServer->sName);
			// Disabled temporarily.
			//$sAlert = Core::SendEmail($sAdmin['email_address'], "Server Down: {$sServer->sName} - To {$sAdmin['email_address']}", 'down', $sVariable);
		  }
		}
		$sServer->uStatusWarning = true;
		echo "Dispatched outage notification email for Server {$sServer->sName}\n";
	  }
	  $sServer->uStatus = false;
	  $sServer->InsertIntoDatabase();
	}
  }

  $sLocalSSH->exec($sCommandList);
  unset($sCommandList);
  unset($sTotal);
  echo "Done\n";
}

/*
 * Clean up old statistics
 */

$sOldStatistics = time() - 432000;
$sStatistics = $database->prepare('DELETE FROM `statistics` WHERE timestamp < :OldStatistics');
$sStatistics->bindParam(':OldStatistics', $sOldStatistics, PDO::PARAM_INT);
$sStatistics->execute();

/*
 * Clean up old history
 */

$sOldHistory = time() - 604800;
$sHistory = $database->prepare('DELETE FROM `history` WHERE timestamp < :OldHistory');
$sHistory->bindParam(':OldHistory', $sOldHistory, PDO::PARAM_INT);
$sHistory->execute();

/*
 * Reset bandwidth if today is the first day of the month, and the last reset was >7 days ago
 */

$sLastReset	= Core::GetSetting('bandwidth_timestamp');
$sTimeAgo	= time() - 604800;
$sDayToday	= date('j');
if (($sLastReset->sValue < $sTimeAgo) && ($sDayToday == 1))
{
  $sReset = $database->prepare('UPDATE `vps` SET `bandwidth_usage` = 0');
  $sReset->execute();
  $sUpdateReset = Core::UpdateSetting('bandwidth_timestamp', time());
}

/*
 * License Check
 */

echo "License update...\n";
if ($sSlaves = $database->CachedQuery('SELECT * FROM servers', array())) $sCountSlaves = count($sSlaves->data);
$sHost	= Core::GetSetting('panel_url');
$sURL	= "http://check.feathur.com/api.php?host={$sHost->sValue}&slaves={$sCountSlaves}";
$sCurl	= curl_init();
curl_setopt($sCurl, CURLOPT_URL, $sURL);
curl_setopt($sCurl, CURLOPT_RETURNTRANSFER, 1);
$sLicense = json_decode(curl_exec($sCurl), true);
curl_close($sCurl);
Core::UpdateSetting('license', ($sLicense['type']=='success' ? 1 : 0));


/*
 * Update check
 */

echo "Dispatching update check...\n";
$sAutomaticUpdates	= Core::GetSetting('automatic_updates');
$sAutomaticUpdates	= $sAutomaticUpdates->sValue;
$sLastUpdateCheck	= Core::GetSetting('last_update_check');
$sLastUpdateCheck	= $sLastUpdateCheck->sValue;
$sTimeAgo			= time - 900;
if ($sLastUpdateCheck < $sTimeAgo)
{
  if ($sAutomaticUpdates == 1)
  {
	$sSSH = new Net_SSH2('127.0.0.1');
	$sKey = new Crypt_RSA();
	$sKey->loadKey(file_get_contents($cphp_config->settings->rootkey));
	if ($sSSH->login("root", $sKey))
	{
	  $sSSH->exec('cd /var/feathur/; git pull; cd /var/feathur/feathur/; php -f /var/feathur/feathur/update.php; rm -f /var/feathur/feathur/update.php');
	  $sVersion = $sSSH->exec('cat /var/feathur/version.txt');
	  $sLastUpdate = Core::UpdateSetting('last_update_check', time());
	  $sNewVersion = Core::UpdateSetting('current_version', $sVersion);
	}
  }
}

/*
 * Release lock files
 */

$sLock = $sLocalSSH->exec("rm -rf /var/feathur/data/bandwidth.lock");

