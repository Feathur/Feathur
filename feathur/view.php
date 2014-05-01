<?php
require_once('./includes/loader.php');

/*
 * Redirect user to main page if not logged in
 */

if (!empty($sUser)) die(header("Location: main.php", 401));


/*
 * Redirect to main page if user is not looking at a VPS
 */

$sId = (isset($_GET['id']) ? preg_replace('/[^\d]/', '', $_GET['id']) : 0);
if ((empty($sId)) || (!is_numeric($sId))) die(header("Location: main.php", 403));

$sAction = preg_replace('/[^\w\d]/', '', $_GET['action']);

/*
 * Check for the existence of a VPS for currently logged in user, or existence at all if admin
 */

try {
	if ($sUser->sPermissions != 7)
	{
		$database->CachedQuery('SELECT * FROM vps WHERE `id` = :VPSId AND `user_id` = :UserId', array('VPSId' => $sId, 'UserId' => $sUser->sId));
	} else {
		$database->CachedQuery('SELECT * FROM vps WHERE `id` = :VPSId', array('VPSId' => $sId));
	}
	$sVPS = new VPS($sId);
} catch (Exception $e) {
	die(header("Location: main.php", 401));
}

/*
 * Redirect if currently logged in user does not own the VPS, and user is not an admin
 */

if (($sVPS->sUserId != $sUser->sId) && ($sUser->sPermissions != 7)) die(header("Location: main.php", 401));

/*
 * Restrict access to VPS management if VPS status is currently suspended
 */

$sSuspended = $sVPS->sSuspended;
if ((!empty($sSuspended)) && ($sUser->sPermissions != 7))
{
  if ($sSuspended == 2)
  {
	$sUserView = Templater::AdvancedParse(
	               $sTemplate->sValue.'/abuse',
				   $locale->strings,
				   array('Hostname' => $sVPS->sHostname)
				 );
  } else {
	$sUserView = Templater::AdvancedParse(
	               $sTemplate->sValue.'/suspended',
				   $locale->strings,
				   array('Hostname' => $sVPS->sHostname)
				 );
  } 
  echo Templater::AdvancedParse(
         $sTemplate->sValue.'/master',
		 $locale->strings,
		 array(
		   'Content'	=> $sUserView,
		   'Page'		=> 'main'
		 )
	   );
  die();
}

/*
 * Restrict access to VPS management if VPS is currently being transferred
 */

if ($sTransfer = $database->CachedQuery('SELECT * FROM transfers WHERE `completed` = 0 AND `vps_id` = :VPSId', array('VPSId' => $sVPS->sId)))
{
  $sUserView = Templater::AdvancedParse(
                 $sTemplate->sValue.'/transfer',
				 $locale->strings,
				 array()
			   );
	echo Templater::AdvancedParse(
	       $sTemplate->sValue.'/master',
		   $locale->strings,
		   array(
		     'Content'	=> $sUserView,
			 'Page'		=> 'main',
			 'Errors'	=> $sErrors,
			 'VPSId'	=> $sVPS->sId
		   )
		 );
	die();
}

/*
 * Process any actions given
 */

if (!empty($sAction))
{
  @set_time_limit(15);
  $sStart = new $sVPS->sType;
  $sDBAction = "database_{$sVPS->sType}_{$sAction}";
  $sServerAction = "{$sVPS->sType}_{$sAction}";
  if (((method_exists($sStart, $sDBAction) === true)) && ((method_exists($sStart, $sServerAction) === true)))
  {
    $sDBResult = $sStart->$sDBAction($sUser, $sVPS, $sRequested);
    if (is_array($sDBResult)) die(json_encode($sDBResult));
	$sServerResult = $sStart->$sServerAction($sUser, $sVPS, $sRequested);
	if (!empty($sServerResult['json'])) die(json_encode($sServerResult));
  } else {
	die(json_encode(array('json' => 1, 'type' => 'error', 'result' => 'Invalid action requested. Please try again.', 'reload' => 1)));
  }
}

/*
 * Ensure VPS isn't currently rebuilding
 */

if ($sVPS->sRebuilding == 1)
{
  $sUserView .= Templater::AdvancedParse(
                  $sTemplate->sValue.'/rebuild',
				  $locale->strings,
				  array('VPS' => array('data' => $sVPS->uData))
				);
  echo Templater::AdvancedParse(
         $sTemplate->sValue.'/master',
		 $locale->strings,
		 array('Content' => $sUserView, 'Page' => 'main')
	   );
  die();
}

/*
 * Pull VPS owner data
 */

$sVPSOwner = new User($sVPS->sUserId);

$sUserView .= Templater::AdvancedParse(
                $sTemplate->sValue.'/'.$sVPS->sType.'.view',
				$locale->strings,
				array(
				  'VPS'				=> array('data' => $sVPS->uData),
				  'IPs' 			=> VPS::list_ipspace($sVPS),
				  'Templates'		=> VPS::list_templates($sVPS),
				  'Servers'			=> VPS::list_servers($sVPS),
				  'User'			=> array('data' => $sVPSOwner->uData),
				  'UserVPSList'		=> VPS::list_uservps($sVPS),
				  'IPv6Exist'		=> Block::ipv6_exist($sVPS),
				  'UserIPv6Block'	=> Block::vps_ipv6_block($sVPS)
				)
			  );

/*
 * Display user view template
 */

echo Templater::AdvancedParse(
       $sTemplate->sValue.'/master',
	   $locale->strings,
	   array('Content' => $sUserView, 'Page' => 'main')
	 );
die();