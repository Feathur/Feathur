<?php

class Pull
{

  public static function pull_status($sServer)
  {

    echo "Setting up prerequisites...\n";
    $sTimestamp = time();
    global $database;

    // Insert History
    $sHistory = new History(0);
    $sHistory->uServerId = $sServer;
    $sHistory->uTimestamp = $sTimestamp;
    $sHistory->uStatus = false;
    $sHistory->InsertIntoDatabase();

    // Insert Statistics
    $sStatistics = new Statistics(0);
    $sStatistics->uServerId = $sServer;
    $sStatistics->uStatus = false;
    $sStatistics->uTimestamp = $sTimestamp;
    $sStatistics->InsertIntoDatabase();

    // Connect to Server
    $sServer = new Server($sServer);
    $sSSH = Server::server_connect($sServer, "1");
    if (is_array($sSSH)) die();

    // Pull system stats.
    echo "Connected to server...\n";
    $sUptime = explode(' ', $sSSH->exec("cat /proc/uptime"));
    $sCPU = explode(' ', $sSSH->exec("cat /proc/loadavg"));
    $sUsedRAM = preg_replace('/[^0-9]/', '', $sSSH->exec("free | head -n 3 | tail -n 1 | awk '{print $3}'"));
    $sTotalRAM = preg_replace('/[^0-9]/', '', $sSSH->exec("free | head -n 2 | tail -n 1 | awk '{print $2}'"));
    $sDisk = $sSSH->exec("df");
    $sDisk = explode("\n", trim($sDisk));
    array_shift($sDisk);

    foreach($sDisk as $sValue)
    {
      $sValue = explode(' ', preg_replace('/\s+/', ' ', $sValue));
      if (is_numeric($sValue[2]))
      {
        $sDiskUsed = $sDiskUsed + $sValue[2];
        $sDiskTotal = $sDiskTotal + $sValue[1];
      }
    }

    $sDiskUsed = $sDiskUsed / 1048576;
    $sDiskTotal = $sDiskTotal / 1048576;
    $sPullBandwidth = explode("\n", $sSSH->exec("grep '$interface:' /vz/private/{$sVPS->sContainerId}/proc/net/dev | perl -i -pe 's/\t|\s+/:/g' | awk -F: '{print $3,$11}'"));

    foreach($sPullBandwidth as $sData)
    {
      if (empty($sNewBandwidth))
      {
        $sData = explode(" ", $sData);
        $sData[0] = preg_replace('/[^0-9]/', '', $sData[0]);
        $sData[1] = preg_replace('/[^0-9]/', '', $sData[1]);
        $sNewBandwidth = round(($sData[0] / 131072) + ($sData[1] / 131072), 2);
      }
    }

    // Update server row.
    // Check to make sure that the current bandwidth is higher than last bandwidth.
    // If higher, update statuses, otherwise replace both values with current value.
    // This prevents bandwidth accounting from becoming negative.
    $sBandwidthNegative = $sServer->sBandwidth;
    if ($sNewBandwidth > $sBandwidthNegative)
    {
      $sServer->uPreviousCheck = $sServer->sLastCheck;
      $sServer->uLastCheck = $sTimestamp;
      $sServer->uLastBandwidth = $sServer->sBandwidth;
      $sServer->uBandwidth = $sNewBandwidth;
    } else {
      $sServer->uPreviousCheck = $sTimestamp;
      $sServer->uLastCheck = $sTimestamp;
      $sServer->uLastBandwidth = $sNewBandwidth;
      $sServer->uBandwidth = $sNewBandwidth;
    }

    $sServer->uLoadAverage = $sCPU[0];
    $sServer->uHardDiskTotal = $sDiskTotal;
    $sServer->uHardDiskFree = ($sDiskTotal - $sDiskUsed);
    $sServer->uTotalMemory = $sTotalRAM;
    $sServer->uFreeMemory = ($sTotalRAM - $sUsedRAM);
    $sServer->uStatus = true;
    $sServer->uStatusWarning = false;
    $sServer->uHardwareUptime = $sUptime[0];
    $sServer->InsertIntoDatabase();

    // Update history
    $sHistory->uStatus = true;
    $sHistory->InsertIntoDatabase();

    // Update statistics
    $sStatistics->uStatus = true;
    $sStatistics->uHardwareUptime = $sUptime[0];
    $sStatistics->uTotalMemory = $sTotalRAM;
    $sStatistics->uFreeMemory = ($sTotalRAM - $sUsedRAM);
    $sStatistics->uLoadAverage = $sCPU[0];
    $sStatistics->uHardDiskTotal = $sDiskTotal;
    $sStatistics->uHardDiskFree = ($sDiskTotal - $sDiskUsed);
    $sStatistics->uBandwidth = $sNewBandwidth;
    $sStatistics->InsertIntoDatabase();

    // Cleanup
    unset($sPullBandwidth);
    echo "Server polling completed...\n";

    echo "Begining script updates...\n";

    // Generate a random number... if the number is 1 then setup and start anti-abuse script on node.
    // This is to reduce the amount of time a server poll takes as we don't need to check suspended users every minute.
    $sRandom = rand(0, 5);

    if (($sServer->sType == 'openvz') && ($sRandom == 1))
    {
      // Copy script to server so that updates are dispersed.
      $sAbuse = escapeshellarg(file_get_contents('/var/feathur/Scripts/abuse.sh'));
      $sDumpCode = $sSSH->exec("mkdir -p /var/feathur/data;cd /var/feathur/data/;echo {$sAbuse} > abuse.sh;");

      // Pull list of suspended users and mark them as suspended in Feathur.
      $sSuspended = explode("\n", $sSSH->exec("cat /var/feathur/data/suspended.txt"));
      foreach($sSuspended as $sValue)
      {
        $sValue = preg_replace('/[^0-9]/', '', $sValue);
        if ((!empty($sValue)) && ($sValue >> 10))
        {
          try
          {
            $sVPS = new VPS($sValue);
            $sVPS->uSuspended = 2;
            $sVPS->InsertIntoDatabase();
          } catch (Exception $e)
          {
            echo "Odd data in suspend tracker. Skipping";
          }
        }
      }

      // Cleanup the suspended list, stop and restart abuse script.
      $sClean = $sSSH->exec("rm -rf /var/feathur/data/suspended.txt;pkill abuse.sh;screen -dmS abuse bash -c \"cd /var/feathur/data/;bash abuse.sh;\";");
    }

    echo "Finishing script updates...\n";
    echo "Starting SMTP abuse search...\n";

    // Pull some settings...
    $sMaxSMTP = Core::GetSetting('max_smtp_connections');

    // Get all current SMTP connection IP addresses.
    $sConnections = explode("\n", $sSSH->exec("netstat -nputw | grep \"smtp\" | grep -E -o '[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}';netstat -nputw | grep \":25 \" | grep -E -o '[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}'"));

    // Count the number of connections for each IP.
    $sCountConnections = array_count_values($sConnections);

    // Go through the list of unique IPs looking for matching user IPs.
    foreach($sCountConnections as $sKey => $sValue)
    {
      if ($sIPs = $database->CachedQuery("SELECT * FROM `ipaddresses` WHERE `ip_address` LIKE :IP", array("IP" => "%".$sKey."%")))
      {
        $sNumber = count($sIPs->data);
        if ($sNumber == 1)
        {

          $sVPS = new VPS($sIPs->data[0]['vps_id']);

          // Log number of SMTP connections.
          $sSMTP = new SMTP(0);
          $sSMTP->uVPSId = $sVPS->sId;
          $sSMTP->uTimestamp = $sTimestamp;
          $sSMTP->uConnections = $sValue;
          $sSMTP->InsertIntoDatabase();

          // Suspend VPS if it's over the predetermined limit and isn't whitelisted.
          if (($sValue >= $sMaxSMTP->sValue) && ($sVPS->sSMTPWhitelist == 0))
          {
            $sServer = new Server($sVPS->sServerId);
            $sSSH = Server::server_connect($sServer);
            $sLog[] = array("command" => "vzctl stop {$sVPS->sContainerId} --fast", "result" => $sSSH->exec("vzctl stop {$sVPS->sContainerId} --fast"));
            $sLog[] = array("command" => "vzctl set {$sVPS->sContainerId} --disabled yes --save", "result" => $sSSH->exec("vzctl set {$sVPS->sContainerId} --disabled yes --save"));
            $sSave = VPS::save_vps_logs($sLog, $sVPS);
            $sVPS->uSuspended = 3;
            $sVPS->InsertIntoDatabase();
          }
        }
      }
    }

    // Cleanup old records:
    $sOldData = (time() - (60*60*24*14));
    $sSMTP = $database->prepare("DELETE FROM `smtp` WHERE timestamp < :OldData");
    $sSMTP->bindParam(':OldData', $sOldData, PDO::PARAM_INT);
    $sSMTP->execute();
    echo "Finishing SMTP abuse search...\n";

    // Bandwidth polling for each VPS on this server.
    // Needs a rewrite... inaccurate.
    $sBandwidthAccounting = Core::GetSetting('bandwidth_accounting');
    echo "Beginning bandwidth accounting...\n";

    if ($sListVPS = $database->CachedQuery("SELECT * FROM `vps` WHERE `server_id` = :ServerId", array("ServerId" => $sServer->sId)))
    {
      foreach($sListVPS->data as $sVPS)
      {

        $sVPS = new VPS($sVPS["id"]);

        if ($sServer->sType == 'openvz')
        {
          $sPullBandwidth = explode("\n",
            $sSSH->exec("grep '$interface:' /vz/private/{$sVPS->sContainerId}/proc/net/dev | perl -i -pe 's/\t|\s+/:/g' | awk -F: '{print $3,$11}'"));
        }

        if ($sServer->sType == 'kvm')
        {
          $sPullBandwidth = explode("\n", $sSSH->exec('for i in $(ip link show | grep kvm'.$sVPS->sContainerId.' | awk \'{print $2}\' | awk -F: \'{print $1}\' | sort -u); do ifconfig $i | grep \'RX bytes\' | awk -F: \'{print $2,$3}\' | awk \'{print $1,$6}\'; done'));
        }

        foreach($sPullBandwidth as $sData)
        {
          $sData = explode(' ', $sData);
          $sData[0] = round(((preg_replace('/[^0-9]/', '', $sData[0]) / 1024) / 1024), 2);
          $sData[1] = round(((preg_replace('/[^0-9]/', '', $sData[1]) / 1024) / 1024), 2);
          if ($sBandwidthAccounting->sValue == 'upload')   $sTotal = $sTotal + $sData[1];
          if ($sBandwidthAccounting->sValue == 'download') $sTotal = $sTotal + $sData[2];
          if ($sBandwidthAccounting->sValue == 'both')     $sTotal = $sTotal + $sData[0] + $sData[1];
        }

        $sLastBandwidth = $sVPS->sLastBandwidth;
        if ($sLastBandwidth < $sTotal)
        {
          $sChange = round(($sTotal - $sVPS->sLastBandwidth), 2);
        } else {
          if (!empty($sVPS->sBandwidthUsage)) $sChange = round($sTotal, 2);
        }

        echo "Bandwidth for: {$sVPS->sId} - Total: {$sTotal} - Change: +{$sChange}\n";
        $sVPS->uBandwidthUsage = $sVPS->sBandwidthUsage + $sChange;

        if (($sVPS->uBandwidthUsage) >= ($sVPS->sBandwidthLimit * 1024)) {
          $sSSH = Server::server_connect($sServer);
          $sLog[] = array("command" => "vzctl stop {$sVPS->sContainerId} --fast", "result" => $sSSH->exec("vzctl stop {$sVPS->sContainerId} --fast"));
          $sLog[] = array("command" => "vzctl set {$sVPS->sContainerId} --disabled yes --save", "result" => $sSSH->exec("vzctl set {$sVPS->sContainerId} --disabled yes --save"));
          $sSave = VPS::save_vps_logs($sLog, $sVPS);
          $sVPS->uSuspended = 2;
        } else {
          if ($sVPS->sSuspended > 0) {
            $sVPS->uSuspended = 0;
            $sSSH = Server::server_connect($sServer);
            $sLog[] = array("command" => "vzctl set {$sVPS->sContainerId} --disabled no --save", "result" => $sSSH->exec("vzctl set {$sVPS->sContainerId} --disabled no --save"));
            $sLog[] = array("command" => "vzctl start {$sVPS->sContainerId}", "result" => $sSSH->exec("vzctl start {$sVPS->sContainerId}"));
            $sSave = VPS::save_vps_logs($sLog, $sVPS);
          }
        }

        $sVPS->uLastBandwidth = $sTotal;
        $sVPS->InsertIntoDatabase();

        unset($sData);
        unset($sTotal);
        unset($sChange);
      }
      unset($sPullBandwidth);
    }
    echo "Completed.\n";
    return true;
  }
}
