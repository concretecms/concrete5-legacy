# cURL
package "php5-curl" do
    action :install
end

# MySQL extensions
package "php5-mysql" do
    action :install
end

# APC cache
package "php-apc" do
    action :install
end

# GD image library
package "php5-gd" do
    action :install
end

# install php xdebug from pecl
php_pear "xdebug" do
    # Specify that xdebug.so must be loaded as zend extension
    zend_extensions ['xdebug.so']
    action :install
end

# xdebug configuration template
template "/etc/php5/conf.d/xdebug.ini" do
    source "xdebug.ini.erb"
    owner "root"
    group "root"
    mode 0644
end

# set pear channel auto-discover and install PHPunit
execute "Pear channel auto-discover and install PHPUnit" do
    user "root"
    command "pear config-set auto_discover 1 && pear install pear.phpunit.de/PHPUnit"
    action :run
    not_if "which phpunit"
end