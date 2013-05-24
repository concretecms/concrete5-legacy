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
	
	protected $requestAction = 'delete';
	
	public function __construct() {
		$pk = PermissionKey::getByHandle('delete_user');
		parent::__construct($pk);
	}
	
	public function getWorkflowRequestDescriptionObject() {
		$d = new WorkflowDescription();
		$ui = UserInfo::getByID($this->getRequestedUserID());
		$d->setEmailDescription(t("User account \"%s\" has been marked for deletion. The deletion request needs to be approved.", $ui->getUserName()));
		$d->setDescription(t("User %s Submitted for Deletion.", $ui->getUserName()));
		$d->setInContextDescription(t("User Submitted for Deletion."));
		$d->setShortStatus(t("Pending"));
		return $d;
	}
	
	public function approve(WorkflowProgress $wp) {
		$ui = UserInfo::getByID($this->getRequestedUserID());
		$ui->delete();
		$wpr = parent::cancel($wp);
		$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '/dashboard/users/search?deleted=1&username='.urlencode($ui->getUserName()));
		$wpr->message = t("User %s has been deleted.", $ui->getUserName());
		return $wpr;
	}
	
	/**
	 * After canceling delete request, do nothing
	 */
	public function cancel(WorkflowProgress $wp) {
		$ui = UserInfo::getByID($this->getRequestedUserID());
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
	
	/**
	 * Gets the translated text of action of user workflow request
	 * 
	 * @return string
	 */
	public function getRequestActionText() {
		return t("Deletion");
	}
}
