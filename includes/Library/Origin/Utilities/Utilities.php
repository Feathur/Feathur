<?php
namespace Origin\Utilities;

class Utilities {
	public static function RandomString($length = 10) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		
		return $randomString;
	}
	
	// Returns a usable array of files if files were uploaded otherwise returns array();
	public static function Uploads(){
		$i = 0;
		$files = array();
		if(!empty($_FILES)){
			foreach ($_FILES as $input) {
				$j = 0;
				foreach ($input as $property => $value) {
					if (is_array($value)) {
						$j = count($value);
						for ($k = 0; $k < $j; ++$k) {
							$files[$i + $k][$property] = $value[$k];
						}
					} else {
						$j = 1;
						$files[$i][$property] = $value;
					}
				}
				
				$i += $j;
			}
		}
		
		return $files;
	}
	
	public static function JsonExit($message = null, $success = false){
		exit(print_r(json_encode(array('message' => $message, 'success' => (($success === true) ? '1' : '0'))), true));
	}
	
	public static function HumanFileSize($bytes, $decimals = 2) {
   		$size = array(' Bytes',' KiloBytes',' MegaBytes',' GigaBytes',' TeraBytes',' PetaBytes');
    	$factor = floor((strlen($bytes) - 1) / 3);
    	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
	}
  
	public static function ValidateMD5($md5 = null){
		return (bool) preg_match('/^[a-f0-9]{32}$/', $md5);
	}
	
	public static function ValidateSHA1($sha1 = null){
		return (bool) preg_match('/^[0-9a-f]{40}$/i', $sha1); 
	}
	
	public static function FetchURLContent($url, array $parameters = null){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);

		if($parameters !== null){
			curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);	
		}
		
		$data = curl_exec($curl);
		curl_close($curl);
		
		return $data;
	}
	
	public static function GetIP(){
		return (isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"]);
	}
	
	public static function ValidateEmail($email = null){
		if($email !== null && filter_var($email, FILTER_VALIDATE_EMAIL)){
			return true;
		}
		
		return false;
	}
}
