<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Pages_Types_Usage extends Controller {

	protected $ct = false;
	
	protected function verify($ctID = false) {
		$ct = false;
		if ($ctID > 0) {
			$ct = CollectionType::getByID($ctID);
		}
		if(is_object($ct)) {
			$this->set('ct', $ct);
			$this->ct = $ct;
		}
		else {
			$this->redirect("/dashboard/pages/types");
		}
	}
	
	public function view($ctID = false, $action = false) { 
		$this->verify($ctID);
		$db = Loader::db();
		$pageVersions = array();
		foreach($db->GetAll('SELECT Pages.cID,CollectionVersions.cvID FROM Pages INNER JOIN CollectionVersions ON Pages.cID = CollectionVersions.cID WHERE Pages.cIsTemplate = 0 and CollectionVersions.ctID = ? ORDER BY Pages.cID,CollectionVersions.cvID',array($ctID)) as $row) {
			$row['Page'] = Page::getByID($row['cID'], $row['cvID']);
			$pageVersions[] = $row;
		}
		$this->set('pageVersions', $pageVersions);
	}	
	
}