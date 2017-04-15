require File.join(File.dirname(__FILE__), %w{support spec_helper})

describe "redis::source" do
  let(:chef_run){ ChefSpec::ChefRunner.new }

  context "on unknown platform" do
    before{ Fauxhai.mock(platform:'chefspec') }

    it "no additional packages are set" do
      chef_run.converge 'redis::source'
      chef_run.node['redis']['source']['pkgs'].should eq []
    end
  end

  context "on ubuntu" do
    before{ Fauxhai.mock(platform:'ubuntu') }

    it "provides additional packages" do
      chef_run.converge 'redis::source'
      chef_run.node['redis']['source']['pkgs'].should eq %w{ build-essential }
    end
  end

  describe "compiling" do
    let(:chef_run) {
      runner = ChefSpec::ChefRunner.new({
        :file_cache_path => './',
        :redis => {
          :source => {
            :version => "1.0.0"
          }
        }
      })
      runner.converge 'redis::source'
      runner
    }

    it "creates user" do
      chef_run.should create_user 'redis'
    end

    it "fetches the correct tar file" do
      pending "Make code testable"
      chef_run.should create_remote_file './redis-1.0.0.tar.gz'
    end

    context "enable service" do
      it "creates init file" do
        chef_run.should create_file '/etc/init.d/redis'
      end

      it "sets correct configuration file" do
        chef_run.should create_file "/etc/redis/6379.conf"
      end
    end

    context "no service" do
      chef_run = ChefSpec::ChefRunner.new do |node|
        node.set['redis']['source']['create_service'] = false
      end

      before{ chef_run.converge 'redis::source' }

      it "does not create init file" do
        chef_run.should_not create_file '/etc/init.d/redis'
      end

      it "does not set configuration file" do
        chef_run.should_not create_file "/etc/redis/6379.conf"
      end
    end
  end
end
