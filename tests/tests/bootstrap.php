<?php
/**
 * @author jshannon
 */

// TODO: check include path
//ini_set('include_path', ini_get('include_path'));

error_reporting(E_ERROR | E_WARNING | E_USER_ERROR);

define('C5_EXECUTE', true);
$DIR_BASE = realpath(dirname(__FILE__) . '/../../web');
define('DIR_BASE', $DIR_BASE);

if(!is_dir(DIR_BASE . '/languages/it_IT/LC_MESSAGES')) {
	mkdir(DIR_BASE . '/languages/it_IT/LC_MESSAGES', 0777, true);
}
copy(dirname(__FILE__) . '/../assets/it_IT.mo', DIR_BASE . '/languages/it_IT/LC_MESSAGES/messages.mo');

define('DIR_BUILDTOOLS', dirname(dirname(__FILE__)) . '/build-tools');
if(!is_dir(DIR_BUILDTOOLS)) {
	exec('git clone --depth 1 --single-branch --branch master https://github.com/mlocati/concrete5-build ' . escapeshellarg(DIR_BUILDTOOLS));
}

//causes dispatcher to skip the page rendering
define('C5_ENVIRONMENT_ONLY', true);

//prevents dispatcher from causing redirection to the base_url
define('REDIRECT_TO_BASE_URL', false);

//let's enable timezones
define('ENABLE_USER_TIMEZONES', true);

//since we can't define/redefine this for individual tests, we set to a value that's most likely to cause errors (vs '')
define('DIR_REL', '/blog');

// Force tests to start in en_US
define('SITE_LOCALE', 'en_US');
define('ACTIVE_LOCALE', 'en_US');

//this is where the magic happens
require(DIR_BASE . '/concrete/dispatcher.php');

//add a user with Europe/Rome timezone
$uTest = UserInfo::getByUserName('testuser_it');
if(!is_object($uTest)) {
	$uTest = UserInfo::add(array(
		'uName' => 'testuser_it',
		'uEmail' => 'testuser_it@concrete5.org',
		'uPassword' => 'testpassword'
	));
}
$uTest->update(array('uTimezone' => 'Europe/Rome'));
define('TESTUSER_IT_ID', $uTest->getUserID());

$uTest = UserInfo::getByUserName('testuser_jp');
if(!is_object($uTest)) {
	$uTest = UserInfo::add(array(
		'uName' => 'testuser_jp',
		'uEmail' => 'testuser_jp@concrete5.org',
		'uPassword' => 'testpassword'
	));
}
$uTest->update(array('uTimezone' => 'Asia/Tokyo'));
define('TESTUSER_JP_ID', $uTest->getUserID());

// login the admin
User::getByUserID(USER_SUPER_ID, true);
Log::addEntry('bootsrapped','unit tests');

// include adodb-lib to avoid a PHPUnit problem with globals
include(ADODB_DIR.'/adodb-lib.inc.php');
