case node['platform_family']

when 'freebsd'
  default['mysql']['data_dir']                = '/var/db/mysql'
  default['mysql']['server']['packages']      = %w[mysql55-server]
  default['mysql']['server']['service_name']  = 'mysql-server'
  default['mysql']['server']['basedir']       = '/usr/local'
  default['mysql']['server']['root_group']              = 'wheel'
  default['mysql']['server']['mysqladmin_bin']          = '/usr/local/bin/mysqladmin'
  default['mysql']['server']['mysql_bin']               = '/usr/local/bin/mysql'
  default['mysql']['server']['conf_dir']                = '/usr/local/etc'
  default['mysql']['server']['confd_dir']               = '/usr/local/etc/mysql/conf.d'
  default['mysql']['server']['socket']                  = '/tmp/mysqld.sock'
  default['mysql']['server']['pid_file']                = '/var/run/mysqld/mysqld.pid'
  default['mysql']['server']['old_passwords']           = 0
  default['mysql']['server']['grants_path']             = '/var/db/mysql/grants.sql'
end
