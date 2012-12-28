<?
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @package Workflow
 * @author Xu Lei <lei.xu@mainiotech.fi>
 * @copyright  Copyright (c) 2003-2012 concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 
class Concrete5_Model_DeleteUserUserWorkflowRequest extends UserWorkflowRequest {
	
	public function __construct() {
		$pk = PermissionKey::getByHandle('delete_user');
		parent::__construct($pk);
	}
	
	public function getWorkflowRequestDescriptionObject() {
		$d = new WorkflowDescription();
		$ui = UserInfo::getByID($this->getRequestedUserID());
		$d->setEmailDescription(t("User account \"%s\" has pending deletion request and needs to be approved.", $ui->getUserName()));
		$d->setShortStatus(t("User deletion Request"));
		return $d;
	}
	
	public function approve(WorkflowProgress $wp) {
		$ui = UserInfo::getByID($this->getRequestedUserID());
		$ui->delete();
		$wpr = new WorkflowProgressResponse();
		$wpr->message = t("User %s has been deleted.", $ui->getUserName());
		$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '?uID=' . $this->getRequestedUserID());
		return $wpr;
	}
	
	public function cancel(WorkflowProgress $wp) {
		$ui = UserInfo::getByID($this->getRequestedUserID());
		
		if (!$ui->isActive()) {
			$pkr = new ActivateUserUserWorkflowRequest();
			$pkr->setRequestedUserID($this->uID);
			$pkr->trigger();
		}
		
		$wpr = parent::cancel($wp);
		$wpr->message = t("User deletion request has been cancelled.");
		return $wpr;
	}

	public function getWorkflowRequestStyleClass() {
		return 'info';
	}

	public function getWorkflowRequestApproveButtonClass() {
		return 'success';
	}

	public function getWorkflowRequestApproveButtonInnerButtonRightHTML() {
		return '<i class="icon-white icon-thumbs-up"></i>';
	}

	public function getWorkflowRequestApproveButtonText() {
		return t('Delete User');
	}
	
	public function getWorkflowRequestAdditionalActions(WorkflowProgress $wp) {
		$buttons = array();
		$button = new WorkflowProgressAction();
		$button->setWorkflowProgressActionLabel(t('User Details'));
		$button->addWorkflowProgressActionButtonParameter('dialog-title', t('User Details'));
		$button->addWorkflowProgressActionButtonParameter('dialog-width', '420');
		$button->addWorkflowProgressActionButtonParameter('dialog-height', '310');
		$button->setWorkflowProgressActionStyleInnerButtonLeftHTML('<i class="icon-eye-open"></i>');
		$button->setWorkflowProgressActionURL(REL_DIR_FILES_TOOLS_REQUIRED . '/workflow/dialogs/user_details.php?uID=' . $this->getRequestedUserID());
		$button->setWorkflowProgressActionStyleClass('dialog-launch');
		$buttons[] = $button;
		return $buttons;
	}
}