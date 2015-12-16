# -*- mode: ruby -*-
# vi: set ft=ruby :

plugin_installed = false

required_plugins = %w( vagrant-hostmanager )
required_plugins.each do |plugin|
    unless Vagrant.has_plugin?(plugin)
        system "vagrant plugin install #{plugin}"
        plugin_installed = true
    end
end

if plugin_installed === true
  exec "vagrant #{ARGV.join' '}"
end

Vagrant.configure(2) do |config|
  config.vm.box = "ubuntu/trusty64"
  config.vm.box_check_update = false

  config.vm.network "private_network",ip: "192.168.31.10"
  config.vm.hostname = 'friendloc'

  config.hostmanager.enabled = true
  config.hostmanager.manage_host = true
  config.hostmanager.ignore_private_ip = false
  config.hostmanager.include_offline = true
  config.hostmanager.aliases = %w(friendloc.dev www.friendloc.dev)

  config.ssh.forward_agent = true

  config.vm.synced_folder "./project", "/home/vagrant/project"

  config.vm.provider :virtualbox do |vb, override|
    vb.customize ["modifyvm", :id, "--cpus",   1]
    vb.customize ["modifyvm", :id, "--memory", 1024]
    vb.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
  end

  config.vm.provision "file", source: "friendloc", destination: "/tmp/friendloc"
  config.vm.provision "shell", path: "./provisioner.sh"
end
