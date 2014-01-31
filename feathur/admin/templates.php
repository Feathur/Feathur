<?php
if($sUser->sPermissions != 7){
	die("Sorry you've accessed our system without permission");
}

$sPage = "templates";
$sPageType = "settings";
$sType = $_GET['type'];

if($sAction == addtemplate){
	if((!empty($_GET['type'])) && (!empty($_GET['url']))){
		$sAdd = VPS::add_template(VPS::localhost_connect(), $_GET['name'], $_GET['url'], $_GET['type']);
		if(is_array($sAdd)){
			echo json_encode($sAdd);
			die();
		}
	}
}

if($sAction == removetemplate){
	if(!empty($_GET['id'])){
		$sRemove = VPS::remove_template(VPS::localhost_connect(), $_GET['id']);
			if(is_array($sRemove)){
				echo json_encode($sRemove);
				die();
			}
	}
	$sJson = 1;
}

if($sAction == updatetemplate){
	if((!empty($_GET['name'])) && (is_numeric($_GET['id']))){
		$sUpdateTemplate = new Template($_GET['id']);
		$sUpdateTemplate->uName = $_GET['name'];
		$sUpdateTemplate->InsertIntoDatabase();
		echo json_encode(array("result" => "Template name updated.", "type" => "success", "json" => "1"));
		die();
	} else {
		echo json_encode(array("result" => "You must specify a name for the template.", "type" => "error", "json" => "1"));
		die();
	}
}

if(empty($sType)){
	$sContent .= Templater::AdvancedParse($sAdminTemplate->sValue.'/templates', $locale->strings, array("Virtualization" => 0));
} else {
	if($sTL = $database->CachedQuery("SELECT * FROM templates WHERE `type` = :Type", array('Type' => $sType))){
		foreach($sTL->data as $sData){
			$sTemplateList[] = array("id" => $sData["id"], "name" => $sData["name"], "path" => $sData["path"]);
		}
	}
	
	$sContent .= Templater::AdvancedParse($sAdminTemplate->sValue.'/templates', $locale->strings, array("Virtualization" => $sType, "TemplateList" => $sTemplateList, "Errors" => $sErrors));
}