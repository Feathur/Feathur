<?php
/**
 * Load our core libraries
 */
$cwd = '/var/feathur/feathur/includes/';
require_once($cwd . 'functions/user.class.php');
require_once($cwd . 'functions/core.class.php');
require_once($cwd . 'functions/settings.class.php');
require_once($cwd . 'functions/templates.class.php');
require_once($cwd . 'functions/vps.class.php');
require_once($cwd . 'functions/vps.openvz.class.php');
require_once($cwd . 'functions/vps.kvm.class.php');
require_once($cwd . 'functions/servers.class.php');
require_once($cwd . 'functions/ipaddresses.class.php');
require_once($cwd . 'functions/ipv6addresses.class.php');
require_once($cwd . 'functions/blocks.class.php');
require_once($cwd . 'functions/useripv6blocks.class.php');
require_once($cwd . 'functions/server.blocks.class.php');
require_once($cwd . 'library/Net/SSH2.php');
require_once($cwd . 'library/Crypt/RSA.php');
require_once($cwd . 'library/File/ANSI.php');
require_once($cwd . 'functions/pull.class.php');
require_once($cwd . 'functions/history.class.php');
require_once($cwd . 'functions/statistics.class.php');
require_once($cwd . 'functions/rdns.class.php');
require_once($cwd . 'functions/transfer.class.php');
require_once($cwd . 'functions/attempts.class.php');
require_once($cwd . 'functions/smtp.class.php');
require_once($cwd . 'functions/groups.class.php');
require_once($cwd . 'functions/server.groups.class.php');

/**
 * Convert timestamp to human-readable date
 * @param int $ss
 * @return string $sReturn
 */

function ConvertTime($ss)
{
  if (!empty($ss))
  {
    $sSeconds = $ss%60;
    $sMinutes = floor(($ss%3600)/60);
    $sHours = floor(($ss%86400)/3600);
    $sDays = floor(($ss%2592000)/86400);
    $sMonths = floor($ss/2592000);

    if ($sMonths > 0)  $sResult[] = "{$sMonths} Months";
    if ($sDays > 0)    $sResult[] = "{$sDays} Days";
    if ($sHours > 0)   $sResult[] = "{$sHours}h";
    if ($sMinutes > 0) $sResult[] = "{$sMinutes}m";
    if ($sSeconds > 0) $sResult[] = "{$sSeconds}s";

    $sTotal = count($sResult);

    foreach($sResult as $value)
    {
      $sCurrent++;
      if ($sCurrent != $sTotal)
      {
        $sReturn .= $value.', ';
      } else {
        $sReturn .= $value.' ';
      }
    }
  } else {
    $sReturn = "0m 0s";
  }
  return $sReturn;
}

/**
 * Convert bytes to suffixed value
 * @param int $sSize
 * @param int $sPrecision
 * @return string
 */

function formatBytes($sSize, $sPrecision = 2)
{
  $sBase = log($sSize) / log(1024);
  if ($sBase < 0) $sBase = 0;
  $sSuffixes = array(' MB', ' GB', ' TB');
  return round(pow(1024, $sBase - floor($sBase)), $sPrecision) . $sSuffixes[floor($sBase)];
}

/**
 * Return random MAC address
 * @return string
 */

function generate_mac()
{
  global $database;

  while ($i <= 6)
  {
    $sGen[] = dechex(rand(0, 15));
    $i++;
  }

  $sMac = sprintf('00:16:3c:%s%s:%s%s:%s%s', $sGen[0], $sGen[1], $sGen[2], $sGen[3], $sGen[4], $sGen[5]);

  if($sExists = $database->CachedQuery("SELECT * FROM vps WHERE `mac` = :Mac", array('Mac' => $sMac)))
  {
    return generate_mac();
  } else {
    return $sMac;
  }
}

function SortPrimaryIP($a, $b)
{
  return strcmp($b['primary'], $a['primary']);
}

function check_updates()
{
  $sCurrentVersion = file_get_contents('/var/feathur/version.txt');
  $sURL = 'https://raw.github.com/BlueVM/Feathur/Testing/version.txt';
  $sCurl = curl_init();
  curl_setopt($sCurl, CURLOPT_URL, $sURL);
  curl_setopt($sCurl, CURLOPT_RETURNTRANSFER, 1);
  $sVersion = preg_replace('/\s+/', '', curl_exec($sCurl));
  curl_close($sCurl);
  if ($sVersion != $sCurrentVersion)
  {
    return array("your_version" => $sCurrentVersion, "current_version" => $sVersion, "update" => "1");
  } else {
    return array("your_version" => $sCurrentVersion, "current_version" => $sVersion, "update" => "0");
  }
}

function endsWith($haystack, $needle)
{
  return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}
