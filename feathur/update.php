<?php
include('./includes/loader.php');

$sSSH = new Net_SSH2('127.0.0.1');
$sKey = new Crypt_RSA();
$sKey->loadKey(file_get_contents($cphp_config->settings->rootkey));
if($sSSH->login("root", $sKey)) {
	$sSSH->exec("mkdir /var/feathur/data/keys;
				chmod 777 /var/feathur/data/keys;
				mkdir /var/feathur/data/templates/;
				mkdir /var/feathur/data/keys;
				mkdir /var/feathur/data/templates/openvz;
				mkdir /var/feathur/data/templates/openvz;
				chmod 777 /var/feathur/data/templates/;
				chmod 777 /var/feathur/data/templates/openvz;
				chmod 777 /var/feathur/data/templates/kvm");
}

$sAdd = $database->prepare("ALTER TABLE `accounts` ADD `forgot` VARCHAR(130);");
$sAdd->execute();

$sAdd = $database->prepare("ALTER TABLE `servers` ADD `volume_group` VARCHAR(130);");
$sAdd->execute();

$sAdd = $database->prepare("ALTER TABLE `servers` ADD `qemu_path` VARCHAR(130);");
$sAdd->execute();

$sAdd = $database->prepare("ALTER TABLE `vps` CHANGE `mac` `mac` TEXT;");
$sAdd->execute();

if(!$sUpdateType = $database->CachedQuery("SELECT * FROM settings WHERE `setting_name` = 'update_type'", array())){
	$sAdd = $database->prepare("INSERT INTO `settings` (`setting_name`, `setting_value`, `setting_group`) VALUES ('update_type', 'develop', 'site_settings')");
	$sAdd->execute();
}
if(!$sBandwidthAccounting = $database->CachedQuery("SELECT * FROM settings WHERE `setting_name` = 'bandwidth_accounting'", array())){
	$sAdd = $database->prepare("INSERT INTO `settings` (`setting_name`, `setting_value`, `setting_group`) VALUES ('bandwidth_accounting', 'both', 'site_settings')");
	$sAdd->execute();
}

$sAdd = $database->prepare("ALTER TABLE `vps` ADD `virtio_network` INT(2)");
$sAdd->execute();

$sAdd = $database->prepare("ALTER TABLE `vps` ADD `virtio_disk` INT(2)");
$sAdd->execute();

$sAdd = $database->prepare("ALTER TABLE `vps` ADD `mac` TEXT");
$sAdd->execute();

$sAdd = $database->prepare("ALTER TABLE `vps` ADD `vnc_port` INT(16)");
$sAdd->execute();

$sAdd = $database->prepare("ALTER TABLE `vps` ADD `boot_order` INT(2)");
$sAdd->execute();