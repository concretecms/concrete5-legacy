<?php
defined('C5_EXECUTE') or die("Access Denied.");
class AttributeKeyCategory extends Object {

	const ASET_ALLOW_NONE = 0;
	const ASET_ALLOW_SINGLE = 1;
	const ASET_ALLOW_MULTIPLE = 2;
	
	public function getRegisteredSettings($akCategoryHandle = NULL) {
		if(!$akCategoryHandle) {
			$akCategoryHandle = $this->akCategoryHandle;
		}
		$akcsh = Loader::helper('attribute_key_category_settings');
		return $akcsh->getRegisteredSettings($akCategoryHandle);
	}
	
	public static function getByID($akCategoryID) {
		$db = Loader::db();
		$row = $db->GetRow('select akCategoryID, akCategoryHandle, akCategoryAllowSets, pkgID from AttributeKeyCategories where akCategoryID = ?', array($akCategoryID));
		if (isset($row['akCategoryID'])) {
			$akc = new AttributeKeyCategory();
			$akc->setPropertiesFromArray($row);
			return $akc;
		}
	}
	
	public static function getByHandle($akCategoryHandle) {
		$db = Loader::db();
		$row = $db->GetRow('select akCategoryID, akCategoryHandle, akCategoryAllowSets, pkgID from AttributeKeyCategories where akCategoryHandle = ?', array($akCategoryHandle));
		if (isset($row['akCategoryID'])) {
			$akc = new AttributeKeyCategory();
			$akc->setPropertiesFromArray($row);
			return $akc;
		}
	}
	
	public function handleExists($akHandle) {
		$db = Loader::db();
		$r = $db->GetOne("select count(akID) from AttributeKeys where akHandle = ? and akCategoryID = ?", array($akHandle, $this->akCategoryID));
		return $r > 0;
	}
	
	public function akCategoryHandleExists($akCategoryHandle) {
		$db = Loader::db();
		$r = $db->GetOne("select count(*) from AttributeKeyCategories where akCategoryHandle = ?", array($akCategoryHandle));
		return $r > 0;
	}
	
	public function getAttributeKeyByHandle($akHandle) {
		if(!Loader::model('attribute/categories/' . $this->akCategoryHandle, $this->getPackageHandle())) {
			if(!Loader::model('attribute/categories/' . $this->akCategoryHandle)) {
				$obj = new AttributeKey;
				$ak = $obj->getByHandle($akHandle, $this->akCategoryHandle);
				return $ak;
			}
		}		
		$txt = Loader::helper('text');
		$className = $txt->camelcase($this->akCategoryHandle);
		$c1 = $className . 'AttributeKey';
		$ak = call_user_func(array($c1, 'getByHandle'), $akHandle);
		return $ak;
	}

	public function getAttributeKeyByID($akID) {
		if(!Loader::model('attribute/categories/' . $this->akCategoryHandle, $this->getPackageHandle())) {
			if(!Loader::model('attribute/categories/' . $this->akCategoryHandle)) {
				$obj = new AttributeKey;
				$ak = $obj->getByID($akID);
				return $ak;
			}
		}		
		$txt = Loader::helper('text');
		$className = $txt->camelcase($this->akCategoryHandle);
		$c1 = $className . 'AttributeKey';
		$ak = call_user_func(array($c1, 'getByID'), $akID);
		return $ak;
	}

	public function getNewAttributeKey() {
		if(!Loader::model('attribute/categories/' . $this->akCategoryHandle, $this->getPackageHandle())) {
			if(!Loader::model('attribute/categories/' . $this->akCategoryHandle)) {
				$ak = new AttributeKey($this->akCategoryHandle);
				return $ak;
			}
		}		
		$txt = Loader::helper('text');
		$className = $txt->camelcase($this->akCategoryHandle) . 'AttributeKey';
		$ak = new $className();
		return $ak;
	}

	public function getUnassignedAttributeKeys() {
		$db = Loader::db();
		$r = $db->Execute('select AttributeKeys.akID from AttributeKeys left join AttributeSetKeys on AttributeKeys.akID = AttributeSetKeys.akID where asID is null and akCategoryID = ?', $this->akCategoryID);
		$keys = array();
		$cat = AttributeKeyCategory::getByID($this->akCategoryID);
		while ($row = $r->FetchRow()) {
			$keys[] = $cat->getAttributeKeyByID($row['akID']);
		}
		return $keys;		
	}	

	public static function getListByPackage($pkg) {
		$db = Loader::db();
		$list = array();
		$r = $db->Execute('select akCategoryID from AttributeKeyCategories where pkgID = ? order by akCategoryID asc', array($pkg->getPackageID()));
		while ($row = $r->FetchRow()) {
			$list[] = AttributeKeyCategory::getByID($row['akCategoryID']);
		}
		$r->Close();
		return $list;
	}	
	
	public function getAttributeKeyCategoryID() {return $this->akCategoryID;}
	public function getAttributeKeyCategoryHandle() {return $this->akCategoryHandle;}
	public function getPackageID() {return $this->pkgID;}
	public function getPackageHandle() {return PackageList::getHandle($this->pkgID);}
	public function allowAttributeSets() {return $this->akCategoryAllowSets;}
	public function setAllowAttributeSets($val) {
		$db = Loader::db();
		$db->Execute('update AttributeKeyCategories set akCategoryAllowSets = ? where akCategoryID = ?', array($val, $this->akCategoryID));
		$this->akCategoryAllowSets = $val;
	}
	
	public function getAttributeSets() {
		$db = Loader::db();
		$r = $db->Execute('select asID from AttributeSets where akCategoryID = ? order by asID asc', $this->akCategoryID);
		$sets = array();
		while ($row = $r->FetchRow()) {
			$sets[] = AttributeSet::getByID($row['asID']);
		}
		return $sets;
	}
	
	public function clearAttributeKeyCategoryColumnHeaders() {
		$db = Loader::db();
		$db->Execute('update AttributeKeys set akIsColumnHeader = 0 where akCategoryID = ?', $this->akCategoryID);
	}
	
	public function associateAttributeKeyType($at) {
		$db = Loader::db();
		$db->Execute('insert into AttributeTypeCategories (atID, akCategoryID) values (?, ?)', array($at->getAttributeTypeID(), $this->akCategoryID));
	}
	
	public function clearAttributeKeyCategoryTypes() {
		$db = Loader::db();
		$db->Execute('delete from AttributeTypeCategories where akCategoryID = ?', $this->akCategoryID);
	}

	/** 
	 * note, this does not remove anything but the direct data associated with the category
	 */
	public function delete() {
		$db = Loader::db();
		$this->clearAttributeKeyCategoryTypes();
		$this->clearAttributeKeyCategoryColumnHeaders();
		$db->Execute('delete from AttributeKeyCategories where akCategoryID = ?', $this->akCategoryID);		
	}
	/**
	 * note, this DOES remove all items and attirbute values associated with the category
	 */
	public function drop() {
		$items = $this->getItemList();
		$items->ignorePermissions = TRUE;
		$items = $items->get(0,0,'objects');
		foreach($items as $item) {
			$item->delete();
		}
		foreach($this->getAttributeSets() as $set) {
			$set->delete();
		}
		foreach($this->getAttributeKeyList() as $ak) {
			$ak->delete();
		}
		$this->delete();
	}
	
	public function getList() {
		$db = Loader::db();
		$cats = array();
		$r = $db->Execute('select akCategoryID from AttributeKeyCategories order by akCategoryID asc');
		while ($row = $r->FetchRow()) {
			$cats[] = AttributeKeyCategory::getByID($row['akCategoryID']);
		}
		return $cats;
	}
	
	public static function add($akCategoryHandle, $akCategoryAllowSets = AttributeKeyCategory::ASET_ALLOW_NONE, $pkg = false) {
		if(self::akCategoryHandleExists($akCategoryHandle)) throw new Exception('AttributeKeyCategory handle already in use.');
		$db = Loader::db();
		if(is_object($pkg)) {
			$pkgID = $pkg->getPackageID();
			$pkgHandle = $pkg->getPackageHandle();
		} elseif(is_numeric($pkg)) {
			$pkgID = $pkg;
		} elseif(!empty($pkg)) {
			$pkgID = Package::getByHandle($pkg)->getPackageID();
			$pkgHandle = $pkg;
		}
		
		$db->Execute('insert into AttributeKeyCategories (akCategoryHandle, akCategoryAllowSets, pkgID) values (?, ?, ?)', array($akCategoryHandle, $akCategoryAllowSets, $pkgID));
		$id = $db->Insert_ID();
		
		if(!Loader::model('attribute/categories/' . $akCategoryHandle, $pkgHandle)) {
			if(!Loader::model('attribute/categories/' . $akCategoryHandle)) {
				return AttributeKeyCategory::getByID($id);
			}
		}		
		$txt = Loader::helper("text");
		$class = $txt->camelcase($akCategoryHandle) . 'AttributeKey';
		$obj = new $class;
		$class = get_parent_class($obj);
		$parent = new $class;
		if($parent->getIndexedSearchTable() != $obj->getIndexedSearchTable()) {
			$obj->createIndexedSearchTable();
		}
		
		return AttributeKeyCategory::getByID($id);
	}

	public function addSet($asHandle, $asName, $pkg = false) {
		if ($this->akCategoryAllowSets > AttributeKeyCategory::ASET_ALLOW_NONE) {
			$db = Loader::db();
			$pkgID = 0;
			if (is_object($pkg)) {
				$pkgID = $pkg->getPackageID();
			}
			$db->Execute('insert into AttributeSets (asHandle, asName, akCategoryID, pkgID) values (?, ?, ?, ?)', array($asHandle, $asName, $this->akCategoryID, $pkgID));
			$id = $db->Insert_ID();
			
			$as = AttributeSet::getByID($id);
			return $as;
		}
	}
	
	public function getItemList($akCategoryHandle = NULL) {
		if($akCategoryHandle) {
			$akc = AttributeKeyCategory::getByHandle($akCategoryHandle);
			return $akc->getItemList();
		}
		
		$rs = $this->getRegisteredSettings();
		$txt = Loader::helper("text");
		$class = $txt->camelcase($this->akCategoryHandle) . 'List';
		switch($this->getAttributeKeyCategoryHandle()) {
			case 'collection':
				Loader::model('page_list');
				$list = new PageList;
				break;
			default:
				if(Loader::model($this->akCategoryHandle . '_list', $this->getPackageHandle())) {
					$list = new $class;
				} elseif(Loader::model(str_replace($this->getPackageHandle().'_', '', $this->akCategoryHandle) . '_list', $this->getPackageHandle())) {
					$list = new $class;
				} elseif(Loader::model(str_replace($this->getPackageHandle().'_', '', $this->akCategoryHandle) . '/list', $this->getPackageHandle())) {
					$list = new $class;
				} elseif($rs['list_model_path']) {
					Loader::model($rs['list_model_path'], $this->getPackageHandle());
					if($rs['list_model_class']) {
						$list = new $rs['list_model_class'];
					} else {
						$list = new $class;
					}
				} else {
					Loader::model('attribute_key_category_item_list');
					$list = new AttributeKeyCategoryItemList($this->getAttributeKeyCategoryHandle());
				}
				break;	
		}
		
		return $list;
	}
	
	public function getItemObject($ID = NULL) {
		$rs = $this->getRegisteredSettings();
		$txt = Loader::helper("text");
		$class = $txt->camelcase($this->getAttributeKeyCategoryHandle());
		if(!$this->getAttributeKeyCategoryHandle()) {
			return false;
		}
		switch($this->getAttributeKeyCategoryHandle()) {
			case 'collection':
				Loader::model('page');
				$item = new Page;
				break;
			case 'user':
				$item = new UserInfo;
				break;
			default:
				if($rs['item_model_path']) {
					Loader::model($rs['item_model_path'], $this->getPackageHandle());
					if($rs['item_model_class']) {
						$item = new $rs['item_model_class'];
					} else {
						$item = new $class;
					}
				} elseif(Loader::model($this->akCategoryHandle, $this->getPackageHandle())) {
					$item = new $class;
				} elseif(Loader::model(str_replace($this->getPackageHandle().'_', '', $this->akCategoryHandle).'/model', $this->getPackageHandle())) {
					$item = new $class;
				} else {
					Loader::model('attribute_key_category_item');
					$item = new AttributeKeyCategoryItem($this->getAttributeKeyCategoryHandle());
				}
				break;	
		}
		if($ID) {
			if(method_exists($item, 'getByID')) {
				$item = $item->getByID($ID);
			} else {
				$txt = Loader::helper('text');
				eval('$item = $item->getBy'.$txt->camelcase($this->getAttributeKeyCategoryHandle()).'ID($ID);');
				if(!$item) {
					eval('$item = $item->getBy'.$txt->camelcase(str_replace($this->getPackageHandle().'_', '', $this->getAttributeKeyCategoryHandle())).'ID($ID);');
				}
			}
			if(!$item->ID) {
				$item->ID = $ID;
			}
			return $item;	
		} else {
			return $item;
		}
	}
	
	public function getAttributeKeyList($akCategoryHandle = NULL) {
		if(!$akCategoryHandle) {
			if(is_object($this)) {
				$akCategoryHandle = $this->getAttributeKeyCategoryHandle();
			} else {
				return false;
			}
		}
		return AttributeKey::getList($akCategoryHandle);
	}
}
