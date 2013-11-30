<?php
class Core {

	static function GetSetting($setting){
		global $database;
		if($result = $database->CachedQuery("SELECT * FROM settings WHERE `setting_name` = :Setting", array(':Setting'	=> $setting))){
			return new Setting($result);
		} else {
			return false;
		}
	}
	
	static function UpdateSetting($setting, $value){
		global $database;
		$result = $database->CachedQuery("UPDATE settings SET `setting_value` = :Value WHERE `setting_name` = :Setting", array(':Value' => $value, ':Setting' => $setting));
		return true;
	}
	
	public static function SendEmail($sTo, $sSubject, $sTemplate, $sVariable){
		global $sPanelURL;
		global $sPanelMode;
		global $sTitle;
		global $locale;
		$sEmail = Templater::AdvancedParse('/email/'.$sTemplate, $locale->strings, array("EmailVars" => array("entry" => $sVariable)));
		$sMail = Core::GetSetting('mail');
		$sMail = $sMail->sValue;
		if($sMail == 1){
			$sSendGridUser = Core::GetSetting('mail_username');
			$sSendGridPass = Core::GetSetting('mail_password');
			$sSendGridUser = $sSendGridUser->sValue;
			$sSendGridPass = $sSendGridPass->sValue;
			if((!empty($sSendGridUser)) && (!empty($sSendGridPass))){
				$sGrid = new SendGrid($sSendGridUser, $sSendGridPass);
				$sMail = new SendGrid\Mail();
				$sMail->addTo($sTo)->setFrom("noreply@{$sPanelURL->sValue}")->setSubject($sSubject)->setHtml($sEmail);
				$sGrid->web->send($sMail);
				return true;
			} else {
				return $sReturn = array("content" => "Unfortunately Send Grid is incorrectly configured!");
			}
		} elseif($sMail == 2){
			$sMandrillUser = Core::GetSetting('mail_username');
			$sMandrillPass = Core::GetSetting('mail_password');
			$sMandrillUser = $sMandrillUser->sValue;
			$sMandrillPass = $sMandrillPass->sValue;
			
			try {
				$sMandrill = new Mandrill($sMandrillPass);
				$sMessage = array(
					'html' => $sEmail,
					'subject' => $sSubject,
					'from_email' => "noreply@{$sPanelURL->sValue}",
					'from_name' => "{$sTitle->sValue}",
					'to' => array(
						array(
							'email' => $sTo,
							'type' => 'to'
						)
					),
					'important' => true,
					'track_opens' => null,
					'track_clicks' => null,
					'auto_text' => null,
					'auto_html' => null,
					'inline_css' => null,
					'url_strip_qs' => null,
					'preserve_recipients' => null,
					'view_content_link' => null,
					'tracking_domain' => null,
					'signing_domain' => null,
					'return_path_domain' => null,
					'merge' => true,
				);
				$sAsync = false;
				$sIPPool = 'Main Pool';
				$sSendAt = date("M d Y H:i:s", time());
				$sResult = $sMandrill->messages->send($sMessage, $sAsync, $sIPPool, $sSendAt);
			} catch (Exception $e) {
				return $sReturn = array("content" => "Mandril Error: {$e}");
			}
			return true;
		} else {
			$sHeaders = "MIME-Version: 1.0" . "\r\n";
			$sHeaders .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
			$sHeaders .= 'From: <noreply@'.$sPanelURL->sValue.'>' . "\r\n";
			if(mail($sTo, $sSubject, $sEmail, $sHeaders)){
				return true;
			} else {
				return $sReturn = array("content" => "Unfortunatly the email failed to send, please check your server's sendmail settings.");
			}
		}
	}
}

class VPSLogs extends CPHPDatabaseRecordClass {

	public $table_name = "vps_logs";
	public $id_field = "id";
	public $fill_query = "SELECT * FROM vps_logs WHERE `id` = :Id";
	public $verify_query = "SELECT * FROM vps_logs WHERE `id` = :Id";
	public $query_cache = 1;
	
	public $prototype = array(
		'string' => array(
			'Entry' => "entry",
			'Command' => "command",
		),
		'numeric' => array(
			'Timestamp' => "timestamp",
			'VPSId' => "vps_id",
		),
	);
}
	
class ServerLogs extends CPHPDatabaseRecordClass {

	public $table_name = "server_logs";
	public $id_field = "id";
	public $fill_query = "SELECT * FROM server_logs WHERE `id` = :Id";
	public $verify_query = "SELECT * FROM server_logs WHERE `id` = :Id";
	public $query_cache = 1;
	
	public $prototype = array(
		'string' => array(
			'Entry' => "entry",
			'Command' => "command",
		),
		'numeric' => array(
			'Timestamp' => "timestamp",
			'ServerId' => "server_id",
		),
	);
	
	public static function save_server_logs($sLog, $sServer){
		foreach($sLog as $key => $value){
			$sLog = new ServerLogs(0);
			$sLog->uEntry = $value["result"];
			$sLog->uCommand = $value["command"];
			$sLog->uServerId = $sServer->sId;
			$sLog->uTimestamp = time();
			$sLog->InsertIntoDatabase();
		}
	}
}

class ServerCommands extends CPHPDatabaseRecordClass {

	public $table_name = "server_commands";
	public $id_field = "id";
	public $fill_query = "SELECT * FROM server_commands WHERE `id` = :Id";
	public $verify_query = "SELECT * FROM server_commands WHERE `id` = :Id";
	public $query_cache = 1;
	
	public $prototype = array(
		'string' => array(
			'Command' => "command",
		),
		'numeric' => array(
			'Last' => "last",
			'Interval' => "interval",
			'ServerId' => "server_id",
		),
	);
}