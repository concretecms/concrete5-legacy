<?php
/**
 * @author jshannon
 */

// TODO: check include path
//ini_set('include_path', ini_get('include_path'));

error_reporting(E_ERROR | E_WARNING | E_USER_ERROR);

define('DIR_BASE', dirname(__FILE__) . '/../web');
define('BS_C5_CONFIG', DIR_BASE . '/concrete/config/');
define('BS_C5_STARTUP', DIR_BASE . '/concrete/startup/');

//dispatcher
define('C5_EXECUTE', true);

## Startup check ##
require(BS_C5_CONFIG . 'base_pre.php');

## Startup check ##
require(BS_C5_STARTUP . 'config_check.php');

## Check to see if, based on a config variable, we need to point to an alternate core ##
//require(BS_C5_STARTUP . 'updated_core_check.php');

## Load the base config file ##
require(BS_C5_CONFIG . 'base.php');

## First we ensure that dispatcher is not being called directly
require(BS_C5_STARTUP . 'file_access_check.php');

## Load the database ##
	Loader::database();

## Startup cache ##
Loader::library('cache');
Cache::startup();

## Load required libraries ##
Loader::library('object');
Loader::library('log');
Loader::library('localization');
Loader::library('request');
Loader::library('events');
Loader::library('model');
Loader::library('item_list');
Loader::library('view');
Loader::library('controller');
Loader::library('file/types');
Loader::library('block_view');
Loader::library('block_view_template');
Loader::library('block_controller');
Loader::library('attribute/view');
Loader::library('attribute/controller');

## Autoload settings
//if (C5_ENVIRONMENT_ONLY == false) {
//	require(dirname(__FILE__) . '/startup/autoload.php');
//}

## Load required models ##
Loader::model('area');
Loader::model('attribute/key');
Loader::model('attribute/value');
Loader::model('attribute/category');
Loader::model('attribute/set');
Loader::model('attribute/type');
Loader::model('block');
Loader::model('custom_style');
Loader::model('file');
Loader::model('file_version');
Loader::model('block_types');
Loader::model('collection');
Loader::model('collection_version');
Loader::model('collection_types');
Loader::model('config');
Loader::model('groups');
Loader::model('layout');
Loader::model('package');
Loader::model('page');
Loader::model('page_theme');
Loader::model('composer_page');
Loader::model('language_section_page');
Loader::model('permissions');
Loader::model('user');
Loader::model('userinfo');
Loader::model('task_permission');

// login the admin
User::getByUserID(1, true);

?>

