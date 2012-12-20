<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Xu Lei <lei.xu@mainiotech.fi>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 
class Concrete5_Model_ActivateUserUserWorkflowRequest extends UserWorkflowRequest {
	
	public function __construct() {
		$pk = PermissionKey::getByHandle('activate_user');
		parent::__construct($pk);
	}
	
	public function getWorkflowRequestDescriptionObject() {
		$d = new WorkflowDescription();
		$d->setEmailDescription(t("User has pending permission changes."));
		$d->setInContextDescription(t("User Submitted for Permission Changes."));
		$d->setShortStatus(t("Permission Changes"));
		return $d;
	}
	
	public function approve(WorkflowProgress $wp) {
		$ui = UserInfo::getByID($this->getRequestedUserID());
		$ui->activate();
		$wpr = new WorkflowProgressResponse();
		$wpr->message = t("User was activated.");
		$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?uID=' . $this->getRequestedUserID());
		return $wpr;
	}
	
	public function cancel(WorkflowProgress $wp) {
		$wpr = parent::cancel($wp);
		$wpr->message = t("User was not activated.");
		$ui = UserInfo::getByID($this->getRequestedUserID());
		$ui->delete();
		return $wpr;
	}

	public function getWorkflowRequestStyleClass(){
		return 'info';
	}

	public function getWorkflowRequestApproveButtonClass(){
		return 'success';
	}

	public function getWorkflowRequestApproveButtonInnerButtonRightHTML(){
		return '<i class="icon-white icon-thumbs-up"></i>';
	}

	public function getWorkflowRequestApproveButtonText(){
		return t('Approve User');
	}

}