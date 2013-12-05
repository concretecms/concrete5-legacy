# Create database before running CLI installation. If already created,
# will fail silently (actually, just moves)
mysql_database node[:concrete5][:database][:name] do
  connection(
    :host     => node[:concrete5][:database][:server],
    :username => node[:concrete5][:database][:username],
    :password => node[:mysql][:server_root_password]
  )
  action :create
end