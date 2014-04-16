<?php
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardWorkflowListController extends Concrete5_Controller_Dashboard_Workflow_List {
	public function save_workflow_details() {
		if (!Loader::helper('validation/token')->validate('save_workflow_details')) {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
		$wfName = trim($this->post('wfName'));
		if (!$wfName) { 
			$this->error->add(t('You must give the workflow a name.'));
		}
		if (!Loader::helper('validation/strings')->multiLingualName($wfName, true)) {
			$this->error->add(t('Workflow Names must only include alphanumerics and spaces.'));
		}
		if (!$this->error->has()) {
			$wf = Workflow::getByID($this->post('wfID'));
			$wf->updateName($wfName);
			$wf->updateDetails($this->post());
			$this->redirect('/dashboard/workflow/list', 'view_detail', $this->post('wfID'), 'workflow_updated');
		} else {
			$this->view_detail($this->post('wfID'));
		}		
	}
	
	public function submit_add() {
		if (!Loader::helper('validation/token')->validate('add_workflow')) {
			$this->error->add(Loader::helper('validation/token')->getErrorMessage());
		}
		$wfName = trim($this->post('wfName'));
		if (!$wfName) { 
			$this->error->add(t('You must give the workflow a name.'));
		}
		if (!Loader::helper('validation/strings')->multiLingualName($wfName, true)) {
			$this->error->add(t('Workflow Names must only include alphanumerics and spaces.'));
		}
		$db = Loader::db();
		$wfID = $db->getOne('SELECT wfID FROM Workflows WHERE wfName=?',array($wfName));
		if ($wfID) {
			$this->error->add(t('Workflow with that name already exists.'));
		}
		if (!$this->error->has()) { 
			$type = WorkflowType::getByID($this->post('wftID'));
			if (!is_object($type) || !($type instanceof WorkflowType)) {
				$this->error->add(t('Invalid Workflow Type.'));
				$this->add();
				return;
			}
			$wf = Workflow::add($type, $wfName);
			$wf->updateDetails($this->post());
			$this->redirect('/dashboard/workflow/list/', 'view_detail', $wf->getWorkflowID(), 'workflow_created');
		}
		$this->add();
	}

	
}