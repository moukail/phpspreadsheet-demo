Vagrant.configure("2") do |config|
  config.vm.box = "generic/centos9s"

  config.vm.provision :shell, path: ".vagrant/bootstrap.sh"
  config.vm.synced_folder '.', '/var/www/'

  #config.vm.network "public_network", ip: "192.168.1.200"
  #config.vm.network "forwarded_port", guest: 80, host: 80
  #config.vm.hostname = "moukafih.nl"

  config.ssh.insert_key = false

  config.vm.provider "virtualbox" do |vb|
    vb.customize ["modifyvm", :id, "--memory", "1024"]
    vb.customize ["modifyvm", :id, "--name", "centos8"]
  end
end
