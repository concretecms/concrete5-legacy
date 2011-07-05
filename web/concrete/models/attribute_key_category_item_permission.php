<?php 

class AttributeKeyCategoryItemPermissionList extends Object {
	
	protected $permissions = array();
	
	public function add($vtp) {
		$this->permissions[] = $vtp;
	}
	
	public function getAttributeKeyCategoryItemPermissionIDs() {
		$vtps = array();
		foreach($this->permissions as $vtp) {
			$vtps[] = $vtp->ID;
		}
		return $vtps;
	}
	
	public function getAttributeKeyCategoryItemPermissions() {return $this->permissions;}
	
}

class AttributeKeyCategoryItemPermission extends Object {
	
	public function getPermissionTypes($columnNames = FALSE) {
		$db = Loader::db();
		foreach($db->MetaColumns('AttributeKeyCategoryItemPermissions') as $col) {
			if (substr($col->name, 0, 3) == 'can') {
				if($columnNames) {
					$permArray[] = $col->name;
				} else {
					$permArray[] = strtolower(substr($col->name, 3));
				}
			}
		}
				
		return $permArray;
	}

	public static function getByID($ID, $recursiveCheck = array()) {
		if(in_array($ID, $recursiveCheck) || in_array('default', $recursiveCheck)) {
			return;
		}
		$db = Loader::db();
		$properties['ID'] = $ID;
		$rows = $db->GetArray("SELECT * FROM AttributeKeyCategoryItemPermissions WHERE ID = ?", array($ID));
		foreach($rows as $row) {
			if($row['uID'] != NULL) {
				$properties['uIDs'][$row['uID']]['read'] = $row['canRead'];
				$properties['uIDs'][$row['uID']]['write'] = $row['canWrite'];
				$properties['uIDs'][$row['uID']]['delete'] = $row['canDelete'];
				$properties['uIDs'][$row['uID']]['add'] = $row['canAdd'];
				$properties['uIDs'][$row['uID']]['search'] = $row['canSearch'];
				$properties['uIDs'][$row['uID']]['admin'] = $row['canAdmin'];
			}
			if($row['gID'] != NULL) {
				$properties['gIDs'][$row['gID']]['read'] = $row['canRead'];
				$properties['gIDs'][$row['gID']]['write'] = $row['canWrite'];
				$properties['gIDs'][$row['gID']]['delete'] = $row['canDelete'];
				$properties['gIDs'][$row['gID']]['add'] = $row['canAdd'];
				$properties['gIDs'][$row['gID']]['search'] = $row['canSearch'];
				$properties['gIDs'][$row['gID']]['admin'] = $row['canAdmin'];
			}
		}
		$rows = $db->GetArray("SELECT akCategoryHandle FROM AttributeKeyCategoryItems WHERE ID = ?", array($ID));
		foreach($rows as $row) {
			if($row['akCategoryHandle'] != NULL) {
				$properties['akCategoryHandle'] = $row['akCategoryHandle'];
			}
			
		}
		
		$vtp = new AttributeKeyCategoryItemPermission();
		$vtp->setPropertiesFromArray($properties);
		return $vtp;
	}

	public function clearPermissions($who) {
		$db = Loader::db();
		if (is_a($who, 'UserInfo')) {
			$db->Execute('DELETE FROM AttributeKeyCategoryItemPermissions WHERE uID = ? AND ID = ?', array($who->getUserID(), $this->ID));
			unset($this->uIDs[$who->getUserID()]);
		} elseif(is_a($who, 'Group')) {
			$db->Execute('DELETE FROM AttributeKeyCategoryItemPermissions WHERE gID = ? AND ID = ?', array($who->getGroupID(), $this->ID));
			unset($this->gIDs[$who->getGroupID()]);
		} else {
			$db->Execute('DELETE FROM AttributeKeyCategoryItemPermissions WHERE gID = 0 AND uID = 0 AND ID = ?', array($this->ID));
		}
	}
	public function clearAllPermissions() {
		$db = Loader::db();
		$db->Execute('DELETE FROM AttributeKeyCategoryItemPermissions WHERE ID = ?', $this->ID);
		unset($this->uIDs);
		unset($this->gIDs);
	}
	
	public function inheritAccess($who, $inherited) {
		$this->setAccess($who, $inherited, 0);			
	}
	
	public function addAccess($who, $can) {
		$this->setAccess($who, $can, 1);
	}
	
	public function removeAccess($who, $canNot) {
		$this->setAccess($who, $canNot, 2);
	}
	
	public function setAccess($who, $can, $setting = NULL) {
		if(is_array($can)) {
			foreach($can as $access => $value) {
				$this->setAccess($who, $access, $value);
			}
		} else {
			if(is_numeric($this->ID)) {
				Loader::model('attribute_key_category_item');
				$item = AttributeKeyCategoryItem::getByID($this->ID);
				$akCategoryHandle = $item->akCategoryHandle;
			}
			
			foreach(self::getPermissionTypes() as $column) {
				if($column != $can) { 
					$keyColumns[$column] = '';
				}
			}
			if(is_numeric($this->ID)) {
				$values['ID'] = (int)$this->ID;
			} else {
				$values['ID'] = $this->ID;
			}
			$values['akCategoryHandle'] = $akCategoryHandle;
			
			switch(get_class($who)) {
				case 'UserInfo':
					if($this->uIDs[$values['uID']][$can] == $setting) {
						return;
					}
					$values['uID'] = (int)$who->getUserID();
					foreach($keyColumns as $key => $value) {
						$value = $this->uIDs[$values['uID']][$key];
						if(empty($value)) {
							$value = 0;
						}
						$values['can'.ucwords($key)] = (int)$value;
					}
					$this->uIDs[$values['uID']][$can] = (int)$setting;
				break;
				case 'Group':
					if($this->gIDs[$values['gID']][$can] == $setting) {
						return;
					}
					$values['gID'] = (int)$who->getGroupID();
					foreach($keyColumns as $key => $value) {
						$value = $this->gIDs[$values['gID']][$key];
						if(empty($value)) {
							$value = 0;
						}
						$values['can'.ucwords($key)] = (int)$value;
					}
					$this->gIDs[$values['gID']][$can] = (int)$setting;
				break;
			}
			unset($keyColumns);
			foreach($values as $key => $value) {
				$keyColumns[] = $key;
				if($key != 'akCategoryHandle') {
					$query .= " AND ".$key." = '".$value."'";
				}
			}
			$values['can'.ucwords($can)] = (int)$setting;
			foreach($values as $key => $value) {
				if($insert) {
					$insert .= ",".$key;
				} else {
					$insert = $key;
				}
			}
			$db = Loader::db();
			if(!empty($akCategoryHandle)) {
				$akCategoryHandle = "akCategoryHandle = '".$akCategoryHandle."'";
			} else {
				$akCategoryHandle = "akCategoryHandle IS NULL";
			}
			foreach($values as $null) {
				if($args) {
					$args .= ",?";
				} else {
					$args = "?";
				}
			}
			$where = $akCategoryHandle.$query;
			$exists = $db->GetOne("SELECT COUNT(*) FROM AttributeKeyCategoryItemPermissions WHERE {$where}");
			$set = 'can'.ucwords($can).' = '.$setting;
			if($exists > 0) {
				$db->Execute("UPDATE AttributeKeyCategoryItemPermissions SET {$set} WHERE {$where}");
			} else {
				$db->Execute("INSERT INTO AttributeKeyCategoryItemPermissions ({$insert}) VALUES ({$args})", $values);
			}
		}
	}
	
	public function can($access, $u = NULL) {
		// check against logged in user
		if($u[0] instanceof User) {
			$u = $u[0];
		} else {
			$u = new User();
		}
		if ($u->isSuperUser()) {
			return TRUE;
		}
		
		$db = Loader::db();
		
		$groups = $u->getUserGroups();
		$groupIDs = array();
		foreach($groups as $key => $value) {
			$groupIDs[] = $key;
		}
		
		$uID = -1;
		if ($u->isRegistered()) {
			$uID = $u->getUserID();
		}
		
		$row = $db->GetOne("SELECT uID FROM AttributeKeyCategoryItems WHERE ID = ?", array($ID));
		if($row['uID'] == $uID) {
			return TRUE;
		}
		
		if($this->uIDs[$uID][$access] == '1') {
			return TRUE;
		} elseif (!empty($groupIDs)) {
			foreach($groupIDs as $gID) {
				if($this->gIDs[$gID][$access] == '1') {
					return TRUE;
				}
			}
		} else {
			return FALSE;
		}
	}
	
	public function __call($nm, $a) {
		if (substr($nm, 0, 3) == 'can') {
			$txt = Loader::helper('text');
			$permission = $txt->uncamelcase(substr($nm, 3));
			if ($this->can($permission, $a)) {
				return TRUE;
			} else {
				return FALSE;
			}
		}
	}

}

class AttributeKeyCategoryItemGroupList extends GroupList {
	
	function AttributeKeyCategoryItemGroupList($obj, $omitRequiredGroups = false, $getAllGroups = false) {
		if ($getAllGroups) {
			$db = Loader::db();
			$minGID = ($omitRequiredGroups) ? 2 : 0;
			$q = "select gID from Groups where gID > $minGID order by gID asc";	
			$r = $db->Execute($q);
			while ($row = $r->FetchRow()) {
				$g = Group::getByID($row['gID']);
				$g->setPermissionsForObject($obj);
				if(!in_array($g,$this->gArray)) 
					$this->gArray[] = $g;
			}
		} else {
			$groups = $this->getRelevantGroups($obj, $omitRequiredGroups);
			foreach($groups as $g) {
				if(!$g) continue;
				$g->setPermissionsForObject($obj);
				if(!in_array($g,$this->gArray)) 
					$this->gArray[] = $g;
			}
		}
	}
	
	function getGroupList() {
		return $this->gArray;
	}
	
	private function getRelevantGroups($obj, $omitRequiredGroups = false) {
		$db = Loader::db();
		
		$vtpis = $obj->getAttributeKeyCategoryItemPermissionIDs();
		$table = 'AttributeKeyCategoryItemPermissions';
		$where = "ID = '" . implode(',', $vtpis) . "'";

		$groups = array();
		if ($where) {
			$q = "select gID from $table where {$where} order by gID asc";
			$gs = $db->GetCol($q);

			if (!$omitRequiredGroups) {
				if (!in_array(GUEST_GROUP_ID, $gs)) {
					$gs[] = GUEST_GROUP_ID;
				}
				if (!in_array(REGISTERED_GROUP_ID, $gs)) {
					$gs[] = REGISTERED_GROUP_ID;
				}
			}
			
			sort($gs);

			foreach($gs as $gID) {
				$g = Group::getByID( $gID );
				$groups[] = $g;
			}
		}
		return $groups;
	}
		
}

class AttributeKeyCategoryItemUserInfoList extends Object {

   // obtains relevant users for the passed object. Since users can now
   // act just like groups (permissions-wise)

   var $uiArray = array();

	function AttributeKeyCategoryItemUserInfoList($obj) {
		$db = Loader::db();
		
		$vtpis = $obj->getAttributeKeyCategoryItemPermissionIDs();
		$q = "select uID from AttributeKeyCategoryItemPermissions where ID = '" . implode(',', $vtpis) . "'";
		$r = $db->Execute($q);
		while ($row = $r->FetchRow()) {
			$userPermissionsArray['permissions'] = $row;
			$ui = UserInfo::getByID($row['uID'], $userPermissionsArray);
			if( !$ui || !method_exists($ui,'getUserID') ) continue;
			$this->uiArray[]=$ui;
		}

		return $this;

	}
	
	function getUserInfoList() {
		return $this->uiArray;
	}
	
}
