# Library usage...
# - http://stackoverflow.com/questions/15595144/dry-within-a-chef-recipe
# - http://stackoverflow.com/questions/19134728/how-can-i-access-the-current-node-from-a-library-in-a-chef-cookbook
# - http://docs.opscode.com/essentials_cookbook_libraries.html

module Concrete5
	module Helpers
		def self.cli_args( _node )
			return {
				'--admin-password' 	=> _node[:concrete5][:admin_pass],
				'--db-server'		=> _node[:concrete5][:database][:server],
				'--db-username'		=> _node[:concrete5][:database][:username],
				'--db-password'		=> _node[:concrete5][:database][:password],
				'--db-database'		=> _node[:concrete5][:database][:name],
				'--core'			=> _node[:concrete5][:core],
				'--target'			=> _node[:concrete5][:target]
			}.map{|k,v| "#{k}=#{v} "}.join(' ')
		end
	end
end