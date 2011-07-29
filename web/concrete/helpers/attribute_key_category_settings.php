<?php
defined('C5_EXECUTE') or die("Access Denied.");
class AttributeKeyCategorySettingsHelper extends Object {
	
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
	
	private $registeredSettings = array(
		'collection' => array(
			'url_search'				=> 'dashboard/sitemap/search',
			'url_insert'				=> 'dashboard/composer',
			'url_structure'				=> 'dashboard/pages/attributes',
			'url_permissions_hidden'	=> TRUE,
			'url_drop_hidden'			=> TRUE,
			'static_attributes'			=> array(
												'ctName'				=> 'Type',
												'vObj/cvName'			=> 'Name',
												'vObj/cvDatePublic'		=> 'Public Date',
												'cDateModified'			=> 'Date Modified',
												'vObj/cvAuthorUname'	=> 'Owner'
											)
		),
		'user' => array(
			'url_search'				=> 'dashboard/users',
			'url_insert'				=> 'dashboard/users/add',
			'url_structure'				=> 'dashboard/users/attributes',
			'url_permissions'			=> 'dashboard/settings/set_permissions',
			'url_drop_hidden'			=> TRUE
			
		),
		'file' => array(
			'url_search'				=> 'dashboard/files',
			'url_insert_hidden'			=> TRUE,
			'url_structure'				=> 'dashboard/files/attributes',
			'url_permissions'			=> 'dashboard/files/access',
			'url_drop_hidden'			=> TRUE
		)
	);

	public static function registerSetting($akCategoryHandle, $settingHandle, $parameters) {
		$akc = AttributeKeyCategorySettingsHelper::getInstance();
		$akc->registeredSettings[$akCategoryHandle][$settingHandle] = $parameters;	
	}

	public function getRegisteredSettings($akCategoryHandle) {
		$akcsh = AttributeKeyCategorySettingsHelper::getInstance();
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
}?>