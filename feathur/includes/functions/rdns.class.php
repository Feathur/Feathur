<?php
class RDNS extends CPHPDatabaseRecordClass {
	
	public static function add_rdns($sIP, $sHostname){
		global $database;
		$sIPArray = explode(".", $sIP->sIPAddress);
		$sBlockARPA = $sIPArray[2].'.'.$sIPArray[1].'.'.$sIPArray[0].'.IN-ADDR.ARPA';
		$sRecordARPA = $sIPArray[3].'.'.$sIPArray[2].'.'.$sIPArray[1].'.'.$sIPArray[0].'.IN-ADDR.ARPA';
		try {
			if(!$sCheckBlockARPA = $database->CachedQuery("SELECT * FROM dns.domains WHERE `name` = :ARPA", array('ARPA' => $sBlockARPA))){
				$sInsertBlockARPA = $database->CachedQuery("INSERT INTO dns.domains (name) VALUES (:ARPA)", array('ARPA' => $sBlockARPA));
			}
		
			if(!$sCheckBlockARPA = $database->CachedQuery("SELECT * FROM dns.domains WHERE `name` = :ARPA", array('ARPA' => $sBlockARPA))){
				return $sArray = array("json" => 1, "type" => "error", "result" => "Contact support, the RDNS system is having issues.");
			}
		
			if(!$sCheckRecordARPA = $database->CachedQuery("SELECT * FROM dns.records WHERE `name` = :ARPA", array('ARPA' => $sRecordARPA))){
				$sInsertRecordARPA = $database->CachedQuery("INSERT INTO dns.records (domain_id, name, type, content) VALUES (:Domain, :ARPA, :Type, :Content)", array('Domain' => $sCheckBlockARPA->data[0]["id"],'ARPA' => $sRecordARPA, 'Type' => "PTR", 'Content' => $sHostname));
				return $sArray = array("json" => 1, "type" => "success", "result" => "rDNS has been updated for {$sIP->sIPAddress}");
			} else {
				$sUpdateRecordARPA = $database->CachedQuery("UPDATE dns.records SET content = :Content WHERE name = :ARPA", array('ARPA' => $sRecordARPA, 'Content' => $sHostname));
				return $sArray = array("json" => 1, "type" => "success", "result" => "rDNS has been updated for {$sIP->sIPAddress}");
			}
		} catch (Exception $e) {
			return $sArray = array("json" => 1, "type" => "error", "result" => "Contact support, the RDNS system is having issues.");
		}
	}
	
	public static function pull_rdns($sIP){
		global $database;
		$sIPArray = explode(".", $sIP->sIPAddress);
		$sRecordARPA = $sIPArray[3].'.'.$sIPArray[2].'.'.$sIPArray[1].'.'.$sIPArray[0].'.IN-ADDR.ARPA';
		try {
			if(!$sCheckRecordARPA = $database->CachedQuery("SELECT * FROM dns.records WHERE `name` = :ARPA", array('ARPA' => $sRecordARPA))){
				return array("result" => "None Set", "json" => 1);
			} else {
				return array("result" => $sCheckRecordARPA->data[0]["content"], "json" => 1);
			}
		} catch (Exception $e) {
			return array("result" => "None Set", "json" => 1);
		}
	}
}
