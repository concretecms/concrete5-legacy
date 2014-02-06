#!/usr/bin/env rake
require 'foodcritic'
require 'emeril/rake'

FoodCritic::Rake::LintTask.new do |t|
  t.options = { :fail_tags => ['any'] }
end

begin
  require 'kitchen/rake_tasks'
  Kitchen::RakeTasks.new
rescue LoadError
  puts ">>>>> Kitchen gem not loaded, omitting tasks" unless ENV['CI']
end

task :default => [:foodcritic]
