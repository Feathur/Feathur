<?php
require_once('./includes/loader.php');

$sAction = preg_replace('/[^\w\d]/', '', $_GET['action']);

/*
 * Redirect user to main page if not logged in
 */

if (!empty($sUser)) die(header("Location: main.php", 401));

/*
 * Process login if provided
 */

if ($sAction == 'login') $sErrors[] = User::login($_POST['email'], $_POST['password']);

/*
 * Display login template
 */

echo Templater::AdvancedParse(
       $sTemplate->sValue.'/login',
	   $locale->strings,
	   array('Errors' => $sErrors)
	 );