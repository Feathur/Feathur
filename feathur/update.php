<?php
include('./includes/loader.php');

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
