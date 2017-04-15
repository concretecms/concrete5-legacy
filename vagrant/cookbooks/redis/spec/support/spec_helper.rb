require 'chefspec'
require 'fauxhai'

# Workaround to set valid cookbook name for specs
# @see https://github.com/acrmp/chefspec/issues/24
COOKBOOK_NAME = File.basename(Dir.getwd)