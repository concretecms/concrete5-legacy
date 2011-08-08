<?php
defined('C5_EXECUTE') or die("Access Denied.");
class AttributeKeyCategorySettingsHelper extends Object {
	
	private $registeredSettings = array();
	
	public function __construct() {
		$this->registeredSettings['collection'] = array(
			'url_insert'				=> 'dashboard/composer',
			'url_permissions_hidden'	=> TRUE,
			'url_drop_hidden'			=> TRUE,
			'standard_properties'		=> array(
				new DatabaseItemListColumn('ctName', t('Type'), 'getCollectionTypeName', false),
				new DatabaseItemListColumn('cvName', t('Name'), 'getCollectionName'),
				new DatabaseItemListColumn('cvDatePublic', t('Public Date'), 'getCollectionDatePublic'),
				new DatabaseItemListColumn('cDateModified', t('Date Modified'), 'getCollectionDateLastModified')
			)
		);
		$this->registeredSettings['user'] = array(
			'url_insert'				=> 'dashboard/users/add',
			'url_permissions'			=> 'dashboard/settings/set_permissions',
			'url_drop_hidden'			=> TRUE,
			'standard_properties'		=> array(
				new DatabaseItemListColumn('uName', t('Username'), 'getUserName'),
				new DatabaseItemListColumn('uEmail', t('Email Address'), 'getUserEmail'),
				new DatabaseItemListColumn('uDateAdded', t('Date Added'), 'getUserDateAdded'),
				new DatabaseItemListColumn('uNumLogins', t('# Logins'), 'getNumLogins')
			)
		);
		$this->registeredSettings['file'] = array(
			'url_insert_hidden'			=> TRUE,
			'url_permissions'			=> 'dashboard/files/access',
			'url_drop_hidden'			=> TRUE,
			'loaders'					=> array(
				'file_list' => Loader::model('file_list')
			),
			'standard_properties'		=> array(
				new DatabaseItemListColumn('fvType', t('Type'), 'getType', false),
				new DatabaseItemListColumn('fvTitle', t('Title'), 'getTitle'),
				new DatabaseItemListColumn('fDateAdded', t('Added'), array('FileManagerDefaultColumnSet', 'getFileDateAdded')),
				new DatabaseItemListColumn('fvDateAdded', t('Active'), array('FileManagerDefaultColumnSet', 'getFileDateActivated')),
				new DatabaseItemListColumn('fvSize', t('Size'), 'getSize'),
				new DatabaseItemListColumn('fvAuthorName', t('Author'), 'getAuthorName')
			)
		);
	}
	
	/** 
	 * Returns an instance of the systemwide AttributeKeyCategory object.
	 */
	public function getInstance() {
		static $instance;
		if (!isset($instance)) {
			$v = __CLASS__;
			$instance = new $v;
		}
		return $instance;
	}
	
	public function getActions() {
		return array('search', 'insert', 'structure', 'permissions', 'drop');
	}
	public function getActionIconSrc($action) {
		switch($action) {
			case 'insert':
				$iconSrc = ASSETS_URL_IMAGES.'/icons/add.png';
				break;
			case 'structure':
				$iconSrc = ASSETS_URL_IMAGES.'/icons/wrench.png';
				break;
			case 'permissions':
				$iconSrc = ASSETS_URL_IMAGES.'/icons/icon_header_permissions.png';
				break;
			case 'drop':
				$iconSrc = ASSETS_URL_IMAGES.'/icons/delete_small.png';
				break;
			default:
				$iconSrc = ASSETS_URL_IMAGES.'/icons/'.$action.'.png';
				break;
		}
		return $iconSrc;
	}

	public static function registerSetting($akCategoryHandle, $settingHandle, $parameters) {
		$akc = AttributeKeyCategorySettingsHelper::getInstance();
		$akc->registeredSettings[$akCategoryHandle][$settingHandle] = $parameters;	
	}

	public function getRegisteredSettings($akCategoryHandle) {
		$akcsh = AttributeKeyCategorySettingsHelper::getInstance();
		$akc = AttributeKeyCategory::getByHandle($akCategoryHandle);
		if($akc->getPackageID()){
			$akcsh->registerSetting($akCategoryHandle, 'url_drop_hidden', TRUE);
		}
		return $akcsh->registeredSettings[$akCategoryHandle];
	}
	
	private $allowAddToPackage = array();

	public static function allowAddToPackage($pkg) {
		if(is_object($pkg)) {
			$pkgHandle = $pkg->getPackageHandle();
		} elseif(is_numeric($pkg)) {
			$pkgHandle = Package::getByID($pkg)->getPackageHandle();
		} else {
			$pkgHandle = $pkg;
		}
		$akc = AttributeKeyCategorySettingsHelper::getInstance();
		$akc->allowAddToPackage[] = $pkgHandle;	
	}
	
	public function canAddToPackage($pkg) {
		if(is_object($pkg)) {
			$pkgHandle = $pkg->getPackageHandle();
		} elseif(is_numeric($pkg)) {
			$pkgHandle = Package::getByID($pkg)->getPackageHandle();
		} else {
			$pkgHandle = $pkg;
		}
		$akc = AttributeKeyCategorySettingsHelper::getInstance();
		return in_array($pkgHandle, $akc->allowAddToPackage);
	}
	
	public function getAllColumnHeaders($akCategoryHandle) {
		Loader::model('attribute_key_category_item_list');
		$akcdc = AttributeKeyCategoryColumnSet::getCurrent($akCategoryHandle);
		return $akcdc;
	}
}?>