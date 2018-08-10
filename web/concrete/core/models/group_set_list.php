<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Model_GroupSetList extends DatabaseItemList {

	public function __construct() {
		$this->setQuery('select gsID from GroupSets');
		$this->sortBy('gsName', 'asc');
	}
	
	public function get($itemsToGet = 0, $offset = 0) {
		$r = parent::get($itemsToGet, $offset);
		$groupsets = array();
		foreach($r as $row) {
			$groupsets[] = GroupSet::getByID($row['gsID']);	
		}
		return $groupsets;
	}
}