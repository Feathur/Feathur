<?php
if($sUser->sPermissions != 7) die("Sorry you've accessed our system without permission.");

$sPage		= 'update';
$sPageType	= 'settings';
$sType		= $_GET['type'];

if ($sAction == 'automatic')
{
  if($_GET['value'] == 1)
  {
	$sAutomatic = Core::UpdateSetting('automatic_updates', '1');
	$sErrors[] = array('result' => 'Automatic updates have been turned on.', 'type' => 'success');
  } elseif($_GET['value'] == 0) {
	$sAutomatic = Core::UpdateSetting('automatic_updates', '0');
	$sErrors[] = array('result' => 'Automatic updates have been turned off.', 'type' => 'success');
  }
}

if ($sAction == 'force')
{
  $sSSH = new Net_SSH2('127.0.0.1');
  $sKey = new Crypt_RSA();
  $sKey->loadKey(file_get_contents($cphp_config->settings->rootkey));
  if($sSSH->login('root', $sKey))
  {
	$sOldVersion = file_get_contents('/var/feathur/version.txt');
	$sSSH->exec("cd /var/feathur/; git reset --hard; git pull; cd /var/feathur/feathur/; php update.php; rm -rf update.php;");
	$sVersion = file_get_contents('/var/feathur/version.txt');
	$sLastUpdate = Core::UpdateSetting('last_update_check', time());
	$sCurrentVersion = file_get_contents('/var/feathur/version.txt');
	if($sCurrentVersion != $sOldVersion)
	{
	  $sErrors[] = array('result' => 'Force update completed.', 'type' => 'success');
	} elseif($sCurrentVersion->sValue == $sOldVersion->sValue) {
	  $sErrors[] = array('result' => 'Force update failed, no updates available.', 'type' => 'error');
	} else {
	  $sErrors[] = array('result' => 'Force update failed, unknown error.', 'type' => 'error');
	}
  }
  $sJson = 1;
}

$sAutomaticUpdates = Core::GetSetting('automatic_updates');
$sUpdates[] = check_updates();

$sContent .= Templater::AdvancedParse(
			   $sAdminTemplate->sValue.'/update',
			   $locale->strings,
			   array(
			     'AutomaticUpdates' => $sAutomaticUpdates->sValue,
				 'Updates' => $sUpdates,
				 'Outdated' => $sUpdates[0]['update'],
				 'Errors' => $sErrors
			   )
			 );

if ($sJson == 1) die(json_encode(array('content' => $sContent)));
unset($sErrors);