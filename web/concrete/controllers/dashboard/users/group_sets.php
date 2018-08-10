<?php
defined('C5_EXECUTE') or die("Access Denied.");

class DashboardUsersGroupSetsController extends Concrete5_Controller_Dashboard_Users_Group_Sets {
	public function add_set() {
		if ($this->token->validate('add_set')) { 
			if (!trim($this->post('gsName'))) { 
				$this->error->add(t("Specify a name for your group set."));
			}
			$gsName = trim($this->post('gsName'));
			if (!Loader::helper('validation/strings')->multiLingualName($gsName, true)) {
				$this->error->add(t('Set Names must only include alphanumerics and spaces.'));
			}
			if (!$this->error->has()) {
				$gs = GroupSet::add($gsName);
				if (is_array($_POST['gID'])) {
					foreach($_POST['gID'] as $gID) {
						$g = Group::getByID($gID);
						if(is_object($g)) {
							$gs->addGroup($g);
						}
					}					
				}
				$this->redirect('dashboard/users/group_sets', 'set_added');
			}
			
		} else {
			$this->error->add($this->token->getErrorMessage());
		}
	}

	public function update_set() {
		$this->edit($this->post('gsID'));
		if ($this->token->validate('update_set')) { 
			$as = GroupSet::getByID($this->post('gsID'));
			if (!is_object($as)) {
				$this->error->add(t('Invalid group set.'));
			} else {
				if (!trim($this->post('gsName'))) { 
					$this->error->add(t("Specify a name for your group set."));
				}
			}
			
			$gsName = trim($this->post('gsName'));
			if (!Loader::helper('validation/strings')->multiLingualName($gsName, true)) {
				$this->error->add(t('Set Names must only include alphanumerics and spaces.'));
			}
			if (!$this->error->has()) {
				$as->updateGroupSetName($gsName);
				$this->redirect('dashboard/users/group_sets', 'set_updated');
			}
			
		} else {
			$this->error->add($this->token->getErrorMessage());
		}
	}

}