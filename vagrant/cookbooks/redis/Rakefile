#!/usr/bin/env rake

require 'rspec/core/rake_task'

task :default => 'foodcritic'

# Don't output shell commands for fileutils
Rake::FileUtilsExt.verbose(false)

desc 'Runs specs'
RSpec::Core::RakeTask.new do |t|
  t.pattern = 'spec/**/*_spec.rb'
  t.verbose = true
end

desc "Runs foodcritic linter"
task :foodcritic do
  if Gem::Version.new("1.9.2") <= Gem::Version.new(RUBY_VERSION.dup)
    sh "foodcritic --epic-fail any ./"
  else
    puts "WARN: foodcritic run is skipped as Ruby #{RUBY_VERSION} is < 1.9.2."
  end
end

desc "Runs knife cookbook test"
task :knife do
  sh "knife cookbook test cookbook --verbose --all --cookbook-path ./"
end
