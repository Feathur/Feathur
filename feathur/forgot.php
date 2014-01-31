<?php
require_once('./includes/loader.php');

$sAction = $_GET['action'];

if(!empty($sUser)){
	header("Location: main.php");
	die();
}

if($sAction == forgot){
	$sErrors[] = User::forgot($_POST['email']);
}

echo Templater::AdvancedParse($sTemplate->sValue.'/forgot', $locale->strings, array("Errors" => $sErrors));