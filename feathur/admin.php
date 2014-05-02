<?php
require_once('./includes/loader.php');

$sId		= abs((int) $_GET['id']);
$sView		= preg_replace('/[^\w\d]/', '', $_GET['view']);
$sAction	= preg_replace('/[^\w\d_]/', '', $_GET['action']);
$sSearch	= preg_replace('/[^\d\w\s\.\-_]/', '', $_GET['search']);
$sType		= preg_replace('/[^\w\d]/', '', $_GET['type']);

$sPage		= 'admin';
$sPageType	= '';

/*
 * Redirect to index if user is not logged in
 */

if (empty($sUser)) die(header("Location: index.php", 401));

/*
 * Redirect to main if user is not an admin
 */

if ($sUser->sPermissions != 7) die(header("Location: main.php", 301));

/*
 * Display view if it exists, otherwise display dashboard
 */

if (@file_exists('./admin/'.$sView.'.php'))
{
  @require_once('./admin/'.$sView.'.php');
} else {
  require_once("./admin/dashboard.php");
}

/*
 * Display the template
 */

echo Templater::AdvancedParse(
       $sTemplate->sValue.'/master',
	   $locale->strings,
	   array(
	     'Content'	=> $sContent,
		 'Page'		=> $sPage,
		 'PageType'	=> $sPageType,
		 'Errors'	=> $sErrors
	   )
	 );