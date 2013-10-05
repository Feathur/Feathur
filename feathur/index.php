<?php
require_once('./includes/loader.php');

$sAction = $_GET['action'];

if(!empty($sUser)){
	header("Location: main.php");
	die();
}

if($sAction == login){
	$sErrors[] = User::login($_POST['email'], $_POST['password']);
}

echo Templater::AdvancedParse($sTemplate->sValue.'/login', $locale->strings, array("Errors" => $sErrors));