<?php
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @package Users
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 * @access private
 */
 
/** 
 * @access private
 */

class Concrete5_Model_GroupSearch extends DatabaseItemList {
	
	
	protected $itemsPerPage = 10;
	protected $minimumGroupID = REGISTERED_GROUP_ID;
	
	public function includeAllGroups() {
		$this->minimumGroupID = -1;
	}
	
	public function filterByKeywords($kw) {
		static $reverseLookup = array();
		$db = Loader::db();
		$locale = Localization::activeLocale();
		if(!array_key_exists($locale, $reverseLookup)) {
			$reverseLookup[$locale] = false;
			if((Localization::activeLocale() != 'en_US') || ENABLE_TRANSLATE_LOCALE_EN_US) {
				$limit = defined('GROUPNAME_REVERSELOOKUP_LIMIT') ? GROUPNAME_REVERSELOOKUP_LIMIT : 100;
				$count = $db->GetOne('select count(*) from Groups');
				if(($count > 0) && ($count <= $limit)) {
					$reverseLookup[$locale] = array();
					$rs = $db->Query('select gID, gName, gDescription from Groups');
					while($row = $rs->FetchRow()) {
						$reverseLookup[$locale][$row['gID']] = array('name' => tc('GroupName', $row['gName']), 'description' => tc('GroupDescription', $row['gDescription']));
					}
					$rs->Close();
				}
			}
		}
		if($reverseLookup[$locale]) {
			$foundIDs = array();
			foreach($reverseLookup[$locale] as $gID => $gTranslated) {
				if((stripos($gTranslated['name'], $kw) !== false) || (stripos($gTranslated['description'], $kw) !== false)) {
					$foundIDs[] = $gID;
				}
			}
			if(count($foundIDs)) {
				$this->filter(false, '(Groups.gID in (' . implode(', ', $foundIDs) . '))');
				return;
			}
		}
		$this->filter(false, "(Groups.gName like " . $db->qstr('%' . $kw . '%') . " or Groups.gDescription like " . $db->qstr('%' . $kw . '%') . ")");
	}
	
	public function filterByAllowedPermission($pk) {
		$assignment = $pk->getMyAssignment();
		$r = $assignment->getGroupsAllowedPermission();
		$gIDs = array('-1');
		if ($r == 'C') {
			$gIDs = array_merge($assignment->getGroupsAllowedArray(), $gIDs);
			$this->filter('gID', $gIDs, 'in');
		}
	}
	
	public function updateItemsPerPage( $num ) {
		$this->itemsPerPage = $num;
	}
	
	function __construct() {
		$this->setQuery("select Groups.gID, Groups.gName, Groups.gDescription from Groups");
		$this->sortBy('gName', 'asc');
	}
	
	public function getPage() {
		$this->filter('gID', $this->minimumGroupID, '>');
		return parent::getPage();
	}
}