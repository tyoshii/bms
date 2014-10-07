# スワップ領域作成
# 参考:http://qiita.com/naoya@github/items/2059e3755962e907315e
bash 'create swapfile' do
  user 'root'
  code <<-EOC
    dd if=/dev/zero of=/swap.img bs=1M count=1024 &&
    chmod 600 /swap.img
    mkswap /swap.img
  EOC
  only_if "test ! -f /swap.img -a `cat /proc/swaps | wc -l` -eq 1"
end

mount '/dev/null' do # swap file entry for fstab
  action :enable # cannot mount; only add to fstab
  device '/swap.img'
  fstype 'swap'
end

bash 'activate swap' do
  code 'swapon -ae'
  only_if "test `cat /proc/swaps | wc -l` -eq 1"
end


# iptables無効
service "iptables" do
  action [:stop, :disable]
end

# # yum.repos
# bash 'install remi repository' do
#   code 'wget -O /tmp/remi-release-6.rpm http://rpms.famillecollet.com/enterprise/remi-release-6.rpm ;sudo rpm -Uvh /tmp/remi-release-6.rpm'
#   only_if "test `rpm -ql |grep remi | wc -l` -eq 1"
# end

#template "httpd.conf" do
#  path "/etc/httpd/conf/httpd.conf"
#  source "httpd.conf.erb"
#  owner "root"
#  group "root"
#  mode 0644
#  #notifies :restart, 'service[httpd]'
#end

# yum関係
%w{gcc vim tree make wget telnet readline-devel ncurses-devel gdbm-devel openssl-devel zlib-devel libyaml-devel httpd}.each do |p|
  package p do
    action :install
  end
end

# phpインストール
#%w{php php55-php-devel php55-php-mbstring php55-php-mcrypt php55-php-mysql php55-php-phpunit-PHPUnit php55-php-pecl-xdebug php55}.each do |p|

%w{php php55-php-devel php55-php-mbstring php55-php-mcrypt php55-php-mysql php-phpunit-PHPUnit php55-php-pecl-xdebug php55}.each do |p|
  package p do
    action :install
    options "--enablerepo=remi"
  end
end
# php設定
template "php.ini" do
  path "/etc/php.ini"
  source "php.ini.erb"
  mode 0644
  #notifies :restart, 'service[httpd]'
end

# mysql関係
%w{mysql-server}.each do |p|
  package p do
    action :install
  end
end
service "mysqld" do
  action [:start, :enable]
end


## httpd設定
##service "httpd" do
##  action [:start, :enable]
##end
#
#template "httpd.conf" do
#  path "/etc/httpd/conf/httpd.conf"
#  source "httpd.conf.erb"
#  owner "root"
#  group "root"
#  mode 0644
#  #notifies :restart, 'service[httpd]'
#end
