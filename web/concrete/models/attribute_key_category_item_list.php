<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
*
* An object that allows a filtered list of Virtual Table Items to be returned.
* @package Virtual Table
*
*/

Loader::model('attribute_key_category_item');

class AttributeKeyCategoryItemList extends DatabaseItemList {
	
	protected $attributeFilters = array();
	protected $attributeClass = 'AttributeKey';
	protected $autoSortColumns = array('ID');
	protected $itemsPerPage = 10;
	public $user = NULL;
	
	public function __construct($akCategoryHandle) {
		$this->akCategoryHandle = $akCategoryHandle;
	}
	
	protected function setBaseQuery() {
		$this->setQuery('SELECT DISTINCT akcisi.* FROM AttributeKeyCategoryItems akci ');
		$this->filter('akci.akCategoryHandle', $this->akCategoryHandle, '=');
	}
	protected function setPermissionFilters() {
		// check against logged in user
		$u = $this->user;
		if(is_null($u)) {
			$u = new User;
		}
		
		Loader::model('attribute_key_category_item_permission');
		$akcip = AttributeKeyCategoryItemPermission::get('GLOBAL');
		if($akcip->canRead($u)) {
			return;
		}
		$akcip = AttributeKeyCategoryItemPermission::get($this->akCategoryHandle);
		if($akcip->canRead($u)) {
			return;
		}
		
		$akcip = AttributeKeyCategoryItemPermission::get('GLOBAL');
		if(!$akcip->canSearch($u)) {
			$defaultFail = TRUE;
		}
		if($defaultFail) {
			$vtp = AttributeKeyCategoryItemPermission::get($this->akCategoryHandle);
			if(!$vtp->canSearch($u)) {
				throw new Exception('Permission Error: User does not have access to search this table.');
			}
		}
		
		if(!($u instanceof User)) {
			throw new Exception('Permission Error: User does not exist.');
		}
		
		$uID = -1;
		if ($u->isRegistered()) {
			$uID = $u->getUserID();
		}
		$this->userPostQuery .= ' AND (akci.uID = '.$uID;
		$this->userPostQuery .= ' OR (akcip.uID = '.$uID.' AND akcip.canRead = 1) ';
		
		$groups = $u->getUserGroups();
		$groupIDs = array();
		foreach($groups as $key => $value) {
			$this->userPostQuery .= 'OR (akcip.gID = '.$key.' AND akcip.canRead = 1) ';
		}
		$this->userPostQuery .= ')';
	}
	protected function createQuery() {
		if(!$this->queryCreated){
			$this->setBaseQuery();
			
			$this->setupAttributeFilters('LEFT JOIN AttributeKeyCategoryItemSearchIndex akcisi ON (akcisi.ID = akci.ID)');
						
			if(!$this->ignorePermissions) {
				$this->userQuery .= 'LEFT JOIN AttributeKeyCategoryItemPermissions akcip ON (akcip.ID = akci.ID) ';
				$this->setPermissionFilters();
			}
			
			$this->queryCreated=1;
		}
	}
	
	/* magic method for filtering by page attributes. */
	public function __call($nm, $a) {
		if (substr($nm, 0, 8) == 'filterBy') {
			$txt = Loader::helper('text');
			$attrib = $txt->uncamelcase(substr($nm, 8));
			if (count($a) == 2) {
				$this->filterByAttribute($attrib, $a[0], $a[1]);
			} else {
				$this->filterByAttribute($attrib, $a[0]);
			}
		}		
	}
	
	// Returns an array of AttributeKeyCategoryItems based on current filter settings
	public function get($itemsToGet = 0, $offset = 0, $getAs = 'objects') {
		$akcis = array(); 
		$this->createQuery();
		$r = parent::get($itemsToGet, $offset);
		if($getAs == 'objects' || $getAs == 'object') {
				foreach($r as $row) {
					$no = AttributeKeyCategoryItem::getByID($row['ID']);			
					$akcis[] = $no;
				}
		} elseif($getAs == 'display' || $getAs == 'displayValue' || $getAs == 'displayValues') {
			$akList = AttributeKey::getList($this->akCategoryHandle);
			foreach($r as $row) {
				foreach($akList as $ak) {
					$db = Loader::db();
					$avID = $db->GetOne('SELECT avID FROM AttributeKeyCategoryItemAttributeValues WHERE ID = ? AND akID = ?', 
										array($row['ID'], $ak->akID)
									);
					$av = AttributeValue::getById($avID);
					if(is_object($av)) {
						$akcis[$row['ID']][$ak->akHandle] = $av->getValue('display');
					} else {
						$akcis[$row['ID']][$ak->akHandle] = '';
					}
				}
			}
		} else {
			$akList = AttributeKey::getList($this->akCategoryHandle);
			foreach($r as $row) {
				foreach($akList as $ak) {
					$akcis[$row['ID']][$ak->akHandle] = $row['ak_'.$ak->akHandle];
				}
			}
		}
		return $akcis;
	}
	
	public function getTotal(){ 
		$this->createQuery();
		return parent::getTotal();
	}	
	
	public function filterByID($itemID, $comparison = '=') {
		$this->filter('ID', $itemID, $comparison);
	}
	
	public function filterByKeywords($keywords) {
		$db = Loader::db();
		$keywordsExact = $db->quote($keywords);
		$qkeywords = $db->quote('%' . $keywords . '%');
		$keys = AttributeKey::getSearchableIndexedList($this->akCategoryHandle);
		$attribsStr = '';
		foreach ($keys as $ak) {
			$cnt = $ak->getController();			
			$attribsStr.=' OR ' . $cnt->searchKeywords($keywords);
		}
		$this->filter(false, '(akci.ID LIKE ' . $qkeywords . $attribsStr . ')');
	}
}

class AttributeKeyCategoryDefaultColumnSet extends AttributeKeyCategoryColumnSet {
	protected $attributeClass = 'AttributeKey';
	public function __construct($akCategoryHandle) {
		$this->akCategoryHandle = $akCategoryHandle;
		$akcsh = Loader::helper('attribute_key_category_settings');
		$rs = $akcsh->getRegisteredSettings($akCategoryHandle);
		if(is_array($rs['standard_properties'])) foreach($rs['standard_properties'] as $sp) {
			$this->addColumn($sp);
		}
		if(is_array($rs['standard_properties'])) {
			$this->setDefaultSortColumn($rs['standard_properties'][0], 'desc');
		} else {
			$list = AttributeKey::getList($akCategoryHandle);
			if(count($list)) {
				$this->setDefaultSortColumn(
					new DatabaseItemListColumn('ak_'.$list[0]->getAttributeKeyHandle(), $list[0]->getAttributeKeyName(), NULL,
					'desc')
				);
			}
		}
	}
	public function getColumnByKey($key) {
		if (substr($key, 0, 3) == 'ak_') {
			eval('$ak = '.$this->attributeClass.'::getByHandle("'.substr($key, 3).'", "'.$this->akCategoryHandle.'");');
			$col = new DatabaseItemListAttributeKeyColumn($ak);
			return $col;
		} else {
			foreach($this->columns as $col) {
				if ($col->getColumnKey() == $key) {
					return $col;			
				}
			}
		}
	}
}

class AttributeKeyCategoryAvailableColumnSet extends AttributeKeyCategoryDefaultColumnSet {
	protected $attributeClass = 'AttributeKey';
	public function __construct($akCategoryHandle) {
		parent::__construct($akCategoryHandle);
		$this->akCategoryHandle = $akCategoryHandle;
		
		//Set all of the attribute columns. DefaultColumnSet only sets the standard properties column, or the first attribute column.
		$akList = AttributeKey::getList($akCategoryHandle);
		foreach($akList as $ak){
			$this->addColumn($this->getColumnByKey('ak_'.$ak->getKeyHandle()));
		}
	}
}

class AttributeKeyCategoryColumnSet extends DatabaseItemListColumnSet {
	protected $attributeClass = 'AttributeKey';
	public function __construct($akCategoryHandle) {
		$this->akCategoryHandle = $akCategoryHandle;
	}
	public function getCurrent($akCategoryHandle = NULL) {
		if(!$akCategoryHandle) $akCategoryHandle = $this->akCategoryHandle;
		$u = new User();
		$akcdc = $u->config(strtoupper($akCategoryHandle).'_LIST_DEFAULT_COLUMNS');
		if ($akcdc != '') {
			$akcdc = @unserialize($akcdc);
		}
		if (!($akcdc instanceof DatabaseItemListColumnSet)) {
			$akcdc = new AttributeKeyCategoryDefaultColumnSet($akCategoryHandle);
		}
		return $akcdc;
	}
}