<?php
class User extends CPHPDatabaseRecordClass {

	public $table_name = "accounts";
	public $id_field = "id";
	public $fill_query = "SELECT * FROM accounts WHERE `id` = :Id";
	public $verify_query = "SELECT * FROM accounts WHERE `id` = :Id";
	public $query_cache = 1;
	
	public $prototype = array(
		'string' => array(
			'Username' => "username",
			'EmailAddress' => "email_address",
			'Password' => "password",
			'ActivationCode' => "activation_code",
			'Salt' => "salt",
			'Forgot' => "forgot",
		),
		'numeric' => array(
			'Permissions' => "permissions",
		),
	);
	
	public static function login($uEmail, $uPassword, $sAPI = 0){
		global $database;
		if((!empty($uEmail)) && (!empty($uPassword))){
			$sGetSalt = $database->CachedQuery("SELECT * FROM accounts WHERE `email_address` = :EmailAddress", array('EmailAddress' => $uEmail));
			$uPassword = User::hash_password($uPassword, $sGetSalt->data[0]["salt"]);
			if($result = $database->CachedQuery("SELECT * FROM accounts WHERE `email_address` = :EmailAddress AND `password` = :Password", array('EmailAddress' => $uEmail, 'Password' => $uPassword))){
				$_SESSION['user_id'] = $result->data[0]["id"];
				if(empty($sAPI)){
					header("Location: main.php");
					die();
				} else {
					return $sUser = new User($result->data[0]["id"]);
				}
			} else {
				return $sError = array("result" => "Your email or password is invalid!", "type" => "error");
			}
		} else {
			return $sError = array("result" => "You must enter an email and a password!", "type" => "error");
		}
	}
	
	public static function generate_user($uEmail, $uUsername, $sAPI = NULL){
		global $database;
		if(!$result = $database->CachedQuery("SELECT * FROM accounts WHERE `email_address` = :EmailAddress", array('EmailAddress' => $uEmail))){
			$uEmail = str_replace(" ", "+", $uEmail);
			if(filter_var($uEmail, FILTER_VALIDATE_EMAIL)){
				$sActivationCode = random_string(120);
				$sUser = new User(0);
				$sUser->uUsername = $uUsername;
				$sUser->uEmailAddress = $uEmail;
				$sUser->uPassword = "-1";
				$sUser->uActivationCode = $sActivationCode;
				$sUser->InsertIntoDatabase();
				$sVariable = array("email" => urlencode($sUser->sEmailAddress), "activation_code" => urlencode($sActivationCode));
				$sSend = Core::SendEmail($sUser->sEmailAddress, "Feathur Activation Email", "new_user", $sVariable);
				if($sSend === true){
					if(empty($sAPI)){
						return $sReturn = array("content" => "Account Created", "created" => "1");
					} else {
						return $sUser;
					}
				} else {
					return $sSend;
				}
			} else {
				return $sReturn = array("content" => "The email you entered is invalid");
			}
		} else {
			return $sReturn = array("content" => "The email you entered already has an account!");
		}
	}
	
	public static function hash_password($sPassword, $sSalt){
		$sHash = crypt($sPassword, "$5\$rounds=50000\${$sSalt}$");
		$sParts = explode("$", $sHash);
		return $sParts[4];
	}
	
	public static function generate_salt($sUser){
		$sSalt = random_string(30);
		$sUser->uSalt = $sSalt;
		$sUser->InsertIntoDatabase();
	}
	
	public static function change_password($sUser, $sNewPassword, $sNewPasswordAgain){
		if((strlen($sNewPassword)) > 5){
			if($sNewPassword == $sNewPasswordAgain){
				if(empty($sUser->sSalt)){
					$sUser->generate_salt($sUser);
				}
				$sUser->uPassword = $sUser->hash_password($sNewPassword, $sUser->sSalt);
				$sUser->InsertIntoDatabase();
				return true;
			} else {
				return $sReturn = array("content" => "Your passwords did not match!");
			}
		} else {
			return $sResult = array("content" => "Password must be at least 5 characters");
		}
	}
	
	public static function forgot($uEmail){
		global $database;
		if($sResult = $database->CachedQuery("SELECT * FROM accounts WHERE `email_address` = :EmailAddress", array('EmailAddress' => $uEmail))){
			$sForgotCode = random_string(120);
			$sUser = new User($sResult->data[0]["id"]);
			$sUser->uForgot = $sForgotCode;
			$sUser->InsertIntoDatabase();
			$sVariable = array("email" => urlencode($sUser->sEmailAddress), "forgot_code" => urlencode($sForgotCode));
			$sSend = Core::SendEmail($sUser->sEmailAddress, "Feathur Forgot Password", "forgot", $sVariable);
			return $sResult = array("content" => "Please check your email for a reset link!", "type" => "succesbox");
		} else {
			return $sResult = array("content" => "User with that email not found!", "type" => "errorbox");
		}
	}
}