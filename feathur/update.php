<?php
include('./includes/loader.php');

$sCurrentVersion = Core::GetSetting('current_version');

if(version_compare($sCurrentVersion->sValue, '0.6.1.9', '<')) {
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

if(version_compare($sCurrentVersion->sValue, '0.6.2.0', '<')) {
	$sAdd = $database->prepare("ALTER TABLE `vps` CHANGE `virtio_disk` `disk_driver` VARCHAR(65)");
	$sAdd->execute();

	$sAdd = $database->prepare("ALTER TABLE `vps` CHANGE `virtio_network` `network_driver` VARCHAR(65)");
	$sAdd->execute();

	if(!$sFindSetting = $database->CachedQuery("SELECT * FROM settings WHERE `setting_name` LIKE :Setting", array('Setting' => "refresh_time"))){
		$sAdd = $database->prepare("INSERT INTO settings(setting_name, setting_value, setting_group) VALUES('refresh_time', '10', 'site_settings')");
		$sAdd->execute();
	}

	$sAdd = $database->prepare("ALTER TABLE `vps` ADD `private_network` INT(2);");
	$sAdd->execute();

	if(!$sFindSetting = $database->CachedQuery("SELECT * FROM settings WHERE `setting_name` LIKE :Setting", array('Setting' => "panel_mode"))){
		$sAdd = $database->prepare("INSERT INTO settings(setting_name, setting_value, setting_group) VALUES('panel_mode', 'https://', 'site_settings')");
		$sAdd->execute();
	}
}

if(version_compare($sCurrentVersion->sValue, '0.6.3.0', '<')) {
	$sAdd = $database->prepare("UPDATE `settings` SET `setting_name` = 'mail' WHERE `setting_name` = 'sendgrid'");
	$sAdd->execute();

	$sAdd = $database->prepare("UPDATE `settings` SET `setting_name` = 'mail_username' WHERE `setting_name` = 'sendgrid_username'");
	$sAdd->execute();

	$sAdd = $database->prepare("UPDATE `settings` SET `setting_name` = 'mail_password' WHERE `setting_name` = 'sendgrid_password'");
	$sAdd->execute();
}

if(version_compare($sCurrentVersion->sValue, '0.6.3.4', '<')) {
	$sAdd = $database->prepare("ALTER TABLE `vps` ADD `last_bandwidth` decimal(65,4)");
	$sAdd->execute();

	$sAdd = $database->prepare("ALTER TABLE `blocks` CHANGE `bandwidth_usage` `bandwidth_usage` decimal(65,4)");
	$sAdd->execute();
}

$sAdd = $database->prepare("ALTER TABLE `server_blocks` ADD `ipv6` int(2) NOT NULL DEFAULT 0;");
$sAdd->execute();

$sAdd = $database->prepare("ALTER TABLE `vps` ADD `ipv6` int(2) NOT NULL DEFAULT 0;");
$sAdd->execute();


$sAdd = $database->prepare("ALTER TABLE `blocks` ADD (`ipv6` int(2) NOT NULL DEFAULT 0, `prefix` varchar(65) NOT NULL, `per_user` int(8) NOT NULL, `current` varchar(65) NOT NULL, `secondary` varchar(65) NOT NULL);");
$sAdd->execute();

$sAdd = $database->prepare("CREATE TABLE IF NOT EXISTS `ipv6addresses` (`id` int(8) NOT NULL AUTO_INCREMENT, `vps_id` int(8) NOT NULL, `block_id` int(8) NOT NULL, `suffix` varchar(65) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=58 ;");
$sAdd->execute();

$sAdd = $database->prepare("CREATE TABLE IF NOT EXISTS `useripv6blocks` (`id` int(8) NOT NULL AUTO_INCREMENT, `vps_id` int(8) NOT NULL, `block_id` int(8) NOT NULL, `user_block` varchar(65) NOT NULL, `current` varchar(65) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=58;");
$sAdd->execute();

$sAdd = $database->prepare("ALTER TABLE `blocks` CHANGE `per_user` `per_user` VARCHAR(65) NOT NULL;");
$sAdd->execute();

// Just in case.
$sAdd = $database->prepare("ALTER TABLE `vps` ADD `tuntap` INT(2);ALTER TABLE `vps` ADD `ppp` INT(2);ALTER TABLE `vps` ADD `iptables` INT(2);");
$sAdd->execute();

$sAdd = $database->prepare("ALTER TABLE `vps` ADD `secondary_drive` VARCHAR(130);");
$sAdd->execute();

$sAdd = $database->prepare("ALTER TABLE `useripv6blocks` ADD `current` varchar(65) NOT NULL;");
$sAdd->execute();

$sAdd = $database->prepare("ALTER TABLE `ipv6addresses` ADD `userblock_id` INT(8) NOT NULL;");
$sAdd->execute();

$sAdd = $database->prepare("CREATE TABLE IF NOT EXISTS `smtp` (`id` int(8) NOT NULL AUTO_INCREMENT, `vps_id` int(8) NOT NULL, `connections` int(8) NOT NULL, `timestamp` int(16) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=0;");
$sAdd->execute();

if(!$sFindSetting = $database->CachedQuery("SELECT * FROM settings WHERE `setting_name` LIKE :Setting", array('Setting' => "max_smtp_connections"))){
	$sAdd = $database->prepare("INSERT INTO settings(setting_name, setting_value, setting_group) VALUES('max_smtp_connections', '8', 'site_settings')");
	$sAdd->execute();
}

$sAdd = $database->prepare("ALTER TABLE `vps` ADD `smtp_whitelist` INT(2);");
$sAdd->execute();

if(!$sFindSetting = $database->CachedQuery("SELECT * FROM settings WHERE `setting_name` LIKE :Setting", array('Setting' => "template_redone_message"))){
	$sAdd = $database->prepare("INSERT INTO settings(setting_name, setting_value, setting_group) VALUES('template_redone_message', '0', 'site_settings')");
	$sAdd->execute();
}

if(!$sFindSetting = $database->CachedQuery("SELECT * FROM settings WHERE `setting_name` LIKE :Setting", array('Setting' => "templates_cleaned"))){
	// Create new table.
	$sAdd = $database->prepare("CREATE TABLE `new_templates`(`id` int(8) NOT NULL AUTO_INCREMENT, `name` varchar(65) NOT NULL, `path` varchar(65) NOT NULL, `url` varchar(255) NOT NULL, `type` varchar(65) NOT NULL, `disabled` int(2) NOT NULL, `size` int(65) NOT NULL, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16;");
	$sAdd->execute();
	
	// Rename old table, rename new table.
	$sAdd = $database->prepare("RENAME TABLE `templates` TO `templates_old`, `new_templates` TO `templates`;");
	$sAdd->execute();
	
	// Make sure this only happens once.
	$sAdd = $database->prepare("INSERT INTO settings(setting_name, setting_value, setting_group) VALUES('templates_cleaned', '1', 'site_settings')");
	$sAdd->execute();
}