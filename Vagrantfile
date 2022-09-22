Vagrant.configure("2") do |config|
  #config.vm.box = "centos/stream8"
  #config.vm.box_version = "20210210.0"

  config.vm.box = "generic/centos9s"
  #config.vm.box = "generic/ubuntu2204"

  config.vm.provision :shell, path: ".vagrant/bootstrap.sh"
  config.vm.synced_folder '.', '/var/www/'

  #config.vm.network "public_network", ip: "192.168.1.200"
  #config.vm.network "forwarded_port", guest: 80, host: 80
  #config.vm.hostname = "moukafih.nl"

  #config.ssh.username = "ismail" # "ubuntu" # default is vagrant
  #config.ssh.password = "meknes" # "8241fe86a2b663288e274711" # default is vagrant
  #config.ssh.port = 22
  config.ssh.insert_key = false

  #
  # Run Ansible from the Vagrant Host
  #
  #config.vm.provision "ansible" do |ansible|
  #  ansible.playbook = "ansible/centos8.yml"
  #end

  config.vm.provider "virtualbox" do |vb|
    vb.customize ["modifyvm", :id, "--memory", "1024"]
    vb.customize ["modifyvm", :id, "--name", "centos8"]
  end

  #config.vm.provider "docker" do |d|
  #  d.image = "ubuntu"
  #end
end
