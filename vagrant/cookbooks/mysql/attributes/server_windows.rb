case node['platform_family']
when 'windows'
  default['mysql']['windows']['version']            = '5.5.34'
  default['mysql']['windows']['arch']               = node['kernel']['machine'] == 'x86_64' ? 'winx64' : 'win32'
  default['mysql']['windows']['package_file']       = "mysql-#{node['mysql']['windows']['version']}-#{node['mysql']['windows']['arch']}.msi"
  default['mysql']['windows']['packages']           = ['MySQL Server 5.5']
  default['mysql']['windows']['url']                = "http://dev.mysql.com/get/Downloads/MySQL-5.5/#{node['mysql']['windows']['package_file']}"

  default['mysql']['windows']['programdir']         = node['kernel']['machine'] == 'x86_64' ? 'Program Files' : 'Program Files (x86)'
  default['mysql']['windows']['basedir']            = "#{ENV['SYSTEMDRIVE']}\\#{node['mysql']['windows']['programdir']}\\MySQL\\#{node['mysql']['windows']['packages'].first}"
  default['mysql']['windows']['data_dir']           = "#{ENV['ProgramData']}\\MySQL\\#{node['mysql']['windows']['packages'].first}\\Data"
  default['mysql']['windows']['bin_dir']            = "#{node['mysql']['windows']['basedir']}\\bin"
  default['mysql']['windows']['mysqladmin_bin']     = "#{node['mysql']['windows']['bin_dir']}\\mysqladmin"
  default['mysql']['windows']['mysql_bin']          = "#{node['mysql']['windows']['bin_dir']}\\mysql"

  default['mysql']['windows']['conf_dir']           = node['mysql']['windows']['basedir']
  default['mysql']['windows']['old_passwords']      = 0
  default['mysql']['windows']['grants_path']        = "#{node['mysql']['windows']['conf_dir']}\\grants.sql"

  default['mysql']['server']['service_name']        = 'mysql'
  default['mysql']['server']['slow_query_log']      = 1
end
