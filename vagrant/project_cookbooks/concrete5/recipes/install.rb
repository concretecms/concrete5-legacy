#####################################################################################
# Currently not used b/c chef won't show the output from PHP, but this works. Its
# just silent and tough to debug. If future Chef version allow display output during
# provisioning, we can use this.
#####################################################################################

# concrete5/libraries/helpers.rb
::Chef::Recipe.send(:include, Concrete5::Helpers)

# Aaaaaand... go
execute "Installing Concrete5 via CLI tools..." do
	cwd "/home/vagrant/app/cli/"
	user "vagrant"
	command "php install-concrete5.php " << Concrete5::Helpers::cli_args(node)
	action :run
end