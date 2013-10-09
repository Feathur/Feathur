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
