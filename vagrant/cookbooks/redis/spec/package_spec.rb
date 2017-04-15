require File.join(File.dirname(__FILE__), %w{support spec_helper})

describe 'redis::package' do
  let(:chef_run){ ChefSpec::ChefRunner.new.converge("redis::package") }

  before{ Fauxhai.mock(platform:'ubuntu') }

  it 'should install the correct packages' do
    chef_run.should install_package 'redis-server'
  end

 	it 'should start the service' do
    chef_run.should start_service 'redis-server'
    chef_run.should enable_service 'redis-server'
  end

  it 'should create a configuration file' do
    chef_run.should create_file '/etc/redis/redis.conf'
  end
end
