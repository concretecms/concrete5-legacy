<?php
defined('C5_EXECUTE') or die("Access Denied.");

$u = new User();
Config::getOrDefine('SITE_LOCALE', 'en_US');

if ($u->getUserDefaultLanguage() != '') {
	define('ACTIVE_LOCALE', $u->getUserDefaultLanguage());
} else if (defined('LOCALE')) {
	define('ACTIVE_LOCALE', LOCALE);
} else {
	define('ACTIVE_LOCALE', SITE_LOCALE);
}

if (strpos(ACTIVE_LOCALE, '_') > -1) {
	$loc = explode('_', ACTIVE_LOCALE);
	if (is_array($loc) && count($loc) == 2) {
		define('LANGUAGE', $loc[0]);
	}
}

define('DIRNAME_LANGUAGES_SITE_INTERFACE', 'site');

if (!defined('DIR_LANGUAGES_SITE_INTERFACE')) {
	define('DIR_LANGUAGES_SITE_INTERFACE', DIR_LANGUAGES . '/' . DIRNAME_LANGUAGES_SITE_INTERFACE);
}

if (!defined('REL_DIR_LANGUAGES_SITE_INTERFACE')) {
	define('REL_DIR_LANGUAGES_SITE_INTERFACE', DIR_REL . '/' . DIRNAME_LANGUAGES . '/' . DIRNAME_LANGUAGES_SITE_INTERFACE);
}

if (!defined("LANGUAGE")) {
	define("LANGUAGE", ACTIVE_LOCALE);
}

if (!defined('ENABLE_TRANSLATE_LOCALE_EN_US')) {
	define('ENABLE_TRANSLATE_LOCALE_EN_US', false);
}

// initialize localization immediately following defining locale
Localization::init();

if (defined('DATE_APP_GENERIC_MDYT_FULL')) {
	define('CUSTOM_DATE_APP_GENERIC_MDYT_FULL', DATE_APP_GENERIC_MDYT_FULL);
} else {
	/** @deprecated Use Loader::helper('date')->formatDateTime($date, true, false) instead */
	define('DATE_APP_GENERIC_MDYT_FULL', t('F d, Y \a\t g:i A'));
}

if (defined('DATE_APP_GENERIC_MDYT_FULL_SECONDS')) {
	define('CUSTOM_DATE_APP_GENERIC_MDYT_FULL_SECONDS', DATE_APP_GENERIC_MDYT_FULL_SECONDS);
} else {
	/** @deprecated Use Loader::helper('date')->formatDateTime($date, true, true) instead */
	define('DATE_APP_GENERIC_MDYT_FULL_SECONDS', t('F d, Y \a\t g:i:s A'));
}

if (defined('DATE_APP_GENERIC_MDYT')) {
	define('CUSTOM_DATE_APP_GENERIC_MDYT', DATE_APP_GENERIC_MDYT);
} else {
	/** @deprecated Use Loader::helper('date')->formatDateTime($date, false, false) instead */
	define('DATE_APP_GENERIC_MDYT', t('n/j/Y \a\t g:i A'));
}

if (defined('DATE_APP_GENERIC_MDY')) {
	define('CUSTOM_DATE_APP_GENERIC_MDY', DATE_APP_GENERIC_MDY);
} else {
	/** @deprecated Use Loader::helper('date')->formatDate($date, false) instead */
	define('DATE_APP_GENERIC_MDY', t('n/j/Y'));
}

if (defined('DATE_APP_GENERIC_MDY_FULL')) {
	define('CUSTOM_DATE_APP_GENERIC_MDY_FULL', DATE_APP_GENERIC_MDY_FULL);
} else {
	/** @deprecated Use Loader::helper('date')->formatDate($date, true) instead */
	define('DATE_APP_GENERIC_MDY_FULL', t('F j, Y'));
}

if (defined('DATE_APP_GENERIC_T')) {
	define('CUSTOM_DATE_APP_GENERIC_T', DATE_APP_GENERIC_T);
} else {
	/** @deprecated Use Loader::helper('date')->formatTime($date, false) instead */
	define('DATE_APP_GENERIC_T', t('g:i A'));
}

if (defined('DATE_APP_GENERIC_TS')) {
	define('CUSTOM_DATE_APP_GENERIC_TS', DATE_APP_GENERIC_TS);
} else {
	/** @deprecated Use Loader::helper('date')->formatTime($date, true) instead */
	define('DATE_APP_GENERIC_TS', t('g:i:s A'));
}

if (defined('DATE_APP_FILENAME')) {
	define('CUSTOM_DATE_APP_FILENAME', DATE_APP_FILENAME);
} else {
	/** @deprecated Use Loader::helper('date')->formatSpecial('FILENAME', $date) instead */
	define('DATE_APP_FILENAME', t('d-m-Y_H:i_')); // used when dates are used to start filenames
}

if (defined('DATE_APP_FILE_PROPERTIES')) {
	define('CUSTOM_DATE_APP_FILE_PROPERTIES', DATE_APP_FILE_PROPERTIES);
} else {
	/** @deprecated Use Loader::helper('date')->formatSpecial('FILE_PROPERTIES', $date) instead */
	define('DATE_APP_FILE_PROPERTIES', DATE_APP_GENERIC_MDYT_FULL);
}
if (defined('DATE_APP_FILE_VERSIONS')) {
	define('CUSTOM_DATE_APP_FILE_VERSIONS', DATE_APP_FILE_VERSIONS);
} else {
	/** @deprecated Use Loader::helper('date')->formatSpecial('FILE_VERSIONS', $date) instead */
	define('DATE_APP_FILE_VERSIONS', DATE_APP_GENERIC_MDYT_FULL);
}
if (defined('DATE_APP_FILE_DOWNLOAD')) {
	define('CUSTOM_DATE_APP_FILE_DOWNLOAD', DATE_APP_FILE_DOWNLOAD);
} else {
	/** @deprecated Use Loader::helper('date')->formatSpecial('FILE_DOWNLOAD', $date) instead */
	define('DATE_APP_FILE_DOWNLOAD', DATE_APP_GENERIC_MDYT_FULL);
}

if (defined('DATE_APP_PAGE_VERSIONS')) {
	define('CUSTOM_DATE_APP_PAGE_VERSIONS', DATE_APP_PAGE_VERSIONS);
} else {
	/** @deprecated Use Loader::helper('date')->formatSpecial('PAGE_VERSIONS', $date) instead */
	define('DATE_APP_PAGE_VERSIONS', DATE_APP_GENERIC_MDYT);
}
if (defined('DATE_APP_DASHBOARD_SEARCH_RESULTS_USERS')) {
	define('CUSTOM_DATE_APP_DASHBOARD_SEARCH_RESULTS_USERS', DATE_APP_DASHBOARD_SEARCH_RESULTS_USERS);
} else {
	/** @deprecated Use Loader::helper('date')->formatSpecial('DASHBOARD_SEARCH_RESULTS_USERS', $date) instead */
	define('DATE_APP_DASHBOARD_SEARCH_RESULTS_USERS', DATE_APP_GENERIC_MDYT);
}

if (defined('DATE_APP_DASHBOARD_SEARCH_RESULTS_FILES')) {
	define('CUSTOM_DATE_APP_DASHBOARD_SEARCH_RESULTS_FILES', DATE_APP_DASHBOARD_SEARCH_RESULTS_FILES);
} else {
	/** @deprecated Use Loader::helper('date')->formatSpecial('DASHBOARD_SEARCH_RESULTS_FILES', $date) instead */
	define('DATE_APP_DASHBOARD_SEARCH_RESULTS_FILES', DATE_APP_GENERIC_MDYT);
}

if (defined('DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES')) {
	define('CUSTOM_DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES', DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES);
} else {
	/** @deprecated Use Loader::helper('date')->formatSpecial('DASHBOARD_SEARCH_RESULTS_PAGES', $date) instead */
	define('DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES', DATE_APP_GENERIC_MDYT);
}

if (defined('DATE_APP_DATE_ATTRIBUTE_TYPE_MDY')) {
	define('CUSTOM_DATE_APP_DATE_ATTRIBUTE_TYPE_MDY', DATE_APP_DATE_ATTRIBUTE_TYPE_MDY);
} else {
	/** @deprecated Use Loader::helper('date')->formatSpecial('DATE_ATTRIBUTE_TYPE_MDY', $date) instead */
	define('DATE_APP_DATE_ATTRIBUTE_TYPE_MDY', DATE_APP_GENERIC_MDY);
}
if (defined('DATE_APP_DATE_ATTRIBUTE_TYPE_T')) {
	define('CUSTOM_DATE_APP_DATE_ATTRIBUTE_TYPE_T', DATE_APP_DATE_ATTRIBUTE_TYPE_T);
} else {
	/** @deprecated Use Loader::helper('date')->formatSpecial('DATE_ATTRIBUTE_TYPE_T', $date) instead */
	define('DATE_APP_DATE_ATTRIBUTE_TYPE_T', DATE_APP_GENERIC_TS);
}
if (defined('DATE_APP_DATE_PICKER')) {
	define('CUSTOM_DATE_APP_DATE_PICKER', DATE_APP_DATE_PICKER);
} else {
	/** @deprecated Use Loader::helper('date')->getJQueryUIDatePickerFormat() instead */
	define('DATE_APP_DATE_PICKER', t(/*i18n http://api.jqueryui.com/datepicker/#utility-formatDate */'m/d/yy'));
}


if (!defined('DATE_APP_SURVEY_RESULTS')) {
	// NO DEFINE HERE, JUST PLACING HERE TO MAKE A NOTE OF IT
}

if (!defined('DATE_FORM_HELPER_FORMAT_HOUR')) {
	define('DATE_FORM_HELPER_FORMAT_HOUR', tc(/*i18n: can be 12 or 24 */'Time format', '12'));
}
/** @deprecated */
define('BLOCK_NOT_AVAILABLE_TEXT', t('This block is no longer available.'));
/** @deprecated */
define('GUEST_GROUP_NAME', tc('GroupName', 'Guest'));
/** @deprecated */
define('REGISTERED_GROUP_NAME', tc('GroupName', 'Registered Users'));
/** @deprecated */
define('ADMIN_GROUP_NAME', tc('GroupName', 'Administrators'));
