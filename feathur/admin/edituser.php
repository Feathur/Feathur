<?php

if ($sUser->sPermissions != 7) die("Sorry, you've accessed our system without permission");

$sPage		= 'edituser';
$sPageType	= 'users';

if ($sAction == 'submituser')
{
  $sEditUser = User::Load($_POST['user_id']);
}

$sContent = Templater::AdvancedParse(
  $sAdminTemplate->sValue.'/edituser',
  $locale->strings,
  $sEditUser
);
