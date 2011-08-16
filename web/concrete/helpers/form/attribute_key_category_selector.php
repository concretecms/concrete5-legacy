<?php
defined('C5_EXECUTE') or die("Access Denied.");
class FormAttributeKeyCategorySelectorHelper {
	
	public function select($akCategoryHandle, $name = 'akCategory') {
		$txt = Loader::helper('text');
		$list = AttributeKeyCategory::getList();
		$pkgs = Package::getInstalledList();
		
		$optGroups['core']['name'] = 'Core';
		$optGroups[0]['name'] = 'Custom';
		foreach($pkgs as $pkg) {
			$optGroups[$pkg->getPackageID()]['name'] = $pkg->getPackageName();
		}
		
		foreach($list as $akc) {
			if($akc->getPackageID() === NULL) {
				$optGroups['core']['option'][] = $akc;
			} else {
				$optGroups[$akc->getPackageID()]['option'][] = $akc;
			}
		}
		$html = '<select name="'.$name.'">';
		
		foreach($optGroups as $group) {
			if(is_array($group['option'])) {
				$html .= '<optgroup label="'.$group['name'].'">';
				foreach($group['option'] as $cat) {
					$html .= '<option';
					if($akCategoryHandle == $cat->getAttributeKeyCategoryHandle()) $html .= ' selected';
					$html .= ' value="'.$cat->getAttributeKeyCategoryHandle().'">'.$txt->unhandle($cat->getAttributeKeyCategoryHandle()).'</option>';
				}
				$html .= '</optgroup>';
			}
		}
		$html .= '</select>';
		
		return $html;
	}
	
} ?>