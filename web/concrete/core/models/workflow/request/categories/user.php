<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Xu Lei <lei.xu@mainiotech.fi>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
abstract class Concrete5_Model_UserWorkflowRequest extends WorkflowRequest {
	
	public function setRequestedUserID($requestedUID) {
        $this->requestedUID = $requestedUID;
    }
	
 	public function getRequestedUserID() {
        return $this->requestedUID;
    }

	/**
	 * Gets the action of user workflow request. There are four actions:
	 * activate, register_activate, deactivate and delete
	 * 
	 * @return string
	 */		
	public function getRequestAction() {
        return $this->requestAction;
    }
	
	public function trigger() {
 		$user = User::getByUserID($this->requestedUID);
		$pk = PermissionKey::getByID($this->pkID);
        $pk->setPermissionObject($user);
		return parent::trigger($pk);
	}
	
	public function approve(WorkflowProgress $wp) {
		$wpr = new WorkflowProgressResponse();
		$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '/dashboard/users/search' . '?uID=' . $this->getRequestedUserID());
		return $wpr;
	}
	public function cancel(WorkflowProgress $wp) {
		$wpr = new WorkflowProgressResponse();
		$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '/dashboard/users/search' . '?uID=' . $this->getRequestedUserID() . '&workflow_canceled=1');
		return $wpr;
	}
	
	public function addWorkflowProgress(Workflow $wf) {
		Loader::model('workflow/progress/categories/user');	
		$uwp = UserWorkflowProgress::add($wf, $this);
		$r = $uwp->start();
		$uwp->setWorkflowProgressResponseObject($r);
		return $uwp;
	}
	
	/**
	 * Override the runTask method in order to launch the cancel function
	 * correctly (to trigger user deletion for instance)
	 */
	public function runTask($task, WorkflowProgress $wp) {
		$wpr = parent::runTask($task, $wp);
		if (!is_object($wpr) && method_exists($this, $task)) {
			if ($task == 'cancel') {
				// we check to see if any other outstanding workflowprogress requests have this id
				// if they don't we proceed
				$db = Loader::db();
				$num = $db->GetOne('select count(wpID) as total from WorkflowProgress where wpID <> ? and wrID = ? and wpIsCompleted = 0', array(
					$wp->getWorkflowProgressID(), $this->getWorkflowRequestID()
				));
				if ($num == 0) {
					$wpr = call_user_func_array(array($this, $task), array($wp));
					return $wpr;
				}
			}
		}
		return $wpr;
	}
}
