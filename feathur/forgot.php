<?php
require_once('./includes/loader.php');

$sAction = preg_replace('/[^\w\d]/', '', $_GET['action']);
$sEmail  = preg_replace('/[^\w\d_\._@+]/', '', $_POST['email']);

/*
 * Redirect user to main page if not logged in
 */

if (!empty($sUser)) die(header("Location: main.php", 401));

/*
 * Process forgotten password form
 */

if ($sAction == 'forgot') $sErrors[] = User::forgot($sEmail);

/*
 * Display forgot password template
 */

echo Templater::AdvancedParse(
       $sTemplate->sValue.'/forgot',
	   $locale->strings,
	   array('Errors' => $sErrors)
	 );