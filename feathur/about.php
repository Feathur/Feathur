<?php
require_once('./includes/loader.php');

/*
 * Session check
 */

if (empty($sUser)) die(header("Location: index.php", 401));

/*
 * Print about page
 */

$sView = Templater::AdvancedParse(
           $sTemplate->sValue.'/about',
		   $locale->strings,
		   array(
		     'Username' => $sUser->sUsername
		   )
		 );
echo Templater::AdvancedParse(
       $sTemplate->sValue.'/master',
	   $locale->strings,
	   array(
	     'Content'	=> $sView,
		 'Page'		=> 'about',
		 'Errors'	=> $sErrors
	   )
	 );