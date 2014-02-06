#
# Cookbook Name:: rbenv
# Recipe:: user
#
# Copyright 2010, 2011 Fletcher Nichol
#
# Licensed under the Apache License, Version 2.0 (the "License");
# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
#

include_recipe "rbenv::user_install"

Array(node['rbenv']['user_installs']).each do |rbenv_user|
  rubies    = rbenv_user['rubies'] || node['rbenv']['user_rubies']
  gem_hash  = rbenv_user['gems'] || node['rbenv']['user_gems']

  rubies.each do |rubie|
    rbenv_ruby "#{rubie} (#{rbenv_user['user']})" do
      definition  rubie
      user        rbenv_user['user']
      root_path   rbenv_user['root_path'] if rbenv_user['root_path']
    end
  end

  rbenv_global "#{rbenv_user['global']} (#{rbenv_user['user']})" do
    rbenv_version rbenv_user['global']
    user          rbenv_user['user']
    root_path     rbenv_user['root_path'] if rbenv_user['root_path']

    only_if     { rbenv_user['global'] }
  end

  gem_hash.each_pair do |rubie, gems|
    Array(gems).each do |gem|
      rbenv_gem "#{gem['name']} (#{rbenv_user['user']})" do
        package_name    gem['name']
        user            rbenv_user['user']
        root_path       rbenv_user['root_path'] if rbenv_user['root_path']
        rbenv_version   rubie

        %w{version action options source}.each do |attr|
          send(attr, gem[attr]) if gem[attr]
        end
      end
    end
  end
end
