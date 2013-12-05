require File.join(File.dirname(__FILE__), %w{support spec_helper})

describe 'redis::gem' do
  let(:chef_run){ ChefSpec::ChefRunner.new.converge("redis::gem") }

  it 'should install required gems' do
    chef_run.should install_gem_package 'redis'
  end
end