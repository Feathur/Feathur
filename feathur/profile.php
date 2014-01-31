<?php
include('./includes/loader.php');

// Get prerequisites.
$sId = $_GET['id'];
$sAction = $_GET['action'];

// Check for login.
if(empty($sUser)){
	header("Location: index.php");
	die();
}

if($sAction == password){
	$sChange = $sUser->change_password($sUser, $_POST['password'], $_POST['passwordagain']);
	if(is_array($sChange)){
		echo json_encode($sChange);
	} else {
		echo json_encode(array("content" => "Your password has been updated."));
	}
	die();
} elseif($sAction == username){
	if(strlen($_POST['username']) > 2){
		$sUser->uUsername = preg_replace("/[^a-zA-Z0-9\s]/", "", $_POST['username']);
		$sUser->InsertIntoDatabase();
		echo json_encode(array("content" => "Your name has been updated in the database."));
	} else {
		echo json_encode(array("content" => "Your name must be at least 2 characters long."));
	}
	die();
}

$sView = Templater::AdvancedParse($sTemplate->sValue.'/profile', $locale->strings, array("Username" => $sUser->sUsername));
echo Templater::AdvancedParse($sTemplate->sValue.'/master', $locale->strings, array("Content" => $sView, "Page" => "profile", "Errors" => $sErrors));