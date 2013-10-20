<?php
class Pull {

	public function Start(){
		global $database;
		global $sMaxReturn;
		global $sPullDifference;
		global $sHistoryDifference;
		
		$sPullTime = time() - $sPullDifference->sValue;
		$sFullPull = $database->CachedQuery("SELECT * FROM servers WHERE `status_type` = :Type AND `last_check` < :PullTime LIMIT 5", array(
			':Type' => "full",
			':PullTime' => $sPullTime,
		), 10);
		
		if(isset($sFullPull)){
			foreach($sFullPull->data as $value){
				$sServer = new Server($value["id"]);
				$sServer->uLastCheck = time();
				$sServer->InsertIntoDatabase();
				$sFullQuery[$value["id"]] = $value["url"];
			}
		}
		
		if(isset($sFullQuery)){
			$sData = $this->StandardUptimePull($sFullQuery);
			foreach($sData as $key => $value){
				$value = json_decode($value, true);
				if(is_array($value)){
					$sServer = new Server($key);
					$sLastBandwidth = $sServer->sBandwidth;
					if($sServer->sUpSince == 0){
						$sServer->uUpSince = time();
					}
					$sServer->uDownSince = 0;
					$sServer->uStatus = 1;
					$sServer->uStatusWarning = 0;
					$sServer->uPreviousCheck = $sServer->sLastCheck;
					$sServer->uLastCheck = time();
					$sServer->uHardwareUptime = $value["uptime"];
					$sServer->uTotalMemory = $value["total_memory"];
					$sServer->uFreeMemory = $value["free_memory"];
					$sServer->uLoadAverage = $value["load_average"];
					$sServer->uHardDiskFree = round(($value["disk_free"] / 1024 / 1024), 2);
					$sServer->uHardDiskTotal = round(($value["disk_total"] / 1024 / 1024), 2);
					$sServer->uLastBandwidth = $sLastBandwidth;
					$sServer->uBandwidth = round((($value["rx_bandwidth"] + $value["tx_bandwidth"]) / 1024 / 1024), 2);
					$sServer->InsertIntoDatabase();
					
					$sHistory = $this->HistoryCreate($key, "1");
					$sStats = $this->StatsCreate($key, "1", $value["uptime"], $value["total_memory"], $value["free_memory"], $value["load_average"], round(($value["disk_free"] / 1024 / 1024), 2), round(($value["disk_total"] / 1024 / 1024), 2), round((($value["rx_bandwidth"] + $value["tx_bandwidth"]) / 1024 / 1024), 2));
				} else {
					$sServer = new Server($key);
					if($sServer->sStatusWarning == 0){
						$sServer->uStatusWarning = 1;
						$sServer->InsertIntoDatabase();
					} else {
						if($sServer->sDownSince == 0){
							$sServer->uDownSince = time();
						}
						$sServer->uUpSince = 0;
						$sServer->uStatus = 0;
						$sServer->uLastCheck = time();
						$sServer->InsertIntoDatabase();
					
						$sHistory = $this->HistoryCreate($key, "0");
					}
				}	
				unset($sLastBandwidth);
			}
		}
		
		$this->CleanStatistics();
		$this->CleanHistory();
	}
	
	public function StandardUptimePull($sData) {
		global $sMaxReturn;
		$sHandles = array();
		$sResult = array();
		$sMultiHandle = curl_multi_init();
	
		foreach ($sData as $sId => $sArray) {
			$sHandles[$sId] = curl_init();
			$sURL = (is_array($sArray) && !empty($sArray['url'])) ? $sArray['url'] : $sArray;
			curl_setopt($sHandles[$sId], CURLOPT_URL, $sURL);
			curl_setopt($sHandles[$sId], CURLOPT_HEADER, 0);
			curl_setopt($sHandles[$sId], CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($sHandles[$sId], CURLOPT_CONNECTTIMEOUT_MS, 3000);
			curl_setopt($sHandles[$sId], CURLOPT_CONNECTTIMEOUT, 3 );
			curl_multi_add_handle($sMultiHandle, $sHandles[$sId]);
		}
		
		$sRunning = NULL;
		do {
			curl_multi_exec($sMultiHandle, $sRunning);
		} while($sRunning > 0);
 
		foreach($sHandles as $sId => $sCurl) {
			$sResult[$sId] = curl_multi_getcontent($sCurl);
			curl_multi_remove_handle($sMultiHandle, $sCurl);
		}
		
  		curl_multi_close($sMultiHandle);
  		
  		return $sResult;
	}
	
	public function StatsCreate($sServerId, $sStatus, $sUptime, $sTotalMemory, $sFreeMemory, $sLoad, $sHardDiskFree, $sHardDiskTotal, $sBandwidth){
		global $sHistoryDifference;
		global $database;
		
		$sTimeAgo = time() - ($sHistoryDifference->sValue * 60);

		if(!$sCheck = $database->CachedQuery("SELECT * FROM statistics WHERE `server_id` = :ServerId AND `timestamp` > :TimeAgo", array(':ServerId' => $sServerId, ':TimeAgo' => $sTimeAgo), 10)){
			$sStatistics = new Statistics(0);
			$sStatistics->uHardwareUptime = $sUptime;
			$sStatistics->uStatus = $sStatus;
			$sStatistics->uServerId = $sServerId;
			$sStatistics->uTotalMemory = $sTotalMemory;
			$sStatistics->uFreeMemory = $sFreeMemory;
			$sStatistics->uLoadAverage = $sLoad;
			$sStatistics->uHardDiskFree = $sHardDiskFree;
			$sStatistics->uHardDiskTotal = $sHardDiskTotal;
			$sStatistics->uBandwidth = $sBandwidth;
			$sStatistics->uTimestamp = time();
			$sStatistics->InsertIntoDatabase();
		}
	}
	
	public function HistoryCreate($sServerId, $sStatus){
		global $database;
		
		$sHistoryCheck = $database->CachedQuery("SELECT * FROM history WHERE `server_id` = :ServerId ORDER BY id DESC LIMIT 1", array(
			':ServerId' => $sServerId,
		), 10);
		if(!empty($sHistoryCheck)){
			if($sHistoryCheck->data[0]["status"] != $sStatus){
				$sHistory = new History(0);
				$sHistory->uServerId = $sServerId;
				$sHistory->uTimestamp = time();
				$sHistory->uStatus = $sStatus;
				$sHistory->InsertIntoDatabase();
			}
		} else {
			$sHistory = new History(0);
			$sHistory->uServerId = $sServerId;
			$sHistory->uTimestamp = time();
			$sHistory->uStatus = $sStatus;
			$sHistory->InsertIntoDatabase();
		}
	}
	
	public function CleanHistory(){
		global $database;
		global $sMaxHistory;
		$sHistory = ($sMaxHistory->sValue * 60 * 60 * 24);
		
		$sHistoryCheck = $database->CachedQuery("DELETE FROM history WHERE `timestamp` < :MaxHistory", array(
			':MaxHistory' => $sHistory,
		), 10);
	}
	
	public function CleanStatistics(){
		global $database;
		global $sMaxStatistics;
		$sStatistics = ($sMaxStatistics->sValue * 60 * 60 * 24);
		
		$sStatisticsCheck = $database->CachedQuery("DELETE FROM statistics WHERE `timestamp` < :MaxStatistics", array(
			':MaxStatistics' => $sStatistics,
		), 10);	
	}
}