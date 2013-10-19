#### FEATHUR README UPDATED: OCTOBER 18, 2013

Feathur is a VPS control panel which is
designed to be used to administrate OpenVZ
and KVM VPS. Feathur supports multiple nodes
using a master and slave type functionality.

Feathur is released under the Feathur Use
License Version 1.0. By viewing, editing,
using or downloading Feathur (in whole
or in part) you agree to abide by the terms
set forth in the license provided.

##### SETUP DIRECTIONS (FULL INSTALLER) - DEBIAN 6

As a precaution I would like to take the
time to make sure that if you have a copy
of nginx, mysql, php or apache installed
on your server please DO NOT install Feathur.
Feathur's installer will overwrite any
configuration you have, delete files and
folders you may need and may destroy
anything and everything on your server.

*NOTICE:* This Feathur installer is designed to
run on a VPS and is not designed to be installed
ontop of an OpenVZ or KVM node. If you wish to
install Feathur directly on the node itself
please use a different installer.

Installation requirements:
* Debian 6 (32 or 64 bit)
* Basic Linux Knowledge

Run the following command on your server:

		cd ~ && wget --no-check-certificate https://raw.github.com/BlueVM/Feathur/develop/Scripts/master-installer.sh && bash master-installer.sh