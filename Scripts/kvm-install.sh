yum install kvm kmod-kvm qemu qemu-kvm libvirtd bridge-utils httpd php
modprobe kvm-intel
sed -i 's/SELINUX=.*/SELINUX=disabled/' /etc/sysconfig/selinux
mkdir /var/feathur/
mkdir /var/feathur/configs/
mkdir /var/feathur/data/
mkdir /var/feathur/data/templates
mkdir /var/feathur/data/templates/kvm
sudo chkconfig dhcpd on
echo "subnet 0.0.0.0 netmask 0.0.0.0 { authoritative; default-lease-time 21600000; max-lease-time 432000000; }" > /var/feathur/configs/dhcpd.head
cd ~
mkdir ~/.ssh/
ssh-keygen -t rsa -N "" -f ~/.ssh/id_rsa
cd ~/.ssh/
cat id_rsa.pub >> ~/.ssh/authorized_keys
cat id_rsa
iptables -F && service iptables save
