#
# Cookbook Name:: bms
# Recipe:: default
#
# Copyright 2014, YOUR_COMPANY_NAME
#
# All rights reserved - Do Not Redistribute
#
log "Hello World"

directory '/var/www' do
  owner 'root'
  group 'root'
  mode  '0755'
  action :create
end
%w{bms_staging bms }.each do |dir|
  directory "/var/www/#{dir}" do
    owner 'root'
    group 'root'
    mode  '0755'
    action :create
  end
  directory "/var/www/#{dir}/public" do
    owner 'root'
    group 'root'
    mode  '0755'
    action :create
  end
end

# yum関係
%w{gcc git sendmail make wget telnet readline-devel ncurses-devel gdbm-devel openssl-devel zlib-devel libyaml-devel httpd bash}.each do |p|
  package p do
    action :install
  end
end
# optional package
%w{dstat vim tree}.each do |p|
  package p do
    action :install
  end
end

template "httpd.conf" do
  path "/etc/httpd/conf/httpd.conf"
  source "httpd.conf.erb"
  owner "root"
  group "root"
  mode 0644
  notifies :start, 'service[httpd]'
end

#%w{ php php-devel php-mbstring php-mcrypt php-mysql php-xml}.each do |pkg|
%w{ php php-pdo php-mysql php-xml}.each do |pkg|
   package pkg do
      action :install
   end
end

# too havy
#bash 'setup composet' do
#    user 'vagrant'
#    cwd '/vagrant/'
#    code <<-EOH
#        php composer.phar update
#    EOH
#end

# php設定
template "bms.ini" do
  path "/etc/bms.ini"
  source "bms.ini.erb"
  mode 0644
  notifies :start, 'service[httpd]'
end
service "network" do
  action [:restart]
end
service "httpd" do
  action [:restart]
end
