<?php defined('C5_EXECUTE') or die("Access Denied.");

Loader::model('attribute_key_category_item_permission');
class AttributeKeyCategoryItemPermissionsHelper { 
	
	public function save($post) {
		$akcip = AttributeKeyCategoryItemPermission::get($post['akcipID'], $post['akCategoryHandle']);
		$akcip->clearAllPermissions();
		foreach($post['selectedEntity'] as $e) {
			if ($e != '') {
				$o1 = explode('[', $e);
				$type = $o1[0];
				$ID = str_replace(']', '', $o1[1]);
				$access = $post[$type][$ID];
				if ($type == 'uID') {
					$obj = UserInfo::getByID($ID);
				} else {
					$obj = Group::getByID($ID);					
				}
				foreach($access as $can) {
					if($can) { $set = TRUE; }
				}
				if(isset($set)) {
					$akcip->setAccess($obj, $access);
				}
			}
		}
	}

	public function getForm($item, $description = '', $disabled = FALSE) {
		
		if ($item instanceof AttributeKeyCategoryItemPermission) {
			$akcip = new AttributeKeyCategoryItemPermissionList();
			$akcip->add($item);
		} else {
			$akcip = $item;
		}

		$gl = new AttributeKeyCategoryItemGroupList($akcip);
		$ul = new AttributeKeyCategoryItemUserInfoList($akcip);
		$uArray = $ul->getUserInfoList();
		$gArray = $gl->getGroupList();
		
		$akcips = $akcip->getAttributeKeyCategoryItemPermissions();
		$html = '';
		
		if(!$disabled) {
			foreach($akcips as $_tp) {
				$html .= '<input type="hidden" name="akcipID" value="' . $_tp->ID . '" />';
			}
		
			$html .= '<a href="' . REL_DIR_FILES_TOOLS_REQUIRED . '/user_group_selector" id="ug-selector" dialog-modal="false" dialog-width="90%" dialog-title="' . t('Choose User/Group') . '"  dialog-height="70%" class="ccm-button-right dialog-launch"><span><em>' . t('Add Group or User') . '</em></span></a>';
			$html .= '<p>' . $description . '</p>';
			$html .= '<div class="ccm-spacer">&nbsp;</div><br/>';
		} else {
			$html .= '<p>' . $description . '</p>';
			$html .= '<div class="ccm-spacer">&nbsp;</div><br/>';
		}
		
		$html .= '<div id="ccm-permissions-entities-wrapper" class="ccm-permissions-entities-wrapper">';
		
		if(!$disabled) {
			$html .= '<div id="ccm-permissions-entity-base" class="ccm-permissions-entity-base">' . $this->getAccessRow($akcip) . '</div>';
		}

		foreach($gArray as $g) { 
			$html .= $this->getAccessRow($akcip, $g, $disabled);
		}
		
		foreach($uArray as $ui) {
			$html .= $this->getAccessRow($akcip, $ui, $disabled);
		}
		
		
		$html .= '</div>';
		
		return $html;
	}
	public function getInheritanceForm($item, $description = '', $disabled = FALSE) {
		
		if ($item instanceof AttributeKeyCategoryItemPermission) {
			$akcip = new AttributeKeyCategoryItemPermissionList();
			$akcip->add($item);
		} else {
			$akcip = $item;
		}

		$gl = new AttributeKeyCategoryItemGroupList($akcip);
		$ul = new AttributeKeyCategoryItemUserInfoList($akcip);
		$uArray = $ul->getUserInfoList();
		$gArray = $gl->getGroupList();
		
		$akcips = $akcip->getAttributeKeyCategoryItemPermissions();
		$html = '';
		
		if(!$disabled) {
			foreach($akcips as $_tp) {
				$html .= '<input type="hidden" name="akcipID" value="' . $_tp->ID . '" />';
			}
		
			$html .= '<a href="' . REL_DIR_FILES_TOOLS_REQUIRED . '/user_group_selector" id="ug-selector" dialog-modal="false" dialog-width="90%" dialog-title="' . t('Choose User/Group') . '"  dialog-height="70%" class="ccm-button-right dialog-launch"><span><em>' . t('Add Group or User') . '</em></span></a>';
			$html .= '<p>' . $description . '</p>';
			$html .= '<div class="ccm-spacer">&nbsp;</div><br/>';
		} else {
			$html .= '<p>' . $description . '</p>';
		}
		
		$html .= '<div id="ccm-permissions-entities-wrapper" class="ccm-permissions-entities-wrapper">';
		
		if(!$disabled) {
			$html .= '<div id="ccm-permissions-entity-base" class="ccm-permissions-entity-base">' . $this->getInheritanceAccessRow($akcip) . '</div>';
		}

		foreach($gArray as $g) { 
			$html .= $this->getInheritanceAccessRow($akcip, $g, $disabled);
		}
		
		foreach($uArray as $ui) {
			$html .= $this->getInheritanceAccessRow($akcip, $ui, $disabled);
		}
		
		
		$html .= '</div>';
		
		return $html;
	}
	public function getInheritance($item, $description = '') {
		
		if ($item instanceof AttributeKeyCategoryItemPermission) {
			$akcip = new AttributeKeyCategoryItemPermissionList();
			$akcip->add($item);
		} else {
			$akcip = $item;
		}

		$gl = new AttributeKeyCategoryItemGroupList($akcip);
		$ul = new AttributeKeyCategoryItemUserInfoList($akcip);
		$uArray = $ul->getUserInfoList();
		$gArray = $gl->getGroupList();
		
		$akcips = $akcip->getAttributeKeyCategoryItemPermissions();
		$html = '';
		
		$html .= '<div id="ccm-permissions-entities-wrapper" class="ccm-permissions-entities-wrapper">';

		foreach($gArray as $g) { 
			$html .= $this->getInheritanceAccessRow($akcip, $g, TRUE);
		}
		
		foreach($uArray as $ui) {
			$html .= $this->getInheritanceAccessRow($akcip, $ui, TRUE);
		}
		
		
		$html .= '</div>';
		
		return $html;
	}
	
	public function getAccessRow($akcips, $obj = FALSE, $disabled = FALSE) {		
		$form = Loader::helper('form');
		$html = '<div class="ccm-sitemap-permissions-entity">';

		if ($obj != false) {
			if (is_a($obj, 'Group')) {
				$identifier = 'gID[' . $obj->getGroupID().']';
				$name = $obj->getGroupName();
			} else if (is_a($obj, 'UserInfo')) {
				$identifier = 'uID[' . $obj->getUserID().']';
				$name = $obj->getUserName();
			}
		}
		
		$akcip = new AttributeKeyCategoryItemPermission();
		if(!$akcip->canAdmin()) {	return ''; }
		
		$html .= $form->hidden('selectedEntity[]', $identifier);
		
		$html .= '<h2>';
		if (($identifier != 'gID[1]' && $identifier != 'gID[2]')) {
			$html .= '<a href="javascript:void(0)" class="ccm-permissions-remove"><img src="' . ASSETS_URL_IMAGES . '/icons/remove.png" width="16" height="16" /></a>';
		}
		$html .= '<span>' . $name . '</span></h2>';

		
		$html .= '<table border="0" cellspacing="0" cellpadding="0" id="ccm-sitemap-permissions-grid">';
		$tasks = $akcips->getAttributeKeyCategoryItemPermissions();
		
		foreach($tasks as $akcip) {			
			if ($identifier != '') {
				$id = $identifier;
			}
			if (is_a($obj, 'Group')) {
				$access = $akcip->gIDs[$obj->getGroupID()];
			} else if (is_a($obj, 'UserInfo')) {
				$access = $akcip->uIDs[$obj->getUserID()];
			}
			
			$permissions = array(
						'read' => 'View',
						'write' => 'Edit',
						'delete' => 'Delete',
						'add' => 'Add',
						'search' => 'Search',
						'admin' => 'Administrate',
					);
			$skipGuest = array('write', 'delete', 'add', 'admin');
			$skipRegistered = array('delete', 'admin');
			
			foreach($permissions as $handle => $displayName) {
				if ($identifier == 'gID[1]') {
					if(!in_array($handle, $skipGuest)) {
						$html .= self::getAccessItem($id, $handle, $access[$handle], $displayName, $disabled);
					}
				} elseif ($identifier == 'gID[2]') {
					if(!in_array($handle, $skipRegistered)) {
						$html .= self::getAccessItem($id, $handle, $access[$handle], $displayName, $disabled);
					}
				} else {
					$html .= self::getAccessItem($id, $handle, $access[$handle], $displayName, $disabled);
				}
			}
		}
		
		$html .= '</table></div><br/>';
		return $html;
	}
	
	public function getInheritanceAccessRow($akcips, $obj = FALSE, $disabled = FALSE) {		
		$form = Loader::helper('form');
		$html = '<div class="ccm-sitemap-permissions-entity">';

		if ($obj != false) {
			if (is_a($obj, 'Group')) {
				$identifier = 'gID[' . $obj->getGroupID().']';
				$name = $obj->getGroupName();
			} else if (is_a($obj, 'UserInfo')) {
				$identifier = 'uID[' . $obj->getUserID().']';
				$name = $obj->getUserName();
			}
		}
		
		$akcip = new AttributeKeyCategoryItemPermission();
		if(!$akcip->canAdmin()) {	return ''; }
		
		if(!$disabled) {
			$html .= $form->hidden('selectedEntity[]', $identifier);
		}
		
		$html .= '<h2>';
		if (($identifier != 'gID[1]' && $identifier != 'gID[2]')) {
			$html .= '<a href="javascript:void(0)" class="ccm-permissions-remove"><img src="' . ASSETS_URL_IMAGES . '/icons/remove.png" width="16" height="16" /></a>';
		}
		$html .= '<span>' . $name . '</span></h2>';

		
		$html .= '<table border="0" cellspacing="0" cellpadding="0" id="ccm-sitemap-permissions-grid">';
		$tasks = $akcips->getAttributeKeyCategoryItemPermissions();
		
		foreach($tasks as $akcip) {			
			if ($identifier != '') {
				$id = $identifier;
			}
			if (is_a($obj, 'Group')) {
				$access = $akcip->gIDs[$obj->getGroupID()];
			} else if (is_a($obj, 'UserInfo')) {
				$access = $akcip->uIDs[$obj->getUserID()];
			}
			
			$permissions = array(
						'read' => 'View',
						'write' => 'Edit',
						'delete' => 'Delete',
						'add' => 'Add',
						'search' => 'Search',
						'admin' => 'Administrate',
					);
			$skipGuest = array('write', 'delete', 'add', 'admin');
			$skipRegistered = array('delete', 'admin');
			
			foreach($permissions as $handle => $displayName) {
				if ($identifier == 'gID[1]') {
					if(!in_array($handle, $skipGuest)) {
						$html .= self::getInheritanceAccessItem($id, $handle, $access[$handle], $displayName, $disabled);
					}
				} elseif ($identifier == 'gID[2]') {
					if(!in_array($handle, $skipRegistered)) {
						$html .= self::getInheritanceAccessItem($id, $handle, $access[$handle], $displayName, $disabled);
					}
				} else {
					$html .= self::getInheritanceAccessItem($id, $handle, $access[$handle], $displayName, $disabled);
				}
			}
		}
		
		$html .= '</table></div><br/>';
		return $html;
	}
	
	public function getAccessItem($id, $handle, $access, $displayName = NULL, $disabled = FALSE) {
		if($disabled) {
			$disabled = array('disabled' => 'disabled');
		}
		$form = Loader::helper('form');
		if(!$displayName) {
			$displayName =  ucwords($handle);
		}
		return '<tr class="ccm-permissions-access">
				<th>' . $displayName . '</th>
				<td>' . $form->radio($id . '['.$handle.']', 0, $access, $disabled) . ' ' . t('Inherit') . '</td>
				<td>' . $form->radio($id .'['.$handle.']', '1', $access, $disabled) . ' ' . t('Yes') . '</td>
				<td>' . $form->radio($id . '['.$handle.']', '2', $access, $disabled) . ' ' . t('No') . '</td>
			</tr>';
	}
	public function getInheritanceAccessItem($id, $handle, $access, $displayName = NULL, $disabled = FALSE) {
		if($disabled) {
			$disabled = array('disabled' => 'disabled');
			$handle = $id .'['.$handle.'][inherited]';
		} else {
			$handle = $id .'['.$handle.']';
		}
		$form = Loader::helper('form');
		if(!$displayName) {
			$displayName =  ucwords($handle);
		}
		return '<tr class="ccm-permissions-access">
				<th>' . $displayName . '</th>
				<td>' . $form->radio($handle, '1', $access, $disabled) . ' ' . t('Yes') . '</td>
				<td>' . $form->radio($handle, '2', $access, $disabled) . ' ' . t('No') . '</td>
			</tr>';
	}
}
