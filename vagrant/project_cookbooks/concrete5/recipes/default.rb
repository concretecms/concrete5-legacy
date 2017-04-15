# Install node dependencies (uses package.json file in /build)
execute "Install NPM and GruntJS dependencies..." do
    cwd "/home/vagrant/app/build/"
    user "root"
    command "/usr/local/bin/npm install; /usr/local/bin/npm install -g grunt-cli"
    action :run
end

# Auto-enable pretty urls?
 if node[:concrete5][:prettyurls]
    template "/home/vagrant/app/web/.htaccess" do
        source "htaccess.erb"
        owner "vagrant"
        group "vagrant"
        mode 0644
    end
 end