<?php
require_once('./includes/loader.php');

$sAction = preg_replace('/[^\w\d]/', '', $_GET['action']);

/*
 * If activation email/id is empty, redirect back to index
 */

if (empty($_GET['email']) || empty($_GET['id'])) die(header("Location: index.php", 401));

/*
 * See if an activation code exists for the given email/id
 */

$sEmail = preg_replace('/[^\w\d_\-\.\_\@\+]/', '', $_GET['email']);
$sID    = preg_replace('/[^\w\d_]/', '', $_GET['id']);

$sActivate = $database->CachedQuery("SELECT * FROM accounts WHERE (`password` = -1 AND `email_address` = :EmailAddress AND `activation_code` = :ActivationCode) || (`email_address` = :EmailAddress AND `forgot` = :ActivationCode)", array('EmailAddress' => $sEmail, 'ActivationCode' => $sID));

/*
 * Redirect to index if there's no need to activate
 */

if (empty($sActivate)) die(header("Location: index.php", 403));


/*
 * Process activation
 */

if ($sAction == 'save')
{
  if (sha1($_POST['password']) == sha1($_POST['passwordagain']))
  {
    $sUser = new User($sActivate->data[0]['id']);
    $sChange = $sUser->change_password($sUser, $_POST['password'], $_POST['passwordagain']);
  } else {
    $sErrors[] = array('Errors' => 'Password and confirmation do not match.');
  }
  if (is_array($sChange))
  {
	$sErrors[] = array("Errors" => $sChange);
  } else {
    die(header("Location: index.php"));
  }
}

/*
 * Display activation result page
 */

echo Templater::AdvancedParse(
       $sTemplate->sValue.'/activate',
	   $locale->strings,
	   array(
	     'Errors'	=> $sErrors,
		 'Id'		=> $sID,
		 'Email'	=> $sEmail
	   )
	 );