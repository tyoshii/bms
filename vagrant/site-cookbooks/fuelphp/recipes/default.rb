# old stable should keep us from installing a php too new for the apache cookbook
execute "add-apt-repository ppa:ondrej/php5" do
    command "add-apt-repository ppa:ondrej/php5-oldstable"
    user "root"
    not_if "which php"
end

bash 'deploy' do
    code <<-EOC
        cd /vagrant/deploy
        /usr/bin/env perl deploy.pl bms.list forcd
    EOC
end

#
# virtualhost-bms.conf
#template "virtualhost-bms.conf" do
#  path "/etc/httpd/conf.d/virtualhost-bms.conf"
#  source "virtualhost-bms.conf.erb"
#  mode 0664
  #notifies :restart, 'service[mysql]'
#end

# setup the vhost
#web_app "fuelphp" do
#  template "web_app.conf.erb"
#  docroot "/mnt/fuelphp"
#  server_name server_fqdn
#  server_aliases "fuelphp"
#end

# create the databases
#node[:db].each do |name|
#    execute "create database #{name}" do
#        command "mysql -uroot -p#{node[:mysql][:server_root_password]} -e 'create database if not exists #{name}'"
#        user "vagrant"
#    end
#end

# add a quick symlink
#link "/var/www/html/fuel" do
#    to "/vagrant/fuel"
#end
#link "/var/www/html/public" do
#    to "/vagrant/public"
#end

# add a quick symlink
#link "/vagrant/fuel/app/config/crypt.php" do
#    to "/vagrant/fuel/app/config/_crypt.php"
#end
#link "/vagrant/fuel/app/config/salt.php" do
#    to "/vagrant/fuel/app/config/_salt.php"
#end
#link "/vagrant/fuel/app/config/password.php" do
#    to "/vagrant/fuel/app/config/_password.php"
#end

# copy our fuellog convinience script for monitoring fuelphp logs
#cookbook_file '/usr/local/bin/fuellog' do
#    source "fuellog"
#    mode   "0755"
#    owner  "vagrant"
#    group  "root"
#end

# run composer
#execute "php composer.phar install" do
#    command "php composer.phar install"
#    cwd "/mnt/fuelphp"
#    creates "/mnt/fuelphp/composer.lock"
#end

# =================== OPTIONAL STUFF ===================== #

# include_recipe "php::module_curl"

# install additional libraries like curl
# package "curl" do
#     action :install
# end

# package "php5-curl" do
#     action :install
# end

# run your fuelphp install task
# execute "fuel php install" do
#     command "php oil r install"
#     cwd "/mnt/fuelphp"
#     creates "/mnt/fuelphp/fuel/app/config/development/migrations.php"
# end

# run your migrations
# execute "fuel php migrate" do
#     command "php oil r migrate"
#     cwd "/mnt/fuelphp"
#     creates "/mnt/fuelphp/fuel/app/config/development/migrations.php"
# end
