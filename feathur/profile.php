<?php
require_once('./includes/loader.php');

/*
 * Redirect user to main page if not logged in
 */

if (empty($sUser)) die(header("Location: index.php", 401));


$sAction	= preg_replace('/[^\w\d]/', '', $_GET['action']);
$sUsername	= preg_replace('/[^a-zA-Z0-9\s]/', '', $_POST['username']);

/*
 * Process password update
 */

if ($sAction == 'password')
{
	$sChange = $sUser->change_password($sUser, $_POST['password'], $_POST['passwordagain']);
	if(is_array($sChange))
	{
	  echo json_encode($sChange);
	} else {
	  echo json_encode(array("content" => "Your password has been updated."));
	}
	die();
}

/*
 * Process username update
 */

if ($sAction == 'username')
{
  if(strlen($sUsername) > 2)
  {
	$sUser->uUsername = $sUsername;
	$sUser->InsertIntoDatabase();
	echo json_encode(array('content' => 'Your name has been updated in the database.'));
  } else {
	echo json_encode(array('content' => 'Your name must be at least 2 characters long.'));
  }
  die();
}

$sView = Templater::AdvancedParse(
           $sTemplate->sValue.'/profile',
		   $locale->strings,
		   array('Username' => $sUser->sUsername)
		 );
echo Templater::AdvancedParse(
       $sTemplate->sValue.'/master',
	   $locale->strings,
	   array(
	     'Content'	=> $sView,
		 'Page'		=> 'profile',
		 'Errors'	=> $sErrors
	   )
	 );