case node['platform_family']
when 'rhel'

  # Probably driven from wrapper cookbooks, environments, or roles.
  # Keep in this namespace for backwards compat
  default['mysql']['data_dir'] = '/var/lib/mysql'

  # switching logic to account for differences in platform native
  # package versions
  case node['platform_version'].to_i
  when 5
    default['mysql']['server']['packages'] = ['mysql-server']
    default['mysql']['server']['log_slow_queries']     = '/var/log/mysql/slow.log'
  when 6
    default['mysql']['server']['packages'] = ['mysql-server']
    default['mysql']['server']['slow_query_log']       = 1
    default['mysql']['server']['slow_query_log_file']  = '/var/log/mysql/slow.log'
  when 2013 # amazon linux
    default['mysql']['server']['packages'] = ['mysql-server']
    default['mysql']['server']['slow_query_log']       = 1
    default['mysql']['server']['slow_query_log_file']  = '/var/log/mysql/slow.log'
  end  

  # Platformisms.. filesystem locations and such.
  default['mysql']['server']['basedir'] = '/usr'
  default['mysql']['server']['tmpdir'] = ['/tmp']

  default['mysql']['server']['directories']['run_dir']              = '/var/run/mysqld'
  default['mysql']['server']['directories']['log_dir']              = '/var/lib/mysql'
  default['mysql']['server']['directories']['slow_log_dir']         = '/var/log/mysql'
  default['mysql']['server']['directories']['confd_dir']            = '/etc/mysql/conf.d'

  default['mysql']['server']['mysqladmin_bin']       = '/usr/bin/mysqladmin'
  default['mysql']['server']['mysql_bin']            = '/usr/bin/mysql'

  default['mysql']['server']['pid_file']             = '/var/run/mysqld/mysqld.pid'
  default['mysql']['server']['socket']               = '/var/lib/mysql/mysql.sock'
  default['mysql']['server']['grants_path']          = '/etc/mysql_grants.sql'
  default['mysql']['server']['old_passwords']        = 1

  # RHEL/CentOS mysql package does not support this option.
  default['mysql']['tunable']['innodb_adaptive_flushing'] = false
  default['mysql']['server']['skip_federated'] = false
end
