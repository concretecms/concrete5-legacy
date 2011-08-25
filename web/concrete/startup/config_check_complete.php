<?
defined('C5_EXECUTE') or die("Access Denied.");
if ($config_check_failed) {
	define('ENABLE_LEGACY_CONTROLLER_URLS', true);
    define("LOCALE","ja_JP.UTF8");
	## Localization ##	
	require(dirname(__FILE__) . '/../config/localization.php');

	
	// nothing is installed
	$v = View::getInstance();
	$v->render('/install/');
	exit;
}