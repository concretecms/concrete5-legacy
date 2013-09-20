<?php defined('C5_EXECUTE') or die('Access Denied.');

class Concrete5_Library_Localization {

	private static $loc = null;

	public static $untouchableDefines = null;

	public static function init() {
		if(!defined('DIRNAME_LANGUAGES_SITE_INTERFACE')) {
			define('DIRNAME_LANGUAGES_SITE_INTERFACE', 'site');
		}
		if (!defined('DIR_LANGUAGES_SITE_INTERFACE')) {
			define('DIR_LANGUAGES_SITE_INTERFACE', DIR_LANGUAGES . '/' . DIRNAME_LANGUAGES_SITE_INTERFACE);
		}
		if (!defined('REL_DIR_LANGUAGES_SITE_INTERFACE')) {
			define('REL_DIR_LANGUAGES_SITE_INTERFACE', DIR_REL . '/' . DIRNAME_LANGUAGES . '/' . DIRNAME_LANGUAGES_SITE_INTERFACE);
		}
		if (!defined('ENABLE_TRANSLATE_LOCALE_EN_US')) {
			define('ENABLE_TRANSLATE_LOCALE_EN_US', false);
		}
		if(!defined('SITE_LOCALE')) {
			Config::getAndDefine('SITE_LOCALE', 'en_US');
		}
		if(!defined('ACTIVE_LOCALE')) {
			$u = User::isLoggedIn() ? new User() : null;
			if ($u && $u->getUserDefaultLanguage() != '') {
				define('ACTIVE_LOCALE', $u->getUserDefaultLanguage());
			} else if (defined('LOCALE')) {
				define('ACTIVE_LOCALE', LOCALE);
			} else {
				define('ACTIVE_LOCALE', SITE_LOCALE);
			}
		}
		if(!defined('LANGUAGE')) {
			$language = ACTIVE_LOCALE;
			if (strpos($language, '_') > -1) {
				$loc = explode('_', $language);
				if (is_array($loc) && count($loc) == 2) {
					$language = $loc[0];
				}
			}
			define('LANGUAGE', $language);
		}
		Localization::getInstance()->updateDefines();
	}

	public static function getInstance() {
		if (null === self::$loc) {
			self::$loc = new self;
		}
		return self::$loc;
	}

	public static function changeLocale($locale) {
		$loc = Localization::getInstance();
		$loc->setLocale($locale);
		$loc->updateDefines();
	}

	public static function activeLocale() {
		$loc = Localization::getInstance();
		return $loc->getLocale();
	}

	protected $translate;

	public function __construct() {
		Loader::library('3rdparty/Zend/Date');
		Loader::library('3rdparty/Zend/Translate');
		$this->setLocale(ACTIVE_LOCALE);
		Zend_Date::setOptions(array('format_type' => 'php'));
		if (ENABLE_TRANSLATE_LOCALE_EN_US || ACTIVE_LOCALE != 'en_US') {
			$cache = Cache::getLibrary();
			if (is_object($cache)) {
				Zend_Translate::setCache($cache);
				Zend_Date::setOptions(array('cache'=>$cache));
			}
		}
	}

	public function setLocale($locale) {
		if($locale != ACTIVE_LOCALE) {
			self::forceDefine('ACTIVE_LOCALE', $locale);
		}
		$language = $locale;
		if (strpos($language, '_') > -1) {
			$loc = explode('_', $language);
			if (is_array($loc) && count($loc) == 2) {
				$language = $loc[0];
			}
		}
		if($language != LANGUAGE) {
			self::forceDefine('LANGUAGE', $language);
		}
		$localeNeededLoading = false;
		if (!ENABLE_TRANSLATE_LOCALE_EN_US && $locale == 'en_US' && isset($this->translate)) {
			unset($this->translate);
		}
		elseif (ENABLE_TRANSLATE_LOCALE_EN_US || $locale != 'en_US') {
			$languageDir = false;
			if (is_dir(DIR_LANGUAGES . '/' . $locale)) {
				$languageDir = DIR_LANGUAGES . '/' . $locale;
			} elseif (is_dir(DIR_LANGUAGES_CORE . '/' . $locale)) {
				$languageDir = DIR_LANGUAGES_CORE . '/' . $locale;
			}
			if ($languageDir !== false) {
				$options = array(
					'adapter' => 'gettext',
					'content' => $languageDir,
					'locale' => $locale
				);
				if (defined('TRANSLATE_OPTIONS')) {
					$_options = unserialize(TRANSLATE_OPTIONS);
					if (is_array($_options)) {
						$options = array_merge($options, $_options);
					}
				}
				if (!isset($this->translate)) {
					$this->translate = new Zend_Translate($options);
					$localeNeededLoading = true;
				} else {
					if (!in_array($locale, $this->translate->getList())) {
						$this->translate->addTranslation($options);
						$localeNeededLoading = true;
					}
					$this->translate->setLocale($locale);
				}
			}
		}
		if($localeNeededLoading) {
			Events::fire('on_locale_load', $locale);
		}
	}

	public function getLocale() {
		return isset($this->translate) ? $this->translate->getLocale() : 'en_US';
	}

	public function getActiveTranslateObject() {
		return $this->translate;
	}

	public function addSiteInterfaceLanguage($language) {
		if (is_object($this->translate)) {
			$this->translate->addTranslation(DIR_LANGUAGES_SITE_INTERFACE . '/' . $language . '.mo', $language);
		} else {
			Loader::library('3rdparty/Zend/Translate');
			$cache = Cache::getLibrary();
			if (is_object($cache)) {
				Zend_Translate::setCache($cache);
			}
			$this->translate = new Zend_Translate(array('adapter' => 'gettext', 'content' => DIR_LANGUAGES_SITE_INTERFACE . '/' . $language . '.mo', 'locale' => $language, 'disableNotices' => true));
		}
	}

	public static function getTranslate() {
		$loc = Localization::getInstance();
		return $loc->getActiveTranslateObject();
	}

	public static function getAvailableInterfaceLanguages() {
		$languages = array();
		$fh = Loader::helper('file');

		if (file_exists(DIR_LANGUAGES)) {
			$contents = $fh->getDirectoryContents(DIR_LANGUAGES);
			foreach($contents as $con) {
				if (is_dir(DIR_LANGUAGES . '/' . $con) && file_exists(DIR_LANGUAGES . '/' . $con . '/LC_MESSAGES/messages.mo')) {
					$languages[] = $con;
				}
			}
		}
		if (file_exists(DIR_LANGUAGES_CORE)) {
			$contents = $fh->getDirectoryContents(DIR_LANGUAGES_CORE);
			foreach($contents as $con) {
				if (is_dir(DIR_LANGUAGES_CORE . '/' . $con) && file_exists(DIR_LANGUAGES_CORE . '/' . $con . '/LC_MESSAGES/messages.mo') && (!in_array($con, $languages))) {
					$languages[] = $con;
				}
			}
		}

		return $languages;
	}

	protected static function updateDefines() {
		if(is_array(self::$untouchableDefines)) {
			$firstTime = false;
		}
		else {
			$firstTime = true;
			self::$untouchableDefines = array();
		}
		self::updateDefine('DATE_APP_GENERIC_MDYT_FULL', t('F d, Y \a\t g:i A'), $firstTime);
		self::updateDefine('DATE_APP_GENERIC_MDYT_FULL_SECONDS', t('F d, Y \a\t g:i:s A'), $firstTime);
		self::updateDefine('DATE_APP_GENERIC_MDYT', t('n/j/Y \a\t g:i A'), $firstTime);
		self::updateDefine('DATE_APP_GENERIC_MDY', t('n/j/Y'), $firstTime);
		self::updateDefine('DATE_APP_GENERIC_MDY_FULL', t('F j, Y'), $firstTime);
		self::updateDefine('DATE_APP_GENERIC_T', t('g:i A'), $firstTime);
		self::updateDefine('DATE_APP_GENERIC_TS', t('g:i:s A'), $firstTime);
		self::updateDefine('DATE_APP_FILENAME', t('d-m-Y_H:i_'), $firstTime); // used when dates are used to start filenames
		self::updateDefine('DATE_APP_FILE_PROPERTIES', DATE_APP_GENERIC_MDYT_FULL, $firstTime);
		self::updateDefine('DATE_APP_FILE_VERSIONS', DATE_APP_GENERIC_MDYT_FULL, $firstTime);
		self::updateDefine('DATE_APP_FILE_DOWNLOAD', DATE_APP_GENERIC_MDYT_FULL, $firstTime);
		self::updateDefine('DATE_APP_PAGE_VERSIONS', DATE_APP_GENERIC_MDYT, $firstTime);
		self::updateDefine('DATE_APP_DASHBOARD_SEARCH_RESULTS_USERS', DATE_APP_GENERIC_MDYT, $firstTime);
		self::updateDefine('DATE_APP_DASHBOARD_SEARCH_RESULTS_FILES', DATE_APP_GENERIC_MDYT, $firstTime);
		self::updateDefine('DATE_APP_DASHBOARD_SEARCH_RESULTS_PAGES', DATE_APP_GENERIC_MDYT, $firstTime);
		self::updateDefine('DATE_APP_DATE_ATTRIBUTE_TYPE_MDY', DATE_APP_GENERIC_MDY, $firstTime);
		self::updateDefine('DATE_APP_DATE_ATTRIBUTE_TYPE_T', DATE_APP_GENERIC_TS, $firstTime);
		self::updateDefine('DATE_APP_DATE_PICKER', 'm/d/yy', $firstTime);
		self::updateDefine('DATE_FORM_HELPER_FORMAT_HOUR', tc(/*i18n: can be 12 or 24 */'Time format', '12'), $firstTime);
		self::updateDefine('BLOCK_NOT_AVAILABLE_TEXT', t('This block is no longer available.'), $firstTime);
		self::updateDefine('GUEST_GROUP_NAME', t('Guest'), $firstTime);
		self::updateDefine('REGISTERED_GROUP_NAME', t('Registered Users'), $firstTime);
		self::updateDefine('ADMIN_GROUP_NAME', t('Admin'), $firstTime);
	}
	protected static function updateDefine($name, $value, $firstTime) {
		if(defined($name)) {
			if($firstTime) {
				self::$untouchableDefines[] = $name;
			}
			elseif(array_search($name, self::$untouchableDefines) === false) {
				self::forceDefine($name, $value);
			}
		}
		else {
			define($name, $value);
		}
	}
	protected static function forceDefine($name, $value) {
		if(defined($name)) {
			if(function_exists('runkit_constant_redefine')) {
				runkit_constant_redefine($name, $value);
			}
		}
		else {
			define($name, $value);
		}
	}

}
