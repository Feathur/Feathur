<?php
include('./includes/loader.php');

$sAction = $_GET['action'];

if((empty($_GET['email'])) || (empty($_GET['id']))){
	header("Location: index.php");
	die();
}

$sActivate = $database->CachedQuery("SELECT * FROM accounts WHERE (`password` = -1 AND `email_address` = :EmailAddress AND `activation_code` = :ActivationCode) || (`email_address` = :EmailAddress AND `forgot` = :ActivationCode)", array('EmailAddress' => $_GET['email'], 'ActivationCode' => $_GET['id']));

if(empty($sActivate)){
	header("Location: index.php");
	die();
}

if($sAction == save){
	$sUser = new User($sActivate->data[0]["id"]);
	$sChange = $sUser->change_password($sUser, $_POST['password'], $_POST['passwordagain']);
	if(is_array($sChange)){
		$sErrors = array("Errors" => $sChange);
	} else {
		header("Location: index.php");
		die();
	}
}
	
echo Templater::AdvancedParse($sTemplate->sValue.'/activate', $locale->strings, array('Errors' => $sErrors, 'Id' => urlencode($_GET['id']), 'Email' => urlencode($_GET['email'])));