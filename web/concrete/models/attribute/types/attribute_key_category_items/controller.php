<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class AttributeKeyCategoryItemsAttributeTypeController extends DefaultAttributeTypeController  {

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
			case 'getIdArray':				
				return $this->getIdArray();
			default:				
				$IDs = $this->getIdArray();
				$akc = AttributeKeyCategory::getByHandle($this->akCategoryHandle);
				foreach($IDs as $ID) {
					if(!empty($ID) && ($akci = $akc->getItemObject($ID))) {
						$return[] = $akci;
					}
				}				
				return $return;
		}
	}
	
	public function getIdArray(){
		$db = Loader::db();
		$result = $db->GetOne("select value from atDefault where avID = ?", array($this->getAttributeValueID()));
		$IDs = explode(', ', $result);
		return $IDs;		
	}
	
	public function getDisplayValue() {
		$value = $this->getValue();
		if($value) {
			foreach($value as $item) {
				//if(method_exists($item, 'getAttribute')){
				if($display) {
					if($name = $item->getAttribute('name')) {
						$display .= ', '.$name;
					} else if($title = $item->getAttribute('title')) {
						$display .= ', '.$title;
					} else {
						$display .= ', '.$item->ID;
					}
				} else {
					if($name = $item->getAttribute('t')) {
						$display = $name;
					} else if($title = $item->getAttribute('title')) {
						$display = $title;
					} else {
						$display = $item->ID;
					}
				}
				//}
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
		$row = $db->GetRow('SELECT akCategoryHandle, max FROM atAttributeKeyCategoryItemsSettings WHERE akID = ?', $ak->getAttributeKeyID());
		$this->akCategoryHandle = $row['akCategoryHandle'];
		$this->set('akCategoryHandle', $this->akCategoryHandle);
		
		$this->max = $row['max'];	
		$this->set('max', $this->max);		
	}
	
	public function type_form() {
		$this->set('form', Loader::helper('form'));
		$this->load();
	}
	
	public function saveKey($data) {
		parent::saveKey($data);
		
		$ak = $this->getAttributeKey();
		$db = Loader::db();
		$db->Replace('atAttributeKeyCategoryItemsSettings', array(
			'akID' => $ak->getAttributeKeyID(), 
			'akCategoryHandle' => $data['akCategory'],
			'max' => $data['max']
		), array('akID'), true);
	}
	
	public function form() {
		$this->load();
		$html = Loader::helper('html');
		$form = Loader::helper('form');
		$this->akID = $this->attributeKey->getAttributeKeyID();
		//$searchInstance = $_REQUEST['akCategoryHandle'] . time();
		$searchInstance = preg_replace("/(\W)+/", '_', $this->field('value'));
		
		
		if (is_object($this->getAttributeValue())) {
			$IDs = $this->getIdArray();
		}

		$akcis = Loader::helper('form/attribute_key_category_item_selector');
		$akcis->addHeaderItems($this);			
		
		echo $form->hidden($this->field('akCategoryHandle'), $this->akCategoryHandle);
		echo $form->hidden($this->field('max'), $this->max);
		echo $akcis->selectItems($this->akCategoryHandle, $this->field('value').'[]', $IDs, 0, $searchInstance);
	}
	
	public function saveValue($value) {

		$this->load();
		$i = 0;
		$save = '';
		if(is_array($value)) {
			foreach($value as $ID) {
				if($this->max > 0 && $i >= $this->max) break;
				if(!empty($save)) {
					$save .= ', '.$ID;
				} else {
					$save = $ID;
				}
				$i++;			
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
		$akc = AttributeKeyCategory::getByHandle($data['akCategoryHandle']);
		if(!is_object($akc)) return t('%s category does not exist.', $this->akCategoryHandle);
		
		if(count($data['value'])) {
			return TRUE;
		}else{
			return NULL;
		}
	}
	
	public function deleteKey() {
		$db = Loader::db();
		$db->Execute('DELETE FROM atAttributeKeyCategoryItemsSettings WHERE akID = ?', array($this->attributeKey->getAttributeKeyID()));
	}
		
}