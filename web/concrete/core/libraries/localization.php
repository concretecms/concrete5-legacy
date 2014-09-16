<?
	defined('C5_EXECUTE') or die("Access Denied.");
	class Concrete5_Library_Localization {

		private static $loc = null;

		public function init() {
			$loc = Localization::getInstance();
			$loc->getTranslate();
		}

		/**
		* @return Localization
		*/
		public static function getInstance() {
			if (null === self::$loc) {
				self::$loc = new self;
			}
			return self::$loc;
		}

		/** Changes the currently active locale
		* @param string $locale The locale to activate (for example: 'en_US')
		* @param bool $coreOnly = false Set to true to load only the core translation files, set to false (default) to load also packages and site locale translations
		*/
		public static function changeLocale($locale, $coreOnly = false) {
			$loc = Localization::getInstance();
			$loc->setLocale($locale, $coreOnly);
		}
		/** Returns the currently active locale
		* @return string
		* @example 'en_US'
		*/
		public static function activeLocale() {
			$loc = Localization::getInstance();
			return $loc->getLocale();
		}
		/** Returns the language for the currently active locale
		* @return string
		* @example 'en'
		*/
		public static function activeLanguage() {
			return current(explode('_', self::activeLocale()));
		}

		/** The current Zend_Translate instance (null if and only if locale is en_US)
		* @var Zend_Translate|null
		*/
		protected $translate;

		public function __construct() {
			Loader::library('3rdparty/Zend/Date');
			Loader::library('3rdparty/Zend/Translate');
			Loader::library('3rdparty/Zend/Locale');
			Loader::library('3rdparty/Zend/Locale/Data');
			$cache = Cache::getLibrary();
			if (is_object($cache)) {
				Zend_Translate::setCache($cache);
				Zend_Date::setOptions(array('cache'=>$cache));
			}
			$locale = defined('ACTIVE_LOCALE') ? ACTIVE_LOCALE : 'en_US';
			$this->setLocale($locale);
			Zend_Date::setOptions(array('format_type' => 'php'));
		}

		/** Changes the currently active locale
		* @param string $locale The locale to activate (for example: 'en_US')
		* @param bool $coreOnly = false Set to true to load only the core translation files, set to false (default) to load also packages and site locale translations
		*/
		public function setLocale($locale, $coreOnly = false) {
			$localeNeededLoading = false;
			if (($locale == 'en_US') && (!ENABLE_TRANSLATE_LOCALE_EN_US)) {
				if(isset($this->translate)) {
					unset($this->translate);
				}
				return;
			}
			if (is_dir(DIR_LANGUAGES . '/' . $locale)) {
				$languageDir = DIR_LANGUAGES . '/' . $locale;
			}
			elseif (is_dir(DIR_LANGUAGES_CORE . '/' . $locale)) {
				$languageDir = DIR_LANGUAGES_CORE . '/' . $locale;
			}
			else {
				return;
			}

			$options = array(
				'adapter' => 'gettext',
				'content' => $languageDir,
				'locale'  => $locale,
				'disableNotices'  => true,
				'ignore' => array('.', 'messages.po')
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
			if(!$coreOnly) {
				$this->addSiteInterfaceLanguage($locale);
				global $config_check_failed;
				if(!(isset($config_check_failed) && $config_check_failed)) {
					foreach(PackageList::get(1)->getPackages() as $p) {
						$pkg = Loader::package($p->getPackageHandle());
						if (is_object($pkg)) {
							$pkg->setupPackageLocalization($locale, null, $this->translate);
						}
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

		/** Returns the current Zend_Translate instance (null if and only if locale is en_US)
		* @var Zend_Translate|null
		*/
		public function getActiveTranslateObject() {
			return $this->translate;
		}

		/** Loads the site interface locale.
		* @param string $locale = null The locale to load (for instance: 'en_US'). If empty we'll use the currently active locale
		*/
		public function addSiteInterfaceLanguage($locale = null) {
			if (is_object($this->translate)) {
				if(!(is_string($locale) && strlen($locale))) {
					$locale = $this->translate->getLocale();
				}
				$path = DIR_LANGUAGES_SITE_INTERFACE . '/' . $locale . '.mo';
				if(is_file($path)) {
					$this->translate->addTranslation($path, $locale);
				}
			}
		}

		/** Returns the current Zend_Translate instance (null if and only if locale is en_US)
		* @var Zend_Translate|null
		*/
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
		
		/**
		 * Generates a list of all available languages and returns an array like
		 * [ "de_DE" => "Deutsch (Deutschland)",
		 *   "en_US" => "English (United States)",
		 *   "fr_FR" => "Français (France)"]
		 * The result will be sorted by the key.
		 * If the $displayLocale is set, the language- and region-names will be returned in that language 
		 * @param string $displayLocale Language of the description
		 * @return Array An associative Array with locale as the key and description as content
		 */
		public static function getAvailableInterfaceLanguageDescriptions($displayLocale = null) {
			$languages = self::getAvailableInterfaceLanguages();
			if (count($languages) > 0) {
				array_unshift($languages, 'en_US');
			}
			$locales = array();
			foreach($languages as $lang) {
				$locales[$lang] = self::getLanguageDescription($lang,$displayLocale);
			}
			natcasesort($locales);
			return $locales;
		}
		
		/**
		 * Get the description of a locale consisting of language and region description
		 * e.g. "French (France)"
		 * @param string $locale Locale that should be described
		 * @param string $displayLocale Language of the description
		 * @return string Description of a language
		 */
		public static function getLanguageDescription($locale, $displayLocale = null) {
			$localeList = Zend_Locale::getLocaleList();
			if (! isset($localeList[$locale])) {
				return $locale;
			} 
			
			if ($displayLocale !== NULL && (! isset($localeList[$displayLocale]))) {
				$displayLocale = NULL;
			} 
			
			$cacheLibrary = Cache::getLibrary();
			if (is_object($cacheLibrary)) {
				Zend_Locale_Data::setCache($cacheLibrary);
			}		
			
			$displayLocale = $displayLocale?$displayLocale:$locale;
			
			$zendLocale = new Zend_Locale($locale);
			$languageName = Zend_Locale::getTranslation($zendLocale->getLanguage(), 'language', $displayLocale);
			$description = $languageName;
			$region = $zendLocale->getRegion();
			if($region !== false) {
				$regionName = Zend_Locale::getTranslation($region, 'country', $displayLocale);
				if($regionName !== false) {
					$localeData = Zend_Locale_Data::getList($displayLocale, 'layout');
					if ( $localeData['characters'] == "right-to-left") {
						$description = '(' . $languageName . ' (' . $regionName ;
					} else {
						$description = $languageName . ' (' . $regionName . ")";
					}
					
				}
			}
			return $description;
		}

	}

