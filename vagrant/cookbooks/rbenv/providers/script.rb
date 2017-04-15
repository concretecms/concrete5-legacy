#
# Cookbook Name:: rbenv
# Provider:: script
#
# Author:: Fletcher Nichol <fnichol@nichol.ca>
#
# Copyright 2011, Fletcher Nichol
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

include Chef::Rbenv::ScriptHelpers

action :run do
  script_code         = build_script_code
  script_environment  = build_script_environment

  script new_resource.name do
    interpreter   "bash"
    code          script_code
    user          new_resource.user     if new_resource.user
    creates       new_resource.creates  if new_resource.creates
    cwd           new_resource.cwd      if new_resource.cwd
    group         new_resource.group    if new_resource.group
    path          new_resource.path     if new_resource.path
    returns       new_resource.returns  if new_resource.returns
    timeout       new_resource.timeout  if new_resource.timeout
    umask         new_resource.umask    if new_resource.umask
    environment(script_environment)
  end

  new_resource.updated_by_last_action(true)
end

private

def build_script_code
  <<-SYSTEM_WIDE.gsub(/^ +/, '')
    export RBENV_ROOT="#{rbenv_root}"
    export PATH="${RBENV_ROOT}/bin:$PATH"
    eval "$(rbenv init -)"

    rbenv shell #{new_resource.rbenv_version}

    #{new_resource.code}
  SYSTEM_WIDE
end

def build_script_environment
  script_env = { 'RBENV_ROOT' => rbenv_root }
  if new_resource.environment
    script_env.merge!(new_resource.environment)
  end
  if new_resource.user
    script_env.merge!({ 'USER' => new_resource.user,'HOME' => user_home })
  end

  script_env
end
