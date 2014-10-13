# # スワップ領域作成
# # 参考:http://qiita.com/naoya@github/items/2059e3755962e907315e

# iptables無効
service "iptables" do
  action [:stop, :disable]
end

# # yum.repos
# bash 'install remi repository' do
#   code 'wget -O /tmp/remi-release-6.rpm http://rpms.famillecollet.com/enterprise/remi-release-6.rpm ;sudo rpm -Uvh /tmp/remi-release-6.rpm'
#   only_if "test `rpm -ql |grep remi | wc -l` -eq 1"
# end

# # phpインストール
# #%w{php php55-php-devel php55-php-mbstring php55-php-mcrypt php55-php-mysql php55-php-phpunit-PHPUnit php55-php-pecl-xdebug php55}.each do |p|
#
# %w{php php55-php-devel php55-php-mbstring php55-php-mcrypt php55-php-mysql php-phpunit-PHPUnit php55-php-pecl-xdebug php55}.each do |p|
#   package p do
#     action :install
#     options "--enablerepo=remi"
#   end
# end
