<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksSearchController extends Controller {
	
	public function view($akCategoryHandle = NULL) {
		if(!$akCategoryHandle) $this->redirect('dashboard/bricks');
		$this->addHeaderItem('<script type="text/javascript">$(function(){ccm_setupAdvancedSearch(\'new-object\');});</script>');
		$this->addHeaderItem(Loader::helper('html')->javascript('attribute_key_category.ui.js'));
		$subnav = array(
			array(View::url('dashboard/bricks'), t('Categories')),
			array(View::url('dashboard/bricks/search', $akCategoryHandle), t('Search'), TRUE),
			array(View::url('dashboard/bricks/insert', $akCategoryHandle), t('Insert')),
			array(View::url('dashboard/bricks/structure', $akCategoryHandle), t('Structure')),
			array(View::url('dashboard/bricks/permissions', $akCategoryHandle), t('Permissions')),
			array(View::url('dashboard/bricks/drop', $akCategoryHandle), t('Drop'))
		);
		$this->set('subnav', $subnav);
		$this->set('akCategoryHandle', $akCategoryHandle);
		$this->set('txt', Loader::helper('text'));
		$this->set('form', Loader::helper('form'));
		Loader::model('attribute_key_category_item_permission');
		$akcip = AttributeKeyCategoryItemPermission::getByID($akCategoryHandle);
		$this->set('permission', $akcip->canSearch());
		
		$objectList = $this->getRequestedSearchResults($akCategoryHandle);
		$objects = $objectList->getPage();
		
		$this->set('newObjectList', $objectList);		
		$this->set('newObjects', $objects);		
		$this->set('pagination', $objectList->getPagination());
	}
	
	public function getRequestedSearchResults($akCategoryHandle) {
		$objectList = AttributeKeyCategory::getItemList($akCategoryHandle);
		
		if ($_GET['keywords'] != '') {
			$objectList->filterByKeywords($_GET['keywords']);
		}	
		
		if ($_REQUEST['numResults']) {
			$objectList->setItemsPerPage($_REQUEST['numResults']);
		} else {
			$objectList->setItemsPerPage(10);
		}
		
		if (is_array($_REQUEST['selectedSearchField'])) {
			foreach($_REQUEST['selectedSearchField'] as $i => $akID) {
				// due to the way the form is setup, index will always be one more than the arrays
				if ($akID != '') {
					$ak = AttributeKey::getByID($akID);
					$type = $ak->getAttributeType();
					$cnt = $type->getController();
					$cnt->setRequestArray($req);
					$cnt->setAttributeKey($ak);
					$cnt->searchForm($objectList);
				}
			}
		}
		$req = $objectList->getSearchRequest();
		$this->set('searchRequest', $req);
		return $objectList;
	}
} ?>
