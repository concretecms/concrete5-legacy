<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class AttributeKeyCategoryItemsAttributeTypeController extends NumberAttributeTypeController  {

	protected $searchIndexFieldDefinition = 'X NULL';
	
	public function getValue($displayMode = FALSE) {
		$this->load();
		switch($displayMode) {
			case 'getDisplayValue':
				return $this->getDisplayValue();
			case 'getDisplaySanitizedValue':
				return $this->getDisplaySanitizedValue();
			case 'getSearchIndexValue':
				return $this->getSearchIndexValue();
			default:
				$db = Loader::db();
				$value = $db->GetOne("select value from atDefault where avID = ?", array($this->getAttributeValueID()));
				$value = explode(', ', $value);
				
				$akc = AttributeKeyCategory::getByHandle($this->akCategoryHandle);
				foreach($value as $ID) {
					if(!empty($ID)) {
						$return[] = $akc->getItemObject($ID);
					}
				}
				
				return $return;
		}
	}
	
	public function getDisplayValue() {
		$value = $this->getValue();
		if($value) {
			foreach($value as $item) {
				if($display) {
					if($name = $item->getAttribute('name')) {
						$display .= ', '.$name;
					} else {
						$display .= ', '.$item->ID;
					}
				} else {
					if($name = $item->getAttribute('name')) {
						$display = $name;
					} else {
						$display = $item->ID;
					}
				}
			}
		}
		return $display;
	}
	
	public function getDisplaySanitizedValue() {
		$value = $this->getValue();
		if($value) {
			foreach($value as $item) {
				if($item){
					if($display) {
						$display .= "\n".$item->ID;
					} else {
						$display = $item->ID;
					}
				}
			}
		}
		return $display;
	}
	
	public function getSearchIndexValue() {
		$value = $this->getValue();
		$display = '';
		if($value) {
			foreach($value as $item) {
				if(!empty($display)) {
					if($name = $item->getAttribute('name')) {
						$display .= "\n".$name;
					} else {
						$display .= "\n".$item->ID;
					}
				} else {
					if($name = $item->getAttribute('name')) {
						$display = $name;
					} else {
						$display = $item->ID;
					}
				}
			}
		}
		return $display;
	}
	
	protected function load() {
		$ak = $this->getAttributeKey();
		if (!is_object($ak)) {
			return false;
		}
		
		$db = Loader::db();
		$row = $db->GetRow('SELECT akCategoryHandle FROM atAttributeKeyCategoryItemsSettings WHERE akID = ?', $ak->getAttributeKeyID());
		$this->akCategoryHandle = $row['akCategoryHandle'];
		$this->set('akCategoryHandle', $this->akCategoryHandle);			
	}
	
	public function type_form() {
		$this->load();
	}
	
	public function saveKey($data) {
		parent::saveKey($data);
		
		$ak = $this->getAttributeKey();
		$db = Loader::db();
		$db->Replace('atAttributeKeyCategoryItemsSettings', array(
			'akID' => $ak->getAttributeKeyID(), 
			'akCategoryHandle' => $data['akCategory']
		), array('akID'), true);
	}
	
	public function form() {
		$this->load();
		$html = Loader::helper('html');
		$this->akID = $this->attributeKey->getAttributeKeyID();
		$searchInstance = $_REQUEST['akCategoryHandle'] . time();
		if (isset($_REQUEST['searchInstance'])) {
			$searchInstance = $_REQUEST['searchInstance'];
		}
		$this->addHeaderItem(Loader::helper('html')->javascript('ccm.attributekeycategory.js'));
		$akcis = Loader::helper('form/attribute_key_category_item_selector');		
		if (is_object($this->attributeValue)) {
			$value = $this->getAttributeValue()->getValue();
		}
		print $akcis->selectItems($this->akCategoryHandle, $this->field('value'),
								$value, $this->akID, $searchInstance);
	}
	
	// run when we call setAttribute(), instead of saving through the UI
	public function saveValue($value) {
		$this->load();
		$save = '';
		if(is_array($value)) {
			foreach($value as $ID) {
				if(!empty($save)) {
					$save .= ', '.$ID;
				} else {
					$save = $ID;
				}
			}
		} else {
			$save = $value;
		}
		$db = Loader::db();
		$db->Replace('atDefault', array('avID' => $this->getAttributeValueID(), 'value' => $save), 'avID', true);
	}
	
	public function saveForm($data) {
		$this->saveValue($data['value']);
	}
	
	public function validateForm($data) {
		foreach($data['value'] as $ID) {
			$akc = AttributeKeyCategory::getByHandle($data['akCategoryHandle']);
			if(is_object($akc)) {
				$return[] = $akc->getItemObject($ID);
			}
		}
		if($return) {
			return true;
		}
	}
	
	public function deleteKey() {
		$db = Loader::db();
		$db->Execute('DELETE FROM atAttributeKeyCategoryItemsSettings WHERE akID = ?', array($this->attributeKey->getAttributeKeyID()));
	}
		
}