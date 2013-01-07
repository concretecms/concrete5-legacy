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
	
	protected $requestAction = 'activate';
	
	public function __construct() {
		$pk = PermissionKey::getByHandle('activate_user');
		parent::__construct($pk);
	}
	
	public function setRequestAction($action) {
        $this->requestAction = $action;
    }	

 	public function isActivationRequest() {
        return $this->requestAction == 'activate';
    }
	
 	public function isRegisterActivationRequest() {
        return $this->requestAction == 'register_activate';
    }
	
 	public function isDeactivationRequest() {
        return $this->requestAction == 'deactivate';
    }
	
	public function getWorkflowRequestDescriptionObject() {
		$d = new WorkflowDescription();
		$ui = UserInfo::getByID($this->getRequestedUserID());
		$d->setEmailDescription(t("User account \"%s\" has pending activation request which needs to be approved.", $ui->getUserName()));
		$d->setDescription(t("User %s Submitted for Approval.", $ui->getUserName()));
		$d->setInContextDescription(t("User Submitted for Approval."));
		$d->setShortStatus(t("Pending"));
		return $d;
	}
	
	public function approve(WorkflowProgress $wp) {
		$ui = UserInfo::getByID($this->getRequestedUserID());
		$wpr = parent::approve($wp);
		if ($this->isDeactivationRequest()) {
			$wpr->message = t("User %s has been deactivated.", $ui->getUserName());
			$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '/dashboard/users/search' . '?uID=' . $this->getRequestedUserID() . '&deactivated=1');
			$ui->deactivate();
		} else {
			$wpr->message = t("User %s has been activated.", $ui->getUserName());
			$wpr->setWorkflowProgressResponseURL(BASE_URL . DIR_REL . '/' . DISPATCHER_FILENAME . '/dashboard/users/search' . '?uID=' . $this->getRequestedUserID() . '&activated=1');
			$ui->activate();
		}
		return $wpr;
	}
	
	/**
	 * after caneling activate(register activate) request, do nothing
	 * 
	 * @return object
	 */
	public function cancel(WorkflowProgress $wp) {
		$wpr = parent::cancel($wp);
		if ($this->isDeactivationRequest()) {
			$wpr->message = t("User deactivation request has been cancelled.");
		} else {
			$wpr->message = t("User activation request has been cancelled.");
		}
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
		if ($this->isDeactivationRequest()) {
			return t('Deactivate User');
		} else {
			return t('Activate User');	
		}
		
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
		if ($this->isDeactivationRequest()) {
			return t("Deactivation");
		} else {
			return t("Activation");
		}
	}
}
