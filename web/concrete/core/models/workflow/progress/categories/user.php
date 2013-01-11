<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Xu Lei <lei.xu@mainiotech.fi>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class Concrete5_Model_UserWorkflowProgress extends WorkflowProgress {
	
	// Notice: here uID is requested user id, NOT requester user id
	protected $uID;
	
	public function loadDetails() {
		$db = Loader::db();
		$row = $db->GetRow('select uID from UserWorkflowProgress where wpID = ?', array($this->wpID));
		$this->setPropertiesFromArray($row);
	}
	
	public function delete() {
        parent::delete();
        $db = Loader::db();
        $db->Execute('delete from UserWorkflowProgress where wpID = ?', array($this->wpID));
    }
	
	public static function add(Workflow $wf, UserWorkflowRequest $wr) {
		$wp = parent::add('user', $wf, $wr);
		$db = Loader::db();
		$db->Replace('UserWorkflowProgress', array('uID' => $wr->getRequestedUserID(), 'wpID' => $wp->getWorkflowProgressID()), array('uID', 'wpID'), true);
		$wp->uID = $wr->getRequestedUserID();		
		return $wp;
	}
	
	public function getWorkflowProgressFormAction(){
		return REL_DIR_FILES_TOOLS_REQUIRED . '/' . DIRNAME_WORKFLOW . '/categories/user?task=save_user_workflow_progress&uID=' . $this->uID . '&wpID=' . $this->getWorkflowProgressID() . '&' . Loader::helper('validation/token')->getParameter('save_user_workflow_progress');
	}

	public function getPendingWorkflowProgressList() {
		$list = new UserWorkflowProgressList();
		$list->filter('wpApproved', 0);
		$list->sortBy('wpDateLastAction', 'desc');
		return $list;
	}
	
	public static function getList($requestedUID, $filters = array('wpIsCompleted' => 0), $sortBy = 'wpDateAdded asc') {
		$db = Loader::db();
		
		$filter = '';
		foreach($filters as $key => $value) {
			$filter .= ' and ' . $key . ' = ' . $value . ' ';
		}
		$filter .= ' order by ' . $sortBy;
		
		
		$r = $db->Execute('SELECT wp.wpID FROM UserWorkflowProgress uwp INNER JOIN WorkflowProgress wp ON wp.wpID = uwp.wpID WHERE uwp.uID = ? ' . $filter, $requestedUID);
		$list = array();
		while ($row = $r->FetchRow()) {
			$wp = UserWorkflowProgress::getByID($row['wpID']);
			if (is_object($wp)) {
				$list[] = $wp;
			}
		}
		return $list;
	}	
}

class Concrete5_Model_UserWorkflowProgressList extends UserList {
	
	protected $autoSortColumns = array('uName', 'wpDateLastAction', 'wpCurrentStatus');
	
	public function __construct() {
		$this->setQuery('SELECT DISTINCT u.uID, u.uName, wp.wpID FROM Users u INNER JOIN UserWorkflowProgress uwp ON uwp.uID = u.uID INNER JOIN WorkflowProgress wp ON wp.wpID = uwp.wpID');
		$this->filter('wpIsCompleted', 0);
	}
	
	public function get($itemsToGet = 0, $offset = 0) {
		$_users = DatabaseItemList::get($itemsToGet, $offset);
		$users = array();		
		foreach($_users as $row) {
			$u = User::getByUserID($row['uID']);
			$wp = UserWorkflowProgress::getByID($row['wpID']);
			$users[] = new Concrete5_Model_UserWorkflowProgressUser($u, $wp);
		}
		return $users;
	}
}

class Concrete5_Model_UserWorkflowProgressUser {

	public function __construct(User $u, WorkflowProgress $wp) {
		$this->user = $u;
		$this->wp = $wp;
	}
	
	public function getUserObject() {return $this->user;}

	public function getWorkflowProgressObject() {return $this->wp;}
}
  
	