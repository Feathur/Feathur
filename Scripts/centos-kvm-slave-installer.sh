#!/bin/bash

mkdir ~/Feathur-install/
cd ~/Feathur-install/
touch ~/Feathur-install/install.log
exec 3>&1 > ~/Feathur-install/install.log 2>&1

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
status "What is the name of your trunk interface?"
possibleOptions=$(for i in $(ifconfig -a | grep Link | grep -v inet6 | grep Ethernet | awk '{print $1}'); do echo -n $i; done)
status "Possible options: $possibleOptions";
read trunkinterface
status "What is the name of your volumegroup (Ex: volgroup00):"
read volumegroup
status "What is the name of your volume group backing volume: (Ex: /dev/sda3):"
read volumegroupbackingvolume
status " "
status "Beginning installation..."
## ACTION ##
yum -y install bridge-utils dhcp libvirt qemu-kvm vnstat lvm2 rsync screen wget nano 

cd /etc/yum.repos.d/;wget http://download.opensuse.org/repositories/home:/tsariounov:/cpuset/CentOS_CentOS-6/home:tsariounov:cpuset.repo;
cd ~/Feathur-install/

vgcreate $volumegroup $volumegroupbackingvolume

mkdir -p /var/Feathur/data

cp -R /etc/sysconfig/network-scripts /etc/sysconfig/network-scripts.backup
#trunkconfig=
#trunkconfig=$(egrep -v "(NM_CONTROLLED|IPADDR|GATEWAY|NETMASK|BROADCAST|BOOTPROTO)" /etc/sysconfig/network-scripts/ifcfg-$trunkinterface; echo 'BRIDGE="br0"')
bridgeconfig=$(egrep -v "(NM_CONTROLLED|DEVICE|TYPE)" /etc/sysconfig/network-scripts/ifcfg-br0; echo 'INTERFACE="br0"'; echo 'TYPE="Bridge"')
#echo "$trunkconfig" > /etc/sysconfig/network-scripts/ifcfg-$trunkinterface
#cp /etc/sysconfig/network-scripts/ifcfg-$trunkinterface
echo "$bridgeconfig" > /etc/sysconfig/network-scripts/ifcfg-br0
sed -i '$a BRIDGE="br0"' /etc/sysconfig/network-scripts/ifcfg-$trunkinterface
service network restart
if [[ $(ping -c 3 8.8.8.8 | wc -l) == 5 ]]
then
	/bin/rm -Rf /etc/sysconfig/network-scripts/
	mv /etc/sysconfig/network-scripts.backup /etc/sysconfig/network-scripts
	service network restart
	echo "Error configuring network for bridge. Reverting."
fi

cd /
cd ~
mkdir ~/.ssh/
ssh-keygen -t rsa -N "" -f ~/.ssh/id_rsa
cd ~/.ssh/
cat id_rsa.pub >> ~/.ssh/authorized_keys
key=$(cat id_rsa)
status "Feathur SSH Key:"
status " "
status "$key"
iptables -F && service iptables save
status "Finishing installation"
