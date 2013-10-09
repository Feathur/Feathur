<?php
$sArray = array();

$sGetUptime = fopen('/proc/uptime', 'r');
$sUptime = fgets($sGetUptime);
fclose($sGetUptime);
$sUptime = explode('.', $sUptime, 2);
$sArray['uptime'] = $sUptime[0];

$sGetMemory = fopen('/proc/meminfo', 'r');
$sMemory = 0;
while ($sLine = fgets($sGetMemory)) {
	$sPieces = array();
	if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $sLine, $sPieces)) {
		$sArray['total_memory'] = $sPieces[1];
	}
	if (preg_match('/^MemFree:\s+(\d+)\skB$/', $sLine, $sPieces)) {
		$sFreeMemory = $sPieces[1];
	}
	if (preg_match('/^Cached:\s+(\d+)\skB$/', $sLine, $sPieces)) {
		$sCachedMemory = $sPieces[1];
		break;
	}
}
$sArray['free_memory'] = $sFreeMemory + $sCachedMemory;
fclose($sGetMemory);

$sArray['disk_total'] = disk_total_space("/");
$sArray['disk_free'] = disk_free_space("/");

$sLoad = sys_getloadavg();
$sArray['load_average'] = $sLoad[0];

$sArray['rx_bandwidth'] = exec("cat /sys/class/net/eth1/statistics/rx_bytes");
$sArray['tx_bandwidth'] = exec("cat /sys/class/net/eth1/statistics/tx_bytes");

echo json_encode($sArray);