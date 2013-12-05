case node['platform_family']
when 'windows'
  default['mysql']['windows']['package_file']       = "mysql-#{mysql['version']}-#{mysql['arch']}.msi"
  default['mysql']['windows']['packages']           = ['MySQL Server 5.5']
  default['mysql']['windows']['url']                = "http://www.mysql.com/get/Downloads/MySQL-5.5/#{mysql['package_file']}/from/http://mysql.mirrors.pair.com/"
  default['mysql']['windows']['version']            = '5.5.32'
#  default['mysql']['windows']['arch']              = 'win32'

  default['mysql']['windows']['basedir']            = "#{ENV['SYSTEMDRIVE']}\\Program Files (x86)\\MySQL\\#{mysql['server']['packages'].first}"
  default['mysql']['windows']['data_dir']           = "#{node['mysql']['windows']['basedir']}\\Data"
  default['mysql']['windows']['bin_dir']            = "#{node['mysql']['windows']['basedir']}\\bin"
  default['mysql']['windows']['mysqladmin_bin']     = "#{node['mysql']['windows']['bin_dir']}\\mysqladmin"
  default['mysql']['windows']['mysql_bin']          = "#{node['mysql']['windows']['bin_dir']}\\mysql"

  default['mysql']['windows']['conf_dir']           = node['mysql']['windows']['basedir']
  default['mysql']['windows']['old_passwords']      = 0
  default['mysql']['windows']['grants_path']        = "#{node['mysql']['conf_dir']}\\grants.sql"

  default['mysql']['server']['service_name']        = 'mysql'
end
