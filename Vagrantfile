# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "debsqueeze64"
  config.vm.box_url = "http://www.emken.biz/vagrant-boxes/debsqueeze64.box"
  config.vm.network :forwarded_port, guest: 2026, host: 2026
  config.vm.synced_folder ".", "/vagrant",
    owner: "www-data", group: "www-data"

  # Magic provisioning script!
  config.vm.provision :shell, :path => "vagrant-bootstrap/bootstrap.sh"
end
