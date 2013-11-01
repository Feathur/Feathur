yum -y update
curl http://download.openvz.org/openvz.repo > /etc/yum.repos.d/openvz.repo
wget http://dl.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-8.noarch.rpm
wget http://rpms.famillecollet.com/enterprise/remi-release-6.rpm
sudo rpm -Uvh remi-release-6*.rpm epel-release-6*.rpm
sed -i 's/SELINUX=.*/SELINUX=disabled/' /etc/sysconfig/selinux
setenforce 0
yum -y install php pigz screen vim vzkernel vzctl
echo "SELINUX=disabled" > /etc/sysconfig/selinux
echo "SELINUXTYPE=targeted" >> /etc/sysconfig/selinux
service httpd restart
cd /var/www/html/
wget https://raw.github.com/BlueVM/Feathur/develop/Scripts/uptime.php
cd /
cd ~
mkdir ~/.ssh/
ssh-keygen -t rsa -N "" -f ~/.ssh/id_rsa
cd ~/.ssh/
cat id_rsa.pub >> ~/.ssh/authorized_keys
cat id_rsa
iptables -F && service iptables save