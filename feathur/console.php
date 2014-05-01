<?php
require_once('./includes/loader.php');

$sId		= abs((int) $_GET['id']);
$sAction	= preg_replace('/[^\w\d]/', '', $_GET['action']);
$iPort		= abs((int) $_POST['port']);
$sHostname	= preg_replace('/[^\w\d\.\-]/', '', $_POST['hostname']);

/*
 * Redirect to index if user is not logged in
 */

if (empty($sUser)) die(header("Location: index.php", 401));

/*
 * If a VPS is not being viewed, redirect to main page
 */

if (empty($sId)) die(header("Location: main.php", 301));

/*
 * Redirect if user is not viewing their VPS, or user is not an admin
 */

$sVPS = new VPS($sId);
if (($sVPS->sUserId != $sUser->sId) && ($sUser->sPermissions != 7)) die(header("Location: main.php", 401));

/*
 * Show suspension page if user's VPS is suspended
 */

if (($sVPS->sSuspended == 1) && ($sUser->sPermissions != 7))
{
  echo Templater::AdvancedParse($sTemplate->sValue.'/suspended', $locale->strings, array());
  die();
}

/*
 * Show console screen if all other conditions are met
 */

if ($sAction == 'connect')
{
  if ((!empty($sHostname)) && (!empty($iPort)))
  {
	$sView = Templater::AdvancedParse(
	           $sTemplate->sValue.'/console',
			   $locale->strings,
			   array(
			     'connect' => 1,
				 'VPS' => array('data' => $sVPS->uData),
				 'Hostname'	=> $sHostname,
				 'Port'		=> $iPort
			   )
			 );
  } else { 
	$sView = Templater::AdvancedParse(
	           $sTemplate->sValue.'/console',
			   $locale->strings,
			   array(
			     'connect' => 0,
				 'VPS' => array("data" => $sVPS->uData)
			   )
			 );
  }
} else {
  $sView = Templater::AdvancedParse(
             $sTemplate->sValue.'/console',
			 $locale->strings,
			 array(
			   'connect' => 0,
			   'VPS' => array('data' => $sVPS->uData)
			 )
		   );
}
echo $sView;