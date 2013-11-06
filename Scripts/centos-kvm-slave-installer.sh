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
status "What is the name of your trunk interface (Ex: eth0):"
read trunkinterface
status "What is the name of your volumegroup (Ex: volgroup00):"
read volumegroup
status "What is the name of your volume group backing volume: (Ex: /dev/sda3):"
read volumegroupbackingvolume
status " "
status "Beginning installation..."
## ACTION ##
yum -y install bridge-utils dhcp libvirt qemu-kvm vnstat lvm2 httpd php rsync screen

vgcreate $volumegroup $volumegroupbackingvolume

mkdir -p /var/feathur/data

cp -R /etc/sysconfig/network-scripts /etc/sysconfig/network-scripts.backup
$trunkconfig=`cat /etc/sysconfig/network-scripts/ifcfg-$trunkinterface | grep -v NM_CONTROLLED | grep -v IPADDR | grep -v GATEWAY | grep -v NETMASK | grep -v BROADCAST | grep -v BOOTPROTO; echo 'BRIDGE="br0"'`
$bridgeconfig = `cat /etc/sysconfig/network-scripts/ifcfg-$trunkinterface | grep -v NM_CONTROLLED | grep -v DEVICE | grep -v TYPE; echo 'INTERFACE="br0"'; echo 'TYPE="Bridge"'`
echo "$trunkconfig" > /etc/sysconfig/network-scripts/ifcfg-$trunkinterface
echo "$bridgeconfig" > /etc/sysconfig/network-scripts/ifcfg-br0
service network restart
if [[ `ping -c 3 8.8.8.8 | wc -l` == 5 ]]
then
	rm -Rf /etc/sysconfig/network-scripts/
	mv /etc/sysconfig/network-scripts.backup /etc/sysconfig/network-scripts
	service network restart
	echo "Error configuring network for bridge. Reverting."
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
