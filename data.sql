
CREATE TABLE IF NOT EXISTS `accounts` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `username` varchar(65) NOT NULL,
  `email_address` varchar(130) NOT NULL,
  `password` varchar(130) NOT NULL,
  `permissions` int(2) NOT NULL DEFAULT '0',
  `salt` varchar(65) NOT NULL,
  `activation_code` varchar(130) NOT NULL,
  `forgot` varchar(130) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=73;

INSERT INTO `accounts` (`id`, `username`, `email_address`, `password`, `permissions`, `salt`) VALUES
(1, 'Admin', 'admin@company.com', '6UkIdjBU6DahbrMTbPLI/wv0ZYEfzuTg3l5hdMQixE7', 7, 'BKUIVStg0KV3n3vAUr6bWtJ5IjTDkf');

CREATE TABLE IF NOT EXISTS `blocks` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(65) NOT NULL,
  `gateway` varchar(65) NOT NULL,
  `netblock` varchar(65) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

CREATE TABLE IF NOT EXISTS `history` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `server_id` int(16) NOT NULL,
  `status` int(2) NOT NULL,
  `timestamp` int(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=629 ;

CREATE TABLE IF NOT EXISTS `ipaddresses` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `vps_id` int(8) NOT NULL,
  `block_id` int(8) NOT NULL,
  `ip_address` varchar(65) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=58 ;

CREATE TABLE IF NOT EXISTS `servers` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(65) NOT NULL,
  `user` varchar(65) NOT NULL,
  `ip_address` varchar(65) NOT NULL,
  `key` varchar(65) NOT NULL,
  `type` varchar(65) NOT NULL,
  `password` int(2) NOT NULL DEFAULT '0',
  `url` varchar(255) NOT NULL,
  `port` int(4) NOT NULL,
  `status_type` varchar(16) NOT NULL,
  `location` varchar(130) NOT NULL,
  `status` int(2) NOT NULL,
  `status_warning` int(2) NOT NULL,
  `last_check` int(16) NOT NULL,
  `previous_check` int(16) NOT NULL,
  `up_since` int(16) NOT NULL,
  `down_since` int(16) NOT NULL,
  `alert_after` int(16) NOT NULL,
  `load_alert` varchar(16) NOT NULL,
  `ram_alert` varchar(8) NOT NULL,
  `hard_disk_alert` varchar(8) NOT NULL,
  `display_memory` int(2) NOT NULL,
  `display_load` int(2) NOT NULL,
  `display_hard_disk` int(2) NOT NULL,
  `display_network_uptime` int(2) NOT NULL,
  `display_hardware_uptime` int(11) NOT NULL,
  `display_location` int(2) NOT NULL,
  `display_history` int(2) NOT NULL,
  `display_statistics` int(2) NOT NULL,
  `display_hs` int(2) NOT NULL,
  `display_bandwidth` int(2) NOT NULL,
  `hardware_uptime` varchar(32) NOT NULL,
  `total_memory` varchar(32) NOT NULL,
  `free_memory` varchar(32) NOT NULL,
  `load_average` varchar(32) NOT NULL,
  `hard_disk_free` varchar(32) NOT NULL,
  `hard_disk_total` varchar(32) NOT NULL,
  `bandwidth` int(64) NOT NULL,
  `last_bandwidth` int(64) NOT NULL,
  `container_bandwidth` int(2) NOT NULL,
  `bandwidth_timestamp` int(32) NOT NULL,
  `volume_group` varchar(65) NOT NULL,
  `qemu_path` varchar(65) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

CREATE TABLE IF NOT EXISTS `server_blocks` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `server_id` int(8) NOT NULL,
  `block_id` int(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

CREATE TABLE IF NOT EXISTS `server_commands` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `server_id` int(8) NOT NULL,
  `command` text NOT NULL,
  `interval` int(8) NOT NULL,
  `last` int(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

CREATE TABLE IF NOT EXISTS `server_logs` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `server_id` int(8) NOT NULL,
  `entry` text NOT NULL,
  `command` text NOT NULL,
  `timestamp` int(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1204 ;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `setting_name` varchar(65) NOT NULL,
  `setting_value` varchar(65) NOT NULL,
  `setting_group` varchar(65) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=23 ;

INSERT INTO `settings` (`id`, `setting_name`, `setting_value`, `setting_group`) VALUES
(7, 'title', 'Feathur', 'site_settings'),
(8, 'description', 'VPS Management', 'site_settings'),
(9, 'template', 'default', 'site_settings'),
(11, 'container_id', '101', 'site_settings'),
(12, 'admin_template', 'admin', 'site_settings'),
(13, 'max_return', '1', 'site_settings'),
(14, 'pull_difference', '59', 'site_settings'),
(15, 'history_difference', '5', 'site_settings'),
(16, 'refresh_time', '10', 'site_settings'),
(17, 'max_history', '30', 'site_settings'),
(18, 'max_statistics', '30', 'site_settings'),
(19, 'graph_limit', '7', 'site_settings'),
(20, 'allow_user_notifications', '1', 'site_settings'),
(21, 'panel_url', 'localhost', 'site_settings'),
(22, 'maintenance', '0', 'site_settings'),
(23, 'automatic_updates', '1', 'site_settings'),
(24, 'current_version', '0.5.6.4', 'site_settings'),
(25, 'last_update_check', '0', 'site_settings'),
(26, 'last_template_sync', '0', 'site_settings'),
(27, 'sendgrid', '0', 'site_settings'),
(28, 'sendgrid_username', '', 'site_settings'),
(29, 'sendgrid_password', '', 'site_settings'),
(50, 'update_type', 'develop', 'site_settings'),
(59, 'bandwidth_accounting', 'both', 'site_settings'),
(60, 'license', '0', 'site_settings');

CREATE TABLE IF NOT EXISTS `statistics` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `server_id` int(8) NOT NULL,
  `status` int(2) NOT NULL,
  `hardware_uptime` varchar(32) NOT NULL,
  `total_memory` varchar(32) NOT NULL,
  `free_memory` varchar(32) NOT NULL,
  `load_average` varchar(32) NOT NULL,
  `hard_disk_free` varchar(32) NOT NULL,
  `hard_disk_total` varchar(32) NOT NULL,
  `bandwidth` int(32) NOT NULL,
  `timestamp` int(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17331 ;

CREATE TABLE IF NOT EXISTS `templates` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(65) NOT NULL,
  `path` varchar(65) NOT NULL,
  `type` varchar(65) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=16 ;

CREATE TABLE IF NOT EXISTS `transfers` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `vps_id` int(8) NOT NULL,
  `from_server` int(8) NOT NULL,
  `to_server` int(8) NOT NULL,
  `phase` int(2) NOT NULL,
  `completed` int(2) NOT NULL,
  `cleanup` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

CREATE TABLE IF NOT EXISTS `vps` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `user_id` int(8) NOT NULL,
  `server_id` int(8) NOT NULL,
  `container_id` int(8) NOT NULL,
  `hostname` varchar(130) NOT NULL,
  `primary_ip` varchar(65) NOT NULL,
  `type` varchar(8) NOT NULL,
  `ram` int(8) NOT NULL,
  `swap` int(8) NOT NULL,
  `disk` varchar(8) NOT NULL,
  `cpuunits` int(8) NOT NULL,
  `cpulimit` int(8) NOT NULL,
  `bandwidthlimit` decimal(65,4) NOT NULL,
  `nameserver` varchar(65) NOT NULL,
  `numiptent` int(8) NOT NULL,
  `numproc` int(8) NOT NULL,
  `inodes` int(8) NOT NULL,
  `template_id` int(8) NOT NULL,
  `suspended` int(2) NOT NULL,
  `suspending_admin` int(8) NOT NULL,
  `bandwidth_usage` int(65) NOT NULL,
  `mac` varchar(65) NOT NULL,
  `vnc_port` int(16) NOT NULL,
  `boot_order` varchar(65) NOT NULL,
  `virtio_network` int(2) NOT NULL,
  `virtio_disk` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=90 ;

CREATE TABLE IF NOT EXISTS `vps_logs` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `timestamp` int(16) NOT NULL,
  `vps_id` int(8) NOT NULL,
  `command` text NOT NULL,
  `entry` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=356 ;

CREATE TABLE IF NOT EXISTS `attempts` (
`id` INT( 16 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ip_address` VARCHAR( 65 ) NOT NULL ,
`timestamp` INT( 16 ) NOT NULL ,
`type` VARCHAR( 65 ) NOT NULL
) ENGINE = MYISAM ;

USE dns;

create table domains (
 id INT auto_increment,
 name VARCHAR(255) NOT NULL,
 master VARCHAR(128) DEFAULT NULL,
 last_check INT DEFAULT NULL,
 type VARCHAR(6) NOT NULL,
 notified_serial INT DEFAULT NULL,
 account VARCHAR(40) DEFAULT NULL,
 primary key (id)
) Engine=InnoDB;

CREATE UNIQUE INDEX name_index ON domains(name);

CREATE TABLE records (
  id INT auto_increment,
  domain_id INT DEFAULT NULL,
  name VARCHAR(255) DEFAULT NULL,
  type VARCHAR(10) DEFAULT NULL,
  content VARCHAR(64000) DEFAULT NULL,
  ttl INT DEFAULT NULL,
  prio INT DEFAULT NULL,
  change_date INT DEFAULT NULL,
  primary key(id)
) Engine=InnoDB;

CREATE INDEX rec_name_index ON records(name);
CREATE INDEX nametype_index ON records(name,type);
CREATE INDEX domain_id ON records(domain_id);

create table supermasters (
  ip VARCHAR(25) NOT NULL,
  nameserver VARCHAR(255) NOT NULL,
  account VARCHAR(40) DEFAULT NULL
) Engine=InnoDB;