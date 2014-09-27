<?php
require_once('./includes/loader.php');
$sCurrentVersion = Core::GetSetting('current_version');

/*
 * Fix up database for versions < 0.6.1.9
 */

if (version_compare($sCurrentVersion->sValue, '0.6.1.9', '<'))
{
  $sAdd = $database->prepare("ALTER TABLE `servers` ADD `qemu_path` VARCHAR(130);");
  $sAdd->execute();

  $sAdd = $database->prepare("ALTER TABLE `vps` ADD `virtio_network` INT(2)");
  $sAdd->execute();

  $sAdd = $database->prepare("ALTER TABLE `vps` ADD `virtio_disk` INT(2)");
  $sAdd->execute();

  $sAdd = $database->prepare("ALTER TABLE `vps` ADD `mac` TEXT");
  $sAdd->execute();

  $sAdd = $database->prepare("ALTER TABLE `vps` ADD `vnc_port` INT(16)");
  $sAdd->execute();

  $sAdd = $database->prepare("ALTER TABLE `vps` ADD `boot_order` VARCHAR(65)");
  $sAdd->execute();

  $sAdd = $database->prepare("CREATE TABLE IF NOT EXISTS `attempts` (`id` INT( 16 ) NOT NULL AUTO_INCREMENT PRIMARY KEY , `ip_address` VARCHAR( 65 ) NOT NULL , `timestamp` INT( 16 ) NOT NULL , `type` VARCHAR( 65 ) NOT NULL) ENGINE = MYISAM ;");
  $sAdd->execute();

  $sAdd = $database->prepare("ALTER TABLE `blocks` CHANGE `netblock` `netmask` VARCHAR(65)");
  $sAdd->execute();

  $sAdd = $database->prepare("ALTER TABLE `vps` ADD `rebuilding` INT(2)");
  $sAdd->execute();
}

/*
 * Fix up database for versions < 0.6.2.0
 */

if (version_compare($sCurrentVersion->sValue, '0.6.2.0', '<'))
{
  $sAdd = $database->prepare("ALTER TABLE `vps` CHANGE `virtio_disk` `disk_driver` VARCHAR(65)");
  $sAdd->execute();

  $sAdd = $database->prepare("ALTER TABLE `vps` CHANGE `virtio_network` `network_driver` VARCHAR(65)");
  $sAdd->execute();

  if (!$sFindSetting = $database->CachedQuery('SELECT * FROM settings WHERE `setting_name` LIKE :Setting', array('Setting' => 'refresh_time')))
  {
    $sAdd = $database->prepare("INSERT INTO settings(setting_name, setting_value, setting_group) VALUES('refresh_time', '10', 'site_settings')");
    $sAdd->execute();
  }

  $sAdd = $database->prepare("ALTER TABLE `vps` ADD `private_network` INT(2);");
  $sAdd->execute();

  if(!$sFindSetting = $database->CachedQuery('SELECT * FROM settings WHERE `setting_name` LIKE :Setting', array('Setting' => 'panel_mode')))
  {
    $sAdd = $database->prepare("INSERT INTO settings(setting_name, setting_value, setting_group) VALUES('panel_mode', 'https://', 'site_settings')");
    $sAdd->execute();
  }
}

/*
 * Fix up database for versions < 0.6.3.0
 */

if (version_compare($sCurrentVersion->sValue, '0.6.3.0', '<'))
{
  $sAdd = $database->prepare("UPDATE `settings` SET `setting_name` = 'mail' WHERE `setting_name` = 'sendgrid'");
  $sAdd->execute();

  $sAdd = $database->prepare("UPDATE `settings` SET `setting_name` = 'mail_username' WHERE `setting_name` = 'sendgrid_username'");
  $sAdd->execute();

  $sAdd = $database->prepare("UPDATE `settings` SET `setting_name` = 'mail_password' WHERE `setting_name` = 'sendgrid_password'");
  $sAdd->execute();
}

/*
 * Fix up database for versions < 0.6.3.4
 */

if (version_compare($sCurrentVersion->sValue, '0.6.3.4', '<'))
{
  $sAdd = $database->prepare("ALTER TABLE `vps` ADD `last_bandwidth` decimal(65,4)");
  $sAdd->execute();

  $sAdd = $database->prepare("ALTER TABLE `blocks` CHANGE `bandwidth_usage` `bandwidth_usage` decimal(65,4)");
  $sAdd->execute();
}

/*
 * Add IPv6 support to server_blocks
 */

$sAdd = $database->prepare("ALTER TABLE `server_blocks` ADD `ipv6` int(2) NOT NULL DEFAULT 0;");
$sAdd->execute();

/*
 * Add IPv6 support to vps
 */

$sAdd = $database->prepare("ALTER TABLE `vps` ADD `ipv6` int(2) NOT NULL DEFAULT 0;");
$sAdd->execute();

/*
 * Add IPv6 support to blocks
 */

$sAdd = $database->prepare("ALTER TABLE `blocks` ADD (`ipv6` int(2) NOT NULL DEFAULT 0, `prefix` varchar(65) NOT NULL, `per_user` int(8) NOT NULL, `current` varchar(65) NOT NULL, `secondary` varchar(65) NOT NULL);");
$sAdd->execute();

/*
 * Add IPv6 address table
 */

$sAdd = $database->prepare("CREATE TABLE IF NOT EXISTS `ipv6addresses` (`id` int(8) NOT NULL AUTO_INCREMENT, `vps_id` int(8) NOT NULL, `block_id` int(8) NOT NULL, `suffix` varchar(65) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=58 ;");
$sAdd->execute();

/*
 * Add IPv6 user blocks table
 */

$sAdd = $database->prepare("CREATE TABLE IF NOT EXISTS `useripv6blocks` (`id` int(8) NOT NULL AUTO_INCREMENT, `vps_id` int(8) NOT NULL, `block_id` int(8) NOT NULL, `user_block` varchar(65) NOT NULL, `current` varchar(65) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=58;");
$sAdd->execute();

/*
 * Modify blocks table, column per_user to VARCHAR(65)
 */

$sAdd = $database->prepare("ALTER TABLE `blocks` CHANGE `per_user` `per_user` VARCHAR(65) NOT NULL;");
$sAdd->execute();

/*
 * Add tuntap, ppp, iptables support to vps table
 */

$sAdd = $database->prepare("ALTER TABLE `vps` ADD `tuntap` INT(2);ALTER TABLE `vps` ADD `ppp` INT(2);ALTER TABLE `vps` ADD `iptables` INT(2);");
$sAdd->execute();

/*
 * Add secondary drive column to vps table
 */

$sAdd = $database->prepare("ALTER TABLE `vps` ADD `secondary_drive` VARCHAR(130);");
$sAdd->execute();

/*
 * Add current column to user ipv6 blocks table
 */

$sAdd = $database->prepare("ALTER TABLE `useripv6blocks` ADD `current` varchar(65) NOT NULL;");
$sAdd->execute();

/*
 * Add userblock_id column to ipv6addresses table
 */

$sAdd = $database->prepare("ALTER TABLE `ipv6addresses` ADD `userblock_id` INT(8) NOT NULL;");
$sAdd->execute();

/*
 * Add SMTP tracker table
 */

$sAdd = $database->prepare("CREATE TABLE IF NOT EXISTS `smtp` (`id` int(8) NOT NULL AUTO_INCREMENT, `vps_id` int(8) NOT NULL, `connections` int(8) NOT NULL, `timestamp` int(16) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0;");
$sAdd->execute();

/*
 * Insert default max_smtp_connections limit into settings table
 */

if (!$sFindSetting = $database->CachedQuery('SELECT * FROM settings WHERE `setting_name` LIKE :Setting', array('Setting' => 'max_smtp_connections')))
{
  $sAdd = $database->prepare("INSERT INTO settings(setting_name, setting_value, setting_group) VALUES('max_smtp_connections', '8', 'site_settings')");
  $sAdd->execute();
}

/*
 * Add SMTP tracker whitelist column to vps table
 */

$sAdd = $database->prepare("ALTER TABLE `vps` ADD `smtp_whitelist` INT(2);");
$sAdd->execute();

/*
 * Add default template_redone_message to settings table
 */

if (!$sFindSetting = $database->CachedQuery('SELECT * FROM settings WHERE `setting_name` LIKE :Setting', array('Setting' => 'template_redone_message')))
{
  $sAdd = $database->prepare("INSERT INTO settings(setting_name, setting_value, setting_group) VALUES('template_redone_message', '0', 'site_settings')");
  $sAdd->execute();
}

/*
 * Clean up VPS template list
 */

if(!$sFindSetting = $database->CachedQuery('SELECT * FROM settings WHERE `setting_name` LIKE :Setting', array('Setting' => 'templates_cleaned')))
{
  // Create new table.
  $sAdd = $database->prepare("CREATE TABLE IF NOT EXISTS `new_templates` (`id` int(8) NOT NULL AUTO_INCREMENT, `name` varchar(65) NOT NULL, `path` varchar(65) NOT NULL, `url` varchar(255) NOT NULL, `type` varchar(65) NOT NULL, `disabled` int(2) NOT NULL, `size` int(65) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16;");
  $sAdd->execute();
	
  // Rename old table, rename new table.
  $sAdd = $database->prepare("RENAME TABLE `templates` TO `templates_old`, `new_templates` TO `templates`;");
  $sAdd->execute();
	
  // Remove all VPS template settings just to make sure there's no overlap.
  $sAdd = $database->prepare("UPDATE `vps` SET `template_id` = '0';");
  $sAdd->execute();

  // Make sure this only happens once.
  $sAdd = $database->prepare("INSERT INTO settings(setting_name, setting_value, setting_group) VALUES('templates_cleaned', '1', 'site_settings')");
  $sAdd->execute();
}

/*
 * Add iso_syncing column to vps table
 */

$sAdd = $database->prepare("ALTER TABLE `vps` ADD `iso_syncing` INT(2);");
$sAdd->execute();

/*
 * Update path column in templates table to VARCHAR(255)
 */

$sAdd = $database->prepare("ALTER TABLE `templates` CHANGE `path` `path` VARCHAR(255) NOT NULL;");
$sAdd->execute();

// Create new table for group storage.
$sAdd = $database->prepare("CREATE TABLE IF NOT EXISTS `groups` (`id` int(8) NOT NULL AUTO_INCREMENT, `name` varchar(65) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16;");
$sAdd->execute();

// Create new table for server_groups storage.
$sAdd = $database->prepare("CREATE TABLE IF NOT EXISTS `server_groups` (`id` int(8) NOT NULL AUTO_INCREMENT, `server_id` int(8) NOT NULL, `group_id` int(8) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16;");
$sAdd->execute();
