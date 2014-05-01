<?php
require_once('./includes/loader.php');

/*
 * Redirect user to main page if not logged in
 */

if (empty($sUser)) die(header("Location: index.php", 401));

/*
 * Redirect to admin page if currently logged in user has admin privileges and entry wasn't forced
 */

if (($sUser->sPermissions == 7) && (empty($_GET['force']))) die(header("Location: admin.php", 301));

/*
 * Pull a list of currently logged in user's VPSes
 */

$sPullVPS = $database->CachedQuery('SELECT * FROM vps WHERE `user_id` = :UserId', array('UserId' => $sUser->sId));

/*
 * Throw error if there are no VPS instances for the currently logged in user
 */

if (empty($sPullVPS)) $sErrors[] = array('red' => 'You currently do not have any VPS. Please contact our support department.');

/*
 * Display main page template
 */

$sMain = Templater::AdvancedParse(
           $sTemplate->sValue.'/main',
		   $locale->strings,
		   array()
		 );

echo Templater::AdvancedParse(
       $sTemplate->sValue.'/master',
	   $locale->strings,
	   array(
	     'Content'	=> $sMain,
		 'Page'		=> 'main',
		 'Errors'	=> $sErrors
	   )
	 );