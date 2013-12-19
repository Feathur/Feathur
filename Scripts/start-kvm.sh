#!/bin/bash

# Define possible types
exists='already exists'
missing='No such file'
isomissing='iso'
diskmissing='img'
created='created from'

url=${2// /.}
iso=${3// /.}
isotemp="/var/feathur/data/templates/kvm/temp_$iso.iso"
startup=$(virsh create /var/feathur/configs/kvm$1-vps.xml 2>&1)
ver=$(virsh --version 2>&1)
version=${ver:0:1}


if [[ "$startup" == *"$exists"* ]]
then
	echo 1;
	exit 1;
fi

if [[ "$startup" == *"$missing"* ]]
then
	if [[ "$startup" == *"$isomissing"* ]]
	then
	
		if [ -f "$isotemp" ];
		then
			echo 2;
			exit 1;
		fi
		
		if [ $version = "1" ];
		then
			if [[ "$4" == *"feathurpassword"* ]]
			then
				screen -dmS template bash -c "mkdir -p /var/feathur/data/templates/kvm/;cd /var/feathur/data/templates/kvm/;wget -O temp_$iso.iso --no-check-certificate '$url/templates/kvm/$iso.iso';mv temp_$iso.iso $iso.iso;virsh create /var/feathur/configs/kvm$1-vps.xml;cd /var/feathur/data/templates/kvm/;rm -rf *index.html*;rm -rf *template_sync*;cd /var/feathur/data/;python /var/feathur/data/balancer.py;sleep 600;"
			else
				screen -dmS template bash -c "mkdir -p /var/feathur/data/templates/kvm/;cd /var/feathur/data/templates/kvm/;wget -O temp_$iso.iso --no-check-certificate '$url/templates/kvm/$iso.iso';mv temp_$iso.iso $iso.iso;virsh create /var/feathur/configs/kvm$1-vps.xml;virsh qemu-monitor-command kvm$1 --hmp change vnc :$5;virsh qemu-monitor-command kvm$1 --hmp change vnc password $4 ;cd /var/feathur/data/templates/kvm/;rm -rf *index.html*;rm -rf *template_sync*;cd /var/feathur/data/;python /var/feathur/data/balancer.py;sleep 600;"
			fi
		else
			screen -dmS template bash -c "mkdir -p /var/feathur/data/templates/kvm/;cd /var/feathur/data/templates/kvm/;wget -O temp_$iso.iso --no-check-certificate '$url/templates/kvm/$iso.iso';mv temp_$iso.iso $iso.iso;virsh create /var/feathur/configs/kvm$1-vps.xml;cd /var/feathur/data/templates/kvm/;rm -rf *index.html*;rm -rf *template_sync*;cd /var/feathur/data/;python /var/feathur/data/balancer.py;sleep 600;"
		fi	
		echo 2;
		exit 1;
		
	fi
	
	if [[ "$startup" == *"$diskmissing"* ]]
	then
		echo 3;
		exit 1;
	fi
	
fi

if [[ "$startup" == *"$created"* ]]
then
	if [ $version = "1" ];
	then
		if [[ "$4" == *"feathurpassword"* ]]
		then
			screen -dmS balance bash -c "cd /var/feathur/data/;python /var/feathur/data/balancer.py;sleep 600;"
		else
			screen -dmS balance bash -c "virsh qemu-monitor-command kvm$1 --hmp change vnc :$5;virsh qemu-monitor-command kvm$1 --hmp change vnc password $4;cd /var/feathur/data/;python /var/feathur/data/balancer.py;sleep 600;"
		fi
	else
		screen -dmS balance bash -c "cd /var/feathur/data/;python /var/feathur/data/balancer.py;sleep 600;"
	fi
	echo 4;
	exit 1;
fi

echo 5;
exit 1;