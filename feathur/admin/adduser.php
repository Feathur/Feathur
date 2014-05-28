<?php

if ($sUser->sPermissions != 7) die("Sorry you've accessed our system without permission");

$sPage		= 'adduser';
$sPageType	= 'users';

if ($sAction == 'submituser')
{
  $sCreate = User::generate_user($_POST['email'], $_POST['username']);
  die(json_encode($sCreate));
}

$sContent = Templater::AdvancedParse(
			  $sAdminTemplate->sValue.'/adduser',
			  $locale->strings,
			  array()
			);