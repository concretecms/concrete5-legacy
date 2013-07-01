<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Controller_Dashboard_Pages_Types extends Controller {
	
	
	public function view() { 
		$this->set("icons", CollectionType::getIcons());
	}	
	
	public function on_start() {
		$this->set('disableThirdLevelNav', true);
	}
	
	public function delete($ctID, $token = '', $replace_existing_with = 0) {
		$db = Loader::db();
		$valt = Loader::helper('validation/token');
		if (!$valt->validate('delete_page_type', $token)) {
			$this->set('message', $valt->getErrorMessage());
		} else {
			$ct = CollectionType::getByID($ctID);
			if(!is_object($ct)) {
				$this->set('message', t('Invalid ID.'));
			}
			else {
				$pageCount = $ct->getUsageCount();
				if($pageCount > 0) {
					$replaceExistingWith = Loader::helper('security')->sanitizeInt($replace_existing_with);
					if($replaceExistingWith) {
						if(is_object(CollectionType::getByID($replaceExistingWith))) {
							$db->query('UPDATE Pages INNER JOIN CollectionVersions ON Pages.cID = CollectionVersions.cID SET CollectionVersions.ctID = ? WHERE Pages.cIsTemplate = 0 and CollectionVersions.ctID = ?', array($replaceExistingWith, $ct->getCollectionTypeID()));
							$pageCount = $ct->getUsageCount();
						}
					}
				}
				if($pageCount == 0) {
					$ct->delete();
					$this->redirect("/dashboard/pages/types");
				} else {
					$this->set("error", array(t("You must delete all pages of this type, and remove all page versions that contain this page type before deleting this page type.")));
				}
			}
		}
	}

	public function get_delete_info($ctID, $token = '') {
		$result = array();
		try {
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('get_delete_info', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			$ct = $ctID ? CollectionType::getByID($ctID) : null;
			if(!$ct) {
				throw new Exception(t('Invalid ID.'));
			}
			$result['usage'] = $ct->getUsageCount();
			$replace_existing_with = '';
			if($result['usage'] > 0) {
				$others = array();
				foreach(CollectionType::getList() as $ctOther) {
					if($ctOther->getCollectionTypeID() != $ctID) {
						$others[View::url('/dashboard/pages/types/', 'delete',$ctID, $valt->generate('delete_page_type'), $ctOther->getCollectionTypeID())] = $ctOther->getCollectionTypeName();
					}
				}
				if(count($others)) {
					$form = Loader::helper('form');
					$replace_existing_with .= $form->label('replace_existing_with', t('Re-assign pages to'));
					$replace_existing_with .= $form->select('replace_existing_with', array_merge(array('' => t('Please select')), $others), '');
				}
			}
			$result['replace_existing_with'] = $replace_existing_with;
		}
		catch(Exception $e) {
			$result['error'] = $e->getMessage();
		}
		echo Loader::helper('json')->encode($result);
		die();
	}

	public function page_type_added() {
		$this->set('message', t('Page type added successfully.'));
		$this->view();
	}

	public function page_type_updated() {
		$this->set('message', t('Page type updated successfully.'));
		$this->view();
	}
	
	public function clear_composer() {
		$this->set('message', t("This page type is no longer included in composer."));
	}

	public function update() {
	
	
	}

}