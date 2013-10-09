yum install kvm kmod-kvm qemu qemu-kvm libvirtd
modprobe kvm-intel
sed -i 's/SELINUX=.*/SELINUX=disabled/' /etc/sysconfig/selinux
