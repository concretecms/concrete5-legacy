<?php
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardPagesTypesController extends Concrete5_Controller_Dashboard_Pages_Types {
		public function update() {
		
		$valt = Loader::helper('validation/token');

		$ct = CollectionType::getByID($_REQUEST['ctID']);
		$ctName = $_POST['ctName'];
		$ctHandle = $_POST['ctHandle'];
		$vs = Loader::helper('validation/strings');
		
		if (!$ctHandle) {
			$this->error->add(t("Handle required."));
		} else if (!$vs->handle($ctHandle)) {
			$this->error->add(t('Handles must contain only letters, numbers or the underscore symbol.'));
		}
		
		if (!$ctName) {
			$this->error->add(t("Name required."));
		} else if (!$vs->multiLingualName($ctName, true)) {
			$this->error->add(t('Page type names can only contain letters, numbers, spaces and the following symbols: !, &, (, ), -, _.'));
		}
		
		
		if (!$valt->validate('update_page_type')) {
			$this->error->add($valt->getErrorMessage());
		}
		
		if (!$this->error->has()) {
			try {
				if (is_object($ct)) {
					$ct->update($_POST);
					$this->redirect('/dashboard/pages/types', 'page_type_updated');
				}		
			} catch(Exception $e1) {
				$this->error->add($e1->getMessage());
			}
		}	

		$this->view();

	
	}

}