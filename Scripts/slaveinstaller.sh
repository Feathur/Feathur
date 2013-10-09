pvresize /dev/xvda3
lvextend VolGroup/LogVol00 -l +100%FREE
resize2fs /dev/VolGroup/LogVol00
sed -i 's/SELINUX=.*/SELINUX=disabled/' /etc/sysconfig/selinux
wget http://dl.fedoraproject.org/pub/epel/6/x86_64/epel-release-6-8.noarch.rpm
wget http://rpms.famillecollet.com/enterprise/remi-release-6.rpm
sudo rpm -Uvh remi-release-6*.rpm epel-release-6*.rpm
yum -y install php pigz screen vim
service httpd restart
cd /var/www/html/
wget http://develop.bvm.mx/scripts/uptime.txt
mv uptime.txt uptime.php
cd /
mkdir scripts
cd /scripts/
wget http://develop.bvm.mx/scripts/list-ips.txt
wget http://develop.bvm.mx/scripts/count-traffic.txt
mv list-ips.txt list-ips.sh
mv count-traffic.txt count-traffic.sh
mkdir traffic
cd ~
mkdir ~/.ssh/
ssh-keygen -t rsa -N "" -f ~/.ssh/id_rsa
cd ~/.ssh/
cat id_rsa.pub >> ~/.ssh/authorized_keys
wget http://develop.bvm.mx/scripts/keys.txt
cat keys.txt >> ~/.ssh/authorized_keys
rm -rf keys.txt
cat id_rsa
iptables -F && service iptables save