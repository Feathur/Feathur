#!/bin/bash

mkdir ~/feathur-install/
cd ~/feathur-install/
touch ~/feathur-install/install.log
exec 3>&1 > ~/feathur-install/install.log 2>&1

############################################################
# Functions
############################################################

function status {
	echo $1;
	echo $1 >&3
}

############################################################
# Begin Installation
############################################################

status "====================================="
status "     Welcome to Feathur Installation"
status "====================================="
status " "
status "Feathur KVM slave Ubuntu installation."
status " "
status "Feathur will install KVM along with"
status "several other tools for VPS management."
status " "
status "It is recommended that you run this"
status "installer in a screen."
status " "
status "This script will begin installing"
status "Feathur in 10 seconds. If you wish to"
status "cancel the install press CTRL + C"
sleep 10
status "Feathur needs a bit of information before"
status "beginning the installation."
status " "
status "What is the name of your trunk interface?"
possibleOptions=$(for i in `ifconfig -a | grep Link | grep -v inet 6 | grep Ethernet | awk '{print $1}'`; do echo -n $i; done)
status "Possible options: $possibleOptions";
read trunkinterface
status "What is the name of your volumegroup (Ex: volgroup00):"
read volumegroup
status "What is the name of your volume group backing volume: (Ex: /dev/sda3):"
read volumegroupbackingvolume
status " "
status "Beginning installation..."
## ACTION ##
apt-get -y install bridge-utils isc-dhcp-server libvirt-bin libvirt0 qemu-kvm vnstat lvm2 rsync perl apache2 php5-common libapache2-mod-php5 php5-cli

vgcreate $volumegroup $volumegroupbackingvolume

mkdir -p /var/feathur/data

perl -0777 -i.original -pe "s/auto $trunkinterface\niface $trunkinterface inet static/auto $trunkinterface\niface $trunkinterface inet manual\n\nauto br0\niface br0 inet static\n\tbridge_ports $trunkinterface/" /etc/network/interfaces
service networking restart
if [[ `ping -c 3 8.8.8.8 | wc -l` == 5 ]]
then
	rm /etc/network/interfaces
	mv /etc/network/interfaces.original /etc/network/interfaces
	service networking restart
	status "Error configuring network for bridge. Reverting."
	exit 1;
fi

cd /var/www/html/
wget https://raw.github.com/BlueVM/Feathur/develop/Scripts/uptime.php
cd /
cd ~
mkdir ~/.ssh/
ssh-keygen -t rsa -N "" -f ~/.ssh/id_rsa
cd ~/.ssh/
cat id_rsa.pub >> ~/.ssh/authorized_keys
key=`cat id_rsa`
status "Feathur SSH Key:"
status " "
status "$key"
iptables -F && service iptables save
status "Finishing installation"
