# ditch "could not reliably determine hostname" warning
cookbook_file '/etc/apache2/httpd.conf' do
    owner 'root'
    group 'root'
    mode '0644'
    source 'httpd.conf'
end

# copy self-signed SSL certificate
cookbook_file '/etc/ssl/certs/vagrant_apache_ssl.pem' do
    owner 'root'
    group 'root'
    mode '0644'
    source 'vagrant_apache_ssl.pem'
end

# disable default apache vhost
apache_site "000-default" do
  enable false
end

# setup vhost to link to /home/vagrant/app/web
web_app "default" do
    server_name node[:app][:server_name]
    docroot node[:app][:docroot]
    php_timezone node[:app][:php_timezone]
end