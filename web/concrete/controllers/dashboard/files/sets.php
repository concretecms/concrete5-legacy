<?
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardFilesSetsController extends Concrete5_Controller_Dashboard_Files_Sets {
	public function file_sets_edit(){
		extract($this->getHelperObjects());
		Loader::model('file_set');
		//do my editing
		if (!$validation_token->validate("file_sets_edit")) {			
			$this->set('error', array($validation_token->getErrorMessage()));
			$this->view();
			return;
		}
		
		if(!$this->post('fsID')){
			$this->set('error', array(t('Invalid ID')));
			$this->view();
			return;
		}
		$setName = trim($this->post('file_set_name'));
		if (!$setName) {
			$this->set('error', array(t('Please Enter a Name')));
			$this->view();
			return;
		}
		if (!Loader::helper('validation/strings')->multiLingualName($setName, true, true)) {
			$this->set('error', array(t('Set Names must only include alphanumerics and spaces.')));
			$this->view();
			return;
		}

		$file_set = new FileSet();
		$file_set->Load('fsID = ?', $this->post('fsID'));		
		$file_set->fsName = $setName;
		$copyPermissionsFromBase = false;
		if ($file_set->fsOverrideGlobalPermissions == 0 && $this->post('fsOverrideGlobalPermissions') == 1) {
			// we are checking the checkbox for the first time
			$copyPermissionsFromBase = true;
		}		
		if ($file_set->fsOverrideGlobalPermissions) {
			$permissions = PermissionKey::getList('file_set');
			foreach($permissions as $pk) {
				$pk->setPermissionObject($file_set);
				$pt = $pk->getPermissionAssignmentObject();
				$paID = $_POST['pkID'][$pk->getPermissionKeyID()];
				$pt->clearPermissionAssignment();
				if ($paID > 0) {
					$pa = PermissionAccess::getByID($paID, $pk);
					if (is_object($pa)) {
						$pt->assignPermissionAccess($pa);
					}			
				}		
			}			
		}
		$file_set->fsOverrideGlobalPermissions = ($this->post('fsOverrideGlobalPermissions') == 1) ? 1 : 0;
		$file_set->save();
		
		parse_str($this->post('fsDisplayOrder'));
		$file_set->updateFileSetDisplayOrder($fID);

		if ($file_set->fsOverrideGlobalPermissions == 0) {
			$file_set->resetPermissions();		
		} 		
		if ($copyPermissionsFromBase) {
			$file_set->acquireBaseFileSetPermissions();
		}

		$this->redirect("/dashboard/files/sets", 'view_detail', $this->post('fsID'), 'file_set_updated');
	}

}
