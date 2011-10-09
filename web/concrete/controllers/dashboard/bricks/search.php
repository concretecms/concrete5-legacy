<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksSearchController extends Controller {
	
	//public $helpers = array('html','form','text');
	
	public function view($akCategoryHandle = NULL) {
		$req = Request::get();
		$this->set('request', $req);	
		
		if(!$akCategoryHandle){
			if(!$req->isIncludeRequest()) $this->redirect('dashboard/bricks');
			return;
		}
		$this->set('akCategoryHandle', $akCategoryHandle);
		$searchInstance = $akCategoryHandle.'_search';
		if (isset($_REQUEST['searchInstance'])) {
			$searchInstance = $_REQUEST['searchInstance'];
		}
		$this->set('searchInstance', $searchInstance);
		
		$baseId = uniqid($searchInstance);
		if(isset($_REQUEST['baseId'])){
			$baseId = $_REQUEST['baseId'];
		}
		$this->set('baseId', $baseId);
		
		
		Loader::model('attribute_key_category_item_permission');
		$akcp = AttributeKeyCategoryItemPermission::get($akCategoryHandle);
		$this->set('akcp', $akcp);
		
		
		$this->set('text', $text = Loader::helper('text'));
		$this->set('form', $form = Loader::helper('form'));
		$this->set('html', $html = Loader::helper('html'));
		
		$akcsh = Loader::helper('attribute_key_category_settings');
		$rs = $akcsh->getRegisteredSettings($akCategoryHandle);
		$this->set('rs', $rs);
		
		if($akcp->canSearch()) {
			$this->addHeaderItem($html->javascript('jquery.ui.js'));			
			$this->addHeaderItem($html->javascript('jquery.metadata.js'));	
			$this->addHeaderItem($html->javascript('jquery.tmpl.js'));
			$this->addHeaderItem($html->javascript('ccm.attributekeycategory.js'));
			
			$columns = $this->getResultsColumns($akCategoryHandle, $searchInstance);
			$this->set('columns', $columns);
			
			$akciList = $this->getRequestedSearchResults($akCategoryHandle);
			$akciListPage = $akciList->getPage();
			
			$this->set('akciList', $akciList);		
			$this->set('akciListPage', $akciListPage);		
			$this->set('pagination', $akciList->getPagination());
		}
			
		$subnav = array(array(View::url('dashboard/bricks'), t('Categories')));
		foreach($akcsh->getActions() as $action) {
			if(!$rs['url_'.$action.'_hidden']) {
				$url = View::url('dashboard/bricks/', $action, $akCategoryHandle);
				if($rs['url_'.$action]) $url = View::url($rs['url_'.$action]);
				$subnav[] = array(
					$url,
					t(ucwords($action)),
					($this->getCollectionObject()->getCollectionHandle() == $action)
				);
			}
		}
		$this->set('subnav', $subnav);
	}
	
	public function getRequestedSearchResults($akCategoryHandle, $sortBy = NULL) {
		$akc = AttributeKeyCategory::getByHandle($akCategoryHandle);
		$akciList = $akc->getItemList();
		$akciList->enableStickySearchRequest(true);
		
		if ($_GET['keywords'] != '') {
			$akciList->filterByKeywords($_GET['keywords']);
		}
		
		if($sortBy) {
			$akciList->sortBy($sortBy->columnKey, $sortBy->defaultSortDirection);
		}
		
		if(is_array($_REQUEST['selectedSearchField'])) {
			
			foreach($_REQUEST['selectedSearchField'] as $i => $item) {
				// due to the way the form is setup, index will always be one more than the arrays
				if ($item != '') {
					if(is_numeric($item)) {
						$className = $text->camelcase($akCategoryHandle).'AttributeKey';
						if(class_exists($className)) {
							$ak = new $className;
							$ak = $ak->getByID($item);
						} else {
							$ak = AttributeKey::getByID($item);
						}
						$type = $ak->getAttributeType();
						$cnt = $type->getController();
						$cnt->setRequestArray($req);
						$cnt->setAttributeKey($ak);
						$cnt->searchForm($akciList);
					} else {
						if(!empty($_REQUEST[$item])) {
							$functionName = 'filterBy';
							$handled = Loader::helper('text')->uncamelcase($_REQUEST[$item]);
							switch($item) { 
								case 'onlyMine':
									$u = new User();
									if(method_exists($akciList, 'filterByUserID')) {
										$akciList->filterByUserID($u->getUserID());
									} elseif(method_exists($akciList, 'filterByAuthorUID')) {
										$akciList->filterByAuthorUID($u->getUserID());
									} elseif(method_exists($akciList, 'filterByUserName')) {
										$akciList->filterByUserName($u->getUserName());
									} else {
										$akciList->filter('uID', $u->getUserID());
									}
									break;
								case 'uID':
									$akciList->filterByUserID($_REQUEST[$item]);
									break;
								default:
									$functionName = 'filterBy';
									$handled = Loader::helper('text')->uncamelcase($item);
									$handled = explode("_", $handled);
									foreach($handled as $word) $functionName .= ucwords($word);
									if(method_exists($akciList, $functionName)){
										call_user_func_array(array($akciList,$functionName), array($_REQUEST[$item]));
									} else {
										$akciList->filter($item, '%'.$_REQUEST[$item].'%', 'LIKE');
									}
									break;
							}
						}
					}
				}
			}
		}
		if(is_array($_REQUEST['searchFilters'])) {
			foreach($_REQUEST['searchFilters'] as $filter) {
				if(is_numeric($filter['handle'])) {
					$akciList->filterByAttribute(AttributeKey::getByID($filter['handle'])->getAttributeKeyHandle(), $filter['value'], $filter['comparison']);
				} else {
					if(!empty($filter['handle'])) {
						$functionName = 'filterBy';
						$handled = Loader::helper('text')->uncamelcase(v);
						switch($filter['handle']) { 
							case 'uID':
								$akciList->filterByUserID($filter['value']);
								break;
							default:
								$functionName = 'filterBy';
								$handled = Loader::helper('text')->uncamelcase($filter['handle']);
								$handled = explode("_", $handled);
								foreach($handled as $word) $functionName .= ucwords($word);
								if(method_exists($akciList, $functionName)){
									call_user_func_array(array($akciList,$functionName), array($filter['value']));
								} else {
									$akciList->filter($filter['handle'], '%'.$filter['value'].'%', 'LIKE');
								}
								break;
						}
					}
				}
			}
		}
		$req = $akciList->getSearchRequest();
		$this->set('searchRequest', $req);
		
		if ($_REQUEST['numResults']) {
			$akciList->setItemsPerPage($_REQUEST['numResults']);
		} elseif($akciList->getTotal()) {
			$akciList->setItemsPerPage($akciList->getTotal());
		}
		return $akciList;
	}



	public function getResultsColumns($akCategoryHandle, $searchInstance){
		global $u;
		Loader::model('attribute_key_category_item_list');
		$akccs = new AttributeKeyCategoryColumnSet($akCategoryHandle);
		$db = Loader::db();
		$exists = $db->GetOne('SELECT columns FROM BricksCustomColumns WHERE searchInstance = ? AND uID = ? AND akCategoryHandle = ?', array($searchInstance, 0, $akCategoryHandle));
		if($exists) {
			$defaults = unserialize(urldecode($exists));
			$columns = $defaults['columns'];
		}
		$exists = $db->GetOne('SELECT columns FROM BricksCustomColumns WHERE searchInstance = ? AND uID = ? AND akCategoryHandle = ?', array($searchInstance, $u->getUserID(), $akCategoryHandle));
		if($exists) {
			$defaults = unserialize(urldecode($exists));
			$columns = $defaults['columns'];
		} elseif(isset($_REQUEST['defaults'])) {
			$defaults = unserialize(urldecode($_REQUEST['defaults']));
			$columns = $defaults['columns'];
		} elseif(isset($_REQUEST['defaults_'.$searchInstance])) {
			$defaults = unserialize(urldecode($_REQUEST['defaults_'.$searchInstance]));
			$columns = $defaults['columns'];
		}
		
		if(is_string($columns)) $columns = unserialize(urldecode($columns));
		if(!$columns) $columns = $akccs->getCurrent();
		
		return $columns;
	}
} ?>
