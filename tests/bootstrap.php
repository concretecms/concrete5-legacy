<?php
/**
 * @author jshannon
 */

// TODO: check include path
//ini_set('include_path', ini_get('include_path'));

error_reporting(E_ERROR | E_WARNING | E_USER_ERROR);

define('C5_EXECUTE', true);
define('DIR_BASE', dirname(__FILE__) . '/../web');

//causes dispatcher to skip the page rendering
define('C5_ENVIRONMENT_ONLY', true);

//prevents dispatcher from causing redirection to the base_url
define('REDIRECT_TO_BASE_URL', false);

require(DIR_BASE . '/concrete/dispatcher.php');

// login the admin
User::getByUserID(1, true);

?>

