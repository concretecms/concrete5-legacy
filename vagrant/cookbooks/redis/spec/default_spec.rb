require File.join(File.dirname(__FILE__), %w{support spec_helper})

describe 'redis::default' do
  let(:chef_run){ ChefSpec::ChefRunner.new.converge("redis::default") }

  it 'should be a wrapper for redis::package' do
    chef_run.should include_recipe 'redis::package'
  end
end