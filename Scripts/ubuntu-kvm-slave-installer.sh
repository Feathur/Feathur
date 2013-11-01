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
status "Feathur KVM slave CentOS installation."
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
status "What is the name of your trunk interface (Ex: p1p1):"
read trunkinterface
status "What is the name of your volumegroup (Ex: volgroup00):"
read volumegroup
status "What is the name of your volume group backing volume: (Ex: /dev/sda3):"
read volumegroupbackingvolume
status " "
status "Beginning installation..."
## ACTION ##
apt-get -y install bridge-utils isc-dhcp-server libvirt-bin libvirt0 qemu-kvm vnstat lvm2 rsync perl

vgcreate $volumegroup $volumegroupbackingvolume

mkdir -p /var/feathur/data

cp /etc/network/interfaces /etc/network/interfaces.backup
perl -0777 -i.original -pe "s/auto $trunkinterface\niface $trunkinterface inet static/auto $trunkinterface\niface $trunkinterface inet manual\n\nauto br0\niface br0 inet static\nbridge_ports $trunkInterface" testnet
service network restart
if [[ `ping -c 3 8.8.8.8 | wc -l` == 5 ]]
then
	rm /etc/network/interfaces
	mv /etc/network/interfaces.backup /etc/network/interfaces
	service network restart
	status "Error configuring network for bridge. Reverting."
	exit 1;
fi

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