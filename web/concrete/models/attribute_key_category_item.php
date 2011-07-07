<?php 

defined('C5_EXECUTE') or die(_("Access Denied."));

/**
*
* Returns a Virtual Table Item Object.
* @package Virtual Tables
*
*/
class AttributeKeyCategoryItem extends Object {
	
	public function __construct($akCategoryHandle = NULL) {
		if($akCategoryHandle != NULL) {
			$this->akCategoryHandle = $akCategoryHandle;
		}
	}
	
	public function getID() {
		return $this->ID;
	}
	
	public function add($uID = NULL, $akCategoryHandle = NULL) {
		if(!$akCategoryHandle) { $akCategoryHandle = $this->akCategoryHandle; }
		if(!AttributeKeyCategory::akCategoryHandleExists($akCategoryHandle)) {
			throw new Exception('Attribute Key Category "'.$akCategoryHandle.'" does not exists.');
		}
		
		if(!$uID) { $uID = 1; }
		$dt = Loader::helper('date');
		$timeDate = $dt->getLocalDateTime();
		
		$v = array('akCategoryHandle' => $akCategoryHandle, 'uID' => $uID);
		
		$db = Loader::db();
		$db->Execute('INSERT INTO AttributeKeyCategoryItems (akCategoryHandle, uID) values (?, ?)', $v);
		$id = $db->Insert_ID();
		
		$newObject = new AttributeKeyCategoryItem($akCategoryHandle);
		$newObject->load($id);
		
		$vtak = new AttributeKey($akCategoryHandle);
		foreach($vtak->getList() as $ak) {
			if($ak->akIsEditable == 0) {
				switch($ak->getAttributeKeyHandle()) {
					case 'increment_id':
						Loader::model('attribute_key_category_item_list');
						$vtil = new AttributeKeyCategoryItemList($akCategoryHandle);
						if($vtil->getTotal() > 1) {
							$vtak = new AttributeKey($akCategoryHandle);
							$ak = $vtak->getByHandle('increment_id');
							
							$vtil->sortBy($ak, 'desc');
							$lastItem = $vtil->get(1, 0, TRUE);
							$lastItem = $lastItem[0]->getAttribute('increment_id');
							$lastItem = (int)$lastItem;
							$lastItem++;
						} else {
							$lastItem = 0;
						}
						$newObject->setAttribute($ak, $lastItem);
						break;
					case 'date_created':
						$newObject->setAttribute($ak, $timeDate);
						break;
					case 'date_modified':
						//$newObject->setAttribute($ak, $timeDate);
						break;
					default:
						break;
				}
			} 
		}
		
		Events::fire('on_attribute_key_category_item_add', $newObject);
		$newObject->reindex();
		return $newObject;
	}
	
	public function update() {
		Events::fire('before_attribute_key_category_item_update', $this);
		$dt = Loader::helper('date');
		
		if($this->getAttribute('date_modified')) {
			$this->setAttribute('date_modified', $dt->getLocalDateTime());
		}
		$this->reindex();
		
		$newObject = self::getByID($this->ID);
		Events::fire('after_attribute_key_category_item_update', $newObject);
		return $newObject;
	}
	
	public function saveAttribute($key) {
		$value = $this->getAttributeValueObject($key, true);
		$key->saveAttributeForm($value);
		$db = Loader::db();
		$db->Replace('AttributeKeyCategoryItemAttributeValues', 
					array(
						'ID' => $this->getID(), 
						'akID' => $key->getAttributeKeyID(), 
						'avID' => $value->getAttributeValueID(),
						'akCategoryHandle' => "'".$this->akCategoryHandle."'"
					), 
					array(
						'ID', 
						'akID'
					)
				);
	}
	
	public function setOwner($uID = 1) {
		if($this->ID > 0) {
			$db = Loader::db();
			$db->Execute('UPDATE AttributeKeyCategoryItems SET uID = ? WHERE ID = ?', array($uID, $this->ID));
		}
	}
	
	public function duplicate() {
		
	}
	
	public function delete() {
		Events::fire('before_attribute_key_category_item_delete', $this);
		$db = Loader::db();
		$tables = array(
					'atAddress',
					'atBoolean',
					'atDateTime',
					'atDefault',
					'atFile',
					'atNumber',
					'atSelectOptionsSelected',
					'AttributeValues'
				);
		$rows = $db->GetArray('SELECT * FROM AttributeKeyCategoryItemAttributeValues WHERE ID = ?', array($this->ID));
		foreach($rows as $row) {
			foreach($tables as $table) {
				$db->Execute('DELETE FROM '.$table.' WHERE avID = ?', array($row['avID']));
			}
		}
		$db->Execute('DELETE FROM AttributeKeyCategoryItemAttributeValues WHERE ID = ?', array($this->ID));
		$db->Execute('DELETE FROM AttributeKeyCategoryItems WHERE ID = ?', array($this->ID));
		$db->Execute('DELETE FROM AttributeKeyCategoryItemSearchIndex WHERE ID = ?', array($this->ID));
		$db->Execute('DELETE FROM AttributeKeyCategoryItemPermissions WHERE ID = ?', array($this->ID));
		Events::fire('after_attribute_key_category_item_delete', $this);
	}
	
	public function load($id) {
		$db = Loader::db();
		$row = $db->GetRow('SELECT * FROM AttributeKeyCategoryItems WHERE ID = ?', array($id));
		if ($row['ID']) { 
			$this->akCategoryHandle = $row['akCategoryHandle'];
			$this->setPropertiesFromArray($row);
			;
			$vtak = new AttributeKey($row['akCategoryHandle']);
			foreach($vtak->getList() as $ak) {
				$this->attribs[$ak->getAttributeKeyHandle()] = $ak;
				$this->attribs[$ak->getAttributeKeyHandle()]->value = $this->getAttribute($ak);
			}
			return true;
		} else {
			return false;
		}
	}
	
	public static function getByID($id) {
		$no = new AttributeKeyCategoryItem;
		if ($no->load($id)) {
			return $no;
		}
	}
		
	public function setAttribute($ak, $value) {
		if (!is_object($ak)) {
			$ak = new AttributeKey($this->akCategoryHandle);
			$ak = $ak->getByHandle($ak);
		}
		$ak->setAttribute($this, $value);
		$this->update();
	}

	public function reindex() {	
		$searchableAttributes = array('ID' => $this->getID());
		$attribs = $this->getAttributes('getSearchIndexValue');
		
		$db = Loader::db();
		$db->Execute('DELETE FROM AttributeKeyCategoryItemSearchIndex WHERE ID = ?', array($this->getID()));
		$rs = $db->Execute('SELECT * FROM AttributeKeyCategoryItemSearchIndex WHERE ID = -1');
		
		AttributeKey::reindex('AttributeKeyCategoryItemSearchIndex', $searchableAttributes, $attribs, $rs);
	}
	
	public function clearAttribute($ak) {
			$db = Loader::db();
			$av = $this->getAttributeValueObject($ak);
			if (is_object($av)) {
				$av->delete();
			}
			$this->update();
		}
	
	public function getAttribute($ak, $displayMode = false) {
		if (!is_object($ak)) {
			$ak = AttributeKey::getByHandle($ak, $this->akCategoryHandle);
		}
		if (is_object($ak)) {
			$av = $this->getAttributeValueObject($ak);
			if (is_object($av)) {
				return $av->getValue($displayMode);
			}
		}
	}
	
	public function getAttributes($displayMode = false) {
		if(!$this->akCategoryHandle) {
			return false;
		}
		
		$list = array();
		foreach(AttributeKey::getList($this->akCategoryHandle) as $ak) {
			$list[$ak->akHandle] = $this->getAttribute($ak);
		}
		return $list;
	}
	
	public function getAttributeValueObject($ak, $createIfNotFound = false) {
		if (!is_object($ak)) {
			$ak = AttributeKey::getByHandle($ak, $this->akCategoryHandle);
		}
		$db = Loader::db();
		$av = false;
		$v = array($this->getID(), $ak->getAttributeKeyID());
		$avID = $db->GetOne('SELECT avID FROM AttributeKeyCategoryItemAttributeValues WHERE ID = ? and akID = ?', $v);
		if ($avID > 0) {
			$av = AttributeValue::getByID($avID);
			if (is_object($av)) {
				$av->setAttributeKey($ak);
			}
		}
		
		if ($createIfNotFound) {
			$cnt = 0;
		
			// Is this avID in use ?
			if (is_object($av)) {
				$cnt = $db->GetOne('SELECT COUNT(avID) FROM AttributeValues WHERE avID = ?', $av->getAttributeValueID());
			}
			
			if ((!is_object($av)) || ($cnt > 1)) {
				$av = $ak->addAttributeValue();
			}
		}
		
		return $av;
	}
	
}
