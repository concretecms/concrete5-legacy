case node['platform_family']
when 'mac_os_x'
  default['mysql']['server']['packages']      = %w[mysql]
  default['mysql']['basedir']                 = '/usr/local/Cellar'
  default['mysql']['data_dir']                = '/usr/local/var/mysql'
  default['mysql']['root_group']              = 'admin'
  default['mysql']['mysqladmin_bin']          = '/usr/local/bin/mysqladmin'
  default['mysql']['mysql_bin']               = '/usr/local/bin/mysql'
end
