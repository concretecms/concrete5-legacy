<?php defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Dashboard_System_Basics_TranslateSpecialItems extends DashboardBaseController {

	protected static function getLocales() {
		$locales = array();
		$languages = Localization::getAvailableInterfaceLanguages();
		Zend_Locale_Data::setCache(Cache::getLibrary());
		foreach($languages as $language) {
			$locale = new Zend_Locale($language);
			$locales[$language] = Zend_Locale::getTranslation($locale->getLanguage(), 'language');
			$localeRegion = $locale->getRegion();
			if($localeRegion !== false) {
				$localeRegionName = $locale->getTranslation($locale->getRegion(), 'country');
				if($localeRegionName !== false) {
					$locales[$language] .= ' (' . $localeRegionName . ')';
				}
			}
		}
		asort($locales);
		return $locales;
	}
	public function view() {
		Loader::library('3rdparty/Zend/Locale');
		Loader::library('3rdparty/Zend/Locale/Data');
		$locales = self::getLocales();
		if(count($locales)) {
			$locale = $this->post('locale');
			if(!(is_string($locale) && array_key_exists($locale, $locales))) {
				$locale = $this->get('locale');
				if(!(is_string($locale) && array_key_exists($locale, $locales))) {
					$locale = Localization::activeLocale();
					if(!array_key_exists($locale, $locales)) {
						reset($locales);
						$locale = key($locales);
						reset($locales);
					}
				}
			}
			$attributeCategories = array();
			$attributeSetNames = array();
			$attributeKeyNames = array();
			foreach(AttributeKeyCategory::getList() as $akc) {
				$akcHandle = $akc->getAttributeKeyCategoryHandle();
				switch($akcHandle) {
					case 'collection':
						$akcName = t('Page attributes');
						break;
					case 'user':
						$akcName = t('User attributes');
						break;
					case 'file':
						$akcName = t('File attributes');
						break;
					default:
						$akcName = Object::uncamelcase($akcHandle);
						break;
				}
				$attributeCategories[$akcHandle] = $akcName;
				foreach($akc->getAttributeSets() as $as) {
					$attributeSetNames[$akcHandle][$as->getAttributeSetID()]['source'] = $as->getAttributeSetName();
				}
				if(isset($attributeSetNames[$akcHandle])) {
					uasort($attributeSetNames[$akcHandle], array(__CLASS__, 'sortBySource'));
				}
				foreach(AttributeKey::getList($akcHandle) as $ak) {
					$attributeKeyNames[$akcHandle][$ak->getAttributeKeyID()]['source'] = $ak->getAttributeKeyName();
				}
				if(isset($attributeKeyNames[$akcHandle])) {
					uasort($attributeKeyNames[$akcHandle], array(__CLASS__, 'sortBySource'));
				}
			}
			asort($attributeCategories);
			$attributeTypeNames = array();
			foreach(AttributeType::getList() as $at) {
				$attributeTypeNames[$at->getAttributeTypeID()]['source'] = $at->getAttributeTypeName();
			}
			uasort($attributeTypeNames, array(__CLASS__, 'sortBySource'));
			$permissionCategories = array();
			$permissionKeyNames = array();
			$permissionKeyDescriptions = array();
			foreach(PermissionKeyCategory::getList() as $pkc) {
				$pkcHandle = $pkc->getPermissionKeyCategoryHandle();
				switch($pkcHandle) {
					case 'page':
						$pkcName = t('Page');
						break;
					case 'single_page':
						$pkcName = t('Single page');
						break;
					case 'stack':
						$pkcName = t('Stack');
						break;
					case 'composer_page':
						$pkcName = t('Composer page');
						break;
					case 'user':
						$pkcName = t('User');
						break;
					case 'file_set':
						$pkcName = t('File set');
						break;
					case 'file':
						$pkcName = t('File');
						break;
					case 'area':
						$pkcName = t('Area');
						break;
					case 'block_type':
						$pkcName = t('Block type');
						break;
					case 'block':
						$pkcName = t('Block');
						break;
					case 'admin':
						$pkcName = t('Administration');
						break;
					case 'sitemap':
						$pkcName = t('Site map');
						break;
					case 'marketplace_newsflow':
						$pkcName = t('MarketPlace newsflow');
						break;
					case 'basic_workflow':
						$pkcName = t('Basic workflow');
						break;
					default:
						$pkcName = Object::uncamelcase($akcHandle);
						break;
				}
				$permissionCategories[$pkcHandle] = $pkcName;
				foreach(PermissionKey::getList($pkcHandle) as $pk) {
					$permissionKeyNames[$pkcHandle][$pk->getPermissionKeyID()]['source'] = $pk->getPermissionKeyName();
					$permissionKeyDescriptions[$pkcHandle][$pk->getPermissionKeyID()]['source'] = $pk->getPermissionKeyDescription();
				}
				if(isset($permissionKeyNames[$pkcHandle])) {
					uasort($permissionKeyNames[$pkcHandle], array(__CLASS__, 'sortBySource'));
				}
				if(isset($permissionKeyDescriptions[$pkcHandle])) {
					uasort($permissionKeyDescriptions[$pkcHandle], array(__CLASS__, 'sortBySource'));
				}
			}
			asort($permissionCategories);
			$permissionAccessEntityTypeNames = array();
			foreach(PermissionAccessEntityType::getList() as $accessEntityType) {
				$permissionAccessEntityTypeNames[$accessEntityType->getAccessEntityTypeID()]['source'] = $accessEntityType->getAccessEntityTypeName();
			}
			uasort($permissionAccessEntityTypeNames, array(__CLASS__, 'sortBySource'));
			$permissionAccessEntityTypes = array();
			$curLocale = Localization::activeLocale();
			if($curLocale != $locale) {
				Localization::changeLocale($locale);
			}
			foreach(array_keys($attributeSetNames) as $akcHandle) {
				foreach(array_keys($attributeSetNames[$akcHandle]) as $asID) {
					$localized = isset($_POST["AttributeSetName_$asID"]) ? $this->post("AttributeSetName_$asID") : tc('AttributeSetName', $attributeSetNames[$akcHandle][$asID]['source']);
					$attributeSetNames[$akcHandle][$asID]['translated'] = ($localized == $attributeSetNames[$akcHandle][$asID]['source']) ? '' : $localized;
				}
			}
			foreach(array_keys($attributeKeyNames) as $akcHandle) {
				foreach(array_keys($attributeKeyNames[$akcHandle]) as $akID) {
					$localized = isset($_POST["AttributeKeyName_$akID"]) ? $this->post("AttributeKeyName_$akID") : tc('AttributeKeyName', $attributeKeyNames[$akcHandle][$akID]['source']);
					$attributeKeyNames[$akcHandle][$akID]['translated'] = ($localized == $attributeKeyNames[$akcHandle][$akID]['source']) ? '' : $localized;
				}
			}
			foreach(array_keys($attributeTypeNames) as $atID) {
				$localized = isset($_POST["AttributeTypeName_$atID"]) ? $this->post("AttributeTypeName_$atID") : tc('AttributeTypeName', $attributeTypeNames[$atID]['source']);
				$attributeTypeNames[$atID]['translated'] = ($localized == $attributeTypeNames[$atID]['source']) ? '' : $localized;
			}
			foreach(array_keys($permissionKeyNames) as $pkcHandle) {
				foreach(array_keys($permissionKeyNames[$pkcHandle]) as $pkID) {
					$localized = isset($_POST["PermissionKeyName_$pkID"]) ? $this->post("PermissionKeyName_$pkID") : tc('PermissionKeyName', $permissionKeyNames[$pkcHandle][$pkID]['source']);
					$permissionKeyNames[$pkcHandle][$pkID]['translated'] = ($localized == $permissionKeyNames[$pkcHandle][$pkID]['source']) ? '' : $localized;
				}
			}
			foreach(array_keys($permissionKeyDescriptions) as $pkcHandle) {
				foreach(array_keys($permissionKeyDescriptions[$pkcHandle]) as $pkID) {
					$localized = isset($_POST["PermissionKeyDescription_$pkID"]) ? $this->post("PermissionKeyDescription_$pkID") : tc('PermissionKeyDescription', $permissionKeyDescriptions[$pkcHandle][$pkID]['source']);
					$permissionKeyDescriptions[$pkcHandle][$pkID]['translated'] = ($localized == $permissionKeyDescriptions[$pkcHandle][$pkID]['source']) ? '' : $localized;
				}
			}
			foreach(array_keys($permissionAccessEntityTypeNames) as $accessEntityTypeID) {
				$localized = isset($_POST["PermissionAccessEntityTypeName_$accessEntityTypeID"]) ? $this->post("PermissionAccessEntityTypeName_$accessEntityTypeID") : tc('PermissionAccessEntityTypeName', $permissionAccessEntityTypeNames[$accessEntityTypeID]['source']);
				$permissionAccessEntityTypeNames[$accessEntityTypeID]['translated'] = ($localized == $permissionAccessEntityTypeNames[$accessEntityTypeID]['source']) ? '' : $localized;
			}
			if($curLocale != $locale) {
				Localization::changeLocale($curLocale);
			}
			$this->set('locale', $locale);
			$translationTables = array();
			$translationTables['AttributeSetName'] = array('name' => t('Attribute sets names'), 'rows' => self::buildTranslationRows('AttributeSetName', $attributeSetNames, $attributeCategories));
			$translationTables['AttributeKeyName'] = array('name' => t('Attribute key names'), 'rows' => self::buildTranslationRows('AttributeKeyName', $attributeKeyNames, $attributeCategories));
			$translationTables['AttributeTypeName'] = array('name' => t('Attribute type names'), 'rows' => self::buildTranslationRows('AttributeTypeName', $attributeTypeNames));
			$translationTables['PermissionKeyName'] = array('name' => t('Permission key names'), 'rows' => self::buildTranslationRows('PermissionKeyName', $permissionKeyNames, $permissionCategories));
			$translationTables['PermissionKeyDescription'] = array('name' => t('Permission key descriptions'), 'rows' => self::buildTranslationRows('PermissionKeyDescription', $permissionKeyDescriptions, $permissionCategories));
			$translationTables['PermissionAccessEntityTypeName'] = array('name' => t('Access entity type names'), 'rows' => self::buildTranslationRows('PermissionAccessEntityTypeName', $permissionAccessEntityTypeNames));
			$this->set('translationTables', $translationTables);
			$currentTable = $this->post('currentTable');
			if(!(is_string($currentTable) && array_key_exists($currentTable, $translationTables))) {
				$currentTable = $this->get('table');
				if(!(is_string($currentTable) && array_key_exists($currentTable, $translationTables))) {
					reset($translationTables);
					$currentTable = key($translationTables);
					reset($translationTables);
				}
			}
			$this->set('currentTable', $currentTable);
		}
		$this->set('locales', $locales);
	}
	public function updated() {
		$this->set('message', t('The translations have been saved.'));
		$this->view();
	}
	public function update() {
		if($this->isPost()) {
			if ($this->token->validate('update_translations')) {
				try {
					$locales = self::getLocales();
					$locale = $this->post('locale');
					if(!array_key_exists($locale, $locales)) {
						throw new Exception(t("Invalid locale identifier: '%s'", $locale));
					}
					$translation = Loader::helper('translation_file');
					$translation->setHeader('Language', $locale);
					foreach($this->post() as $name => $translated) {
						$translated = is_string($translated) ? trim($translated) : '';
						if(strlen($translated) && preg_match('/^(.+)_([1-9][0-9]*)$/', $name, $match)) {
							$context = $match[1];
							$id = intval($match[2]);
							switch($context) {
								case 'AttributeSetName':
									$as = AttributeSet::getByID($id);
									if((!is_object($as)) || $as->isError()) {
										throw new Exception(t("Unable to find the attribute set with id '%s'", $id));
									}
									$translation->addWithContext($context, $as->getAttributeSetName(), $translated);
									break;
								case 'AttributeKeyName':
									$ak = AttributeKey::getInstanceByID($id);
									if((!is_object($ak)) || $ak->isError()) {
										throw new Exception(t("Unable to find the attribute key with id '%s'", $id));
									}
									$translation->addWithContext($context, $ak->getAttributeKeyName(), $translated);
									break;
								case 'AttributeTypeName':
									$at = AttributeType::getByID($id);
									if((!is_object($at)) || $at->isError()) {
										throw new Exception(t("Unable to find the attribute type with id '%s'", $id));
									}
									$translation->addWithContext($context, $at->getAttributeTypeName(), $translated);
									break;
								case 'PermissionKeyName':
									$pk = PermissionKey::getByID($id);
									if((!is_object($pk)) || $pk->isError()) {
										throw new Exception(t("Unable to find the permission key with id '%s'", $id));
									}
									$translation->addWithContext($context, $pk->getPermissionKeyName(), $translated);
									break;
								case 'PermissionKeyDescription':
									$pk = PermissionKey::getByID($id);
									if((!is_object($pk)) || $pk->isError()) {
										throw new Exception(t("Unable to find the permission key with id '%s'", $id));
									}
									$translation->addWithContext($context, $pk->getPermissionKeyDescription(), $translated);
									break;
								case 'PermissionAccessEntityTypeName':
									$pt = PermissionAccessEntityType::getByID($id);
									if((!is_object($pt)) || $pt->isError()) {
										throw new Exception(t("Unable to find the access entity type with id '%s'", $id));
									}
									$translation->addWithContext($context, $pt->getAccessEntityTypeName(), $translated);
									break;
							}
						}
					}
					$filename = DIR_CONFIG_SITE . '/special_items/' . $locale . '.mo';
					if($translation->isEmpty()) {
						if(is_file($filename)) {
							@unlink($filename);
						}
					}
					else {
						$foldername = dirname($filename);
						if(!is_dir($foldername)) {
							@mkdir($foldername);
							if(!is_dir($foldername)) {
								throw new Exception(t('Unable to create folder %s', $foldername));
							}
						}
						if(!$translation->save($filename)) {
							throw new Exception(t('Unable to save file %s', $filename));
						}
					}
					$this->redirect('/dashboard/system/basics/translate_special_items/updated/?locale=' . rawurlencode($locale) . '&table=' . rawurlencode($this->post('currentTable')));
				}
				catch(Exception $x) {
					$this->error->add($x->getMessage());
				}
			}
			else {
				$this->error->add($this->token->getErrorMessage());
			}
		}
		$this->view();
	}

	protected static function buildTranslationRows($context, $items, $groupedBy = false) {
		$rows = '';
		if($groupedBy) {
			foreach($groupedBy as $gbID => $gbName) {
				if(array_key_exists($gbID, $items)) {
					$subRows = self::buildTranslationRows($context, $items[$gbID]);
					if(strlen($subRows)) {
						$rows .= '<tr><th colspan="2">' . h($gbName) . '</th></tr>';
						$rows .= $subRows;
					}
				}
			}
		}
		else {
			foreach($items as $id => $translation) {
				if(strlen($translation['source'])) {
					$rows .= '<tr><td style="width:33%">' . h($translation['source']) . '</td><td><input type="text" name="' . h($context . '_' . $id) . '" style="width:100%" placeholder="' . h(t('Same as English (US)')) . '" value="' . h($translation['translated']) . '" />';
				}
			}
		}
		return $rows;
	}

	protected static function sortBySource($a, $b) {
		return strcasecmp($a['source'], $b['source']);
	}
}
