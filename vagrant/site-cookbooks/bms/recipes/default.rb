#
# Cookbook Name:: bms
# Recipe:: default
#
# Copyright 2014, YOUR_COMPANY_NAME
#
# All rights reserved - Do Not Redistribute
#
log "Hello World"
#%w{ php php-devel php-mbstring php-mcrypt php-mysql php-xml}.each do |pkg|
%w{ php php-xml}.each do |pkg|
   package pkg do
      action :install
   end
end
