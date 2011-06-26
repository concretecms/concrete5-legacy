<?php
defined('C5_EXECUTE') or die("Access Denied.");

class DashboardUsersGroupsController extends Controller {
	
	public function edit($gID = 0) {
		$error = Loader::helper('validation/error');
		$group = Group::getByID($gID);
		if(is_object($group)) {
			$this->set('group', $group);
		} else {
			$this->redirect('dashboard/users/groups');
		}
	
	
	}
	
	public function add_group() {
		$error = Loader::helper('validation/error');
		$valt = Loader::helper('validation/token');
		if (!$valt->validate('add_group')) {
			$error->add($valt->getErrorMessage());
			$this->set('error', $error);
			return;
		}
		$group = Group::getByName(trim($this->post('gName')));
		if(is_object($group)) {
			$error->add(t('The group name %s is already in use.', trim($this->post('gName'))));
			$this->set('error', $error);
			return;
		} else if(strlen(trim($this->post('gName'))) == 0) {
			$error->add(t('Invalid group name'));
			$this->set('error', $error);
			return;
		} else {
			$g = Group::add($this->post(trim('gName')), $this->post(trim('gDescription')));
			$this->checkExpirationOptions($g);
			$this->set('message', t('Group successfully added.'));
		}
	}
	
	public function edit_group() {
		$error = Loader::helper('validation/error');
		$valt = Loader::helper('validation/token');
		if (!$valt->validate('edit_group')) {
			$error->add($valt->getErrorMessage());
			$this->set('error', $error);
			return;
		}
		$group = Group::getByID($this->post('gID'));
		if(is_object($group)) {
			if(strlen(trim($this->post('gName'))) == 0) {
				$error->add(t('Invalid group name'));
				$this->set('error', $error);
				return;
			}
			$group->update($this->post('gName'), $this->post('gDescription'));
			$this->checkExpirationOptions($group);
			$this->set('message', t("Group Updated."));
		} else {
			$error->add(t('Invalid group ID'));
			$this->set('error', $error);
			return;
		}
	}
	
	public function delete($delGroupId = 0, $token = ''){
		$error = Loader::helper('validation/error');
		$u=new User();
		try {
		
			if(!$u->isSuperUser()) {
				$error->add(t('You do not have permission to perform this action.'));
				$this->set('error', $error);
				return;
			}
			
			$group = Group::getByID($delGroupId);			
			
			if(!is_object($group)) {
				$error->add(t('Invalid group ID.'));
				$this->set('error', $error);
				return;
			}
			
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('delete_group_' . $delGroupId, $token)) {
				$error->add($valt->getErrorMessage());
				$this->set('error', $error);
				return;
			}
			
			$group->delete(); 
			$resultMsg = t('Group deleted successfully.');		
		
			$this->set('message', $resultMsg);
			$this->view(); 
		} catch(Exception $e) {
			$this->set('error', $e);
		}
	}
	
	private function checkExpirationOptions($g) {
		if(is_object($g)){
			if ($this->post('gUserExpirationIsEnabled')) {
				$date = Loader::helper('form/date_time');
				switch($this->post('gUserExpirationMethod')) {
					case 'SET_TIME':
						$g->setGroupExpirationByDateTime($date->translate('gUserExpirationSetDateTime'), $this->post('gUserExpirationAction'));
						break;
					case 'INTERVAL':
						$g->setGroupExpirationByInterval($this->post('gUserExpirationIntervalDays'), $this->post('gUserExpirationIntervalHours'), $this->post('gUserExpirationIntervalMinutes'), $this->post('gUserExpirationAction'));
						break;
				}
			} else {
				$g->removeGroupExpiration();
			}
		}
	}
}

?>