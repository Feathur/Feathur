#!/bin/bash
# For the new installer.
mkdir ~/feathur-install/
cd ~/feathur-install/
touch ~/feathur-install/install.log
exec 3>&1 > ~/feathur-install/install.log 2>&1

function status {
	echo $1;
	echo $1 >&3
}

status "Preparing installer, this may take up to a minute...";
yum -y install python python-platform python-pip screen;
apt-get -y install python python-pip screen;
pip install platform
pip install pythondialog

python -c "import platform; platform.linux_distribution()";

wget --no-check-certificate https://raw.githubusercontent.com/BlueVM/Feathur/Testing/Scripts/install.py

status "Starting installer...";

screen -mS FeathurInstall "python install.py;";