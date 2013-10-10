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
wget http://manage.bvm.mx/scripts/uptime.txt
mv uptime.txt uptime.php
cd /
mkdir scripts
cd /scripts/
wget http://manage.bvm.mx/scripts/list-ips.txt
wget http://manage.bvm.mx/scripts/count-traffic.txt
mv list-ips.txt list-ips.sh
mv count-traffic.txt count-traffic.sh
mkdir traffic
cd ~
mkdir ~/.ssh/
ssh-keygen -t rsa -N "" -f ~/.ssh/id_rsa
cd ~/.ssh/
cat id_rsa.pub >> ~/.ssh/authorized_keys
cat id_rsa
iptables -F && service iptables save
shutdown -r now