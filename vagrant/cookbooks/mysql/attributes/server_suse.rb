case node['platform_family']
when 'suse'
  default['mysql']['data_dir']                = '/var/lib/mysql'
  default['mysql']['server']['service_name']            = 'mysql'
  default['mysql']['server']['server']['packages']      = %w[mysql-community-server]
  default['mysql']['server']['basedir']                 = '/usr'
  default['mysql']['server']['root_group']              = 'root'
  default['mysql']['server']['mysqladmin_bin']          = '/usr/bin/mysqladmin'
  default['mysql']['server']['mysql_bin']               = '/usr/bin/mysql'
  default['mysql']['server']['conf_dir']                = '/etc'
  default['mysql']['server']['confd_dir']               = '/etc/mysql/conf.d'
  default['mysql']['server']['socket']                  = '/var/run/mysql/mysql.sock'
  default['mysql']['server']['pid_file']                = '/var/run/mysql/mysqld.pid'
  default['mysql']['server']['old_passwords']           = 1
  default['mysql']['server']['grants_path']             = '/etc/mysql_grants.sql'
end
