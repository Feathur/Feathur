yum -y update
curl http://download.openvz.org/openvz.repo > /etc/yum.repos.d/openvz.repo
yum -y update
yum -y install vzkernel vzctl php
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