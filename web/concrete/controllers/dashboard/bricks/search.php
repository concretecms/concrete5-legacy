<?php
defined('C5_EXECUTE') or die(_("Access Denied."));
class DashboardBricksSearchController extends Controller {
	
	public function view($akCategoryHandle = NULL) {
		if(!$akCategoryHandle) $this->redirect('dashboard/bricks');
		
		Loader::model('attribute_key_category_item_permission');
		$akcip = AttributeKeyCategoryItemPermission::get($akCategoryHandle);
		$this->set('permission', $akcip->canSearch());
		
		$this->set('akCategoryHandle', $akCategoryHandle);
		$this->set('txt', Loader::helper('text'));
		$this->set('form', Loader::helper('form'));
		
		$akcsh = Loader::helper('attribute_key_category_settings');
		$rs = $akcsh->getRegisteredSettings($akCategoryHandle);
		$this->set('rs', $rs);
		
		if($akcip->canSearch()) {
			$searchInstance = $akCategoryHandle.time();
			if (isset($_REQUEST['searchInstance'])) {
				$searchInstance = $_REQUEST['searchInstance'];
			}
			$this->addHeaderItem(Loader::helper('html')->javascript('ccm.attributekeycategory.js'));
			$this->addHeaderItem('<script type="text/javascript">$(function(){ccm_setupAdvancedSearch(\''.$searchInstance.'\');});</script>');
			$objectList = $this->getRequestedSearchResults($akCategoryHandle);
			$objects = $objectList->getPage();
			
			$this->set('newObjectList', $objectList);		
			$this->set('newObjects', $objects);		
			$this->set('pagination', $objectList->getPagination());
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
		$objectList = $akc->getItemList();
		
		if ($_GET['keywords'] != '') {
			$objectList->filterByKeywords($_GET['keywords']);
		}	
		
		if($sortBy) {
			$objectList->sortBy($sortBy->columnKey, $sortBy->defaultSortDirection);
		}
		
		if(is_array($_REQUEST['selectedSearchField'])) {
			$txt = Loader::helper('text');
			foreach($_REQUEST['selectedSearchField'] as $i => $item) {
				// due to the way the form is setup, index will always be one more than the arrays
				if ($item != '') {
					if(is_numeric($item)) {
						$className = $txt->camelcase($akCategoryHandle).'AttributeKey';
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
						$cnt->searchForm($objectList);
					} else {
						if(!empty($_REQUEST[$item])) {
							$functionName = 'filterBy';
							$handled = Loader::helper('text')->uncamelcase($_REQUEST[$item]);
							switch($item) { 
								case 'onlyMine':
									$u = new User();
									if(method_exists($objectList, 'filterByUserID')) {
										$objectList->filterByUserID($u->getUserID());
									} elseif(method_exists($objectList, 'filterByAuthorUID')) {
										$objectList->filterByAuthorUID($u->getUserID());
									} elseif(method_exists($objectList, 'filterByUserName')) {
										$objectList->filterByUserName($u->getUserName());
									} else {
										$objectList->filter('uID', $u->getUserID());
									}
									break;
								case 'uID':
									$objectList->filterByUserID($_REQUEST[$item]);
									break;
								default:
									$functionName = 'filterBy';
									$handled = Loader::helper('text')->uncamelcase($item);
									$handled = explode("_", $handled);
									foreach($handled as $word) $functionName .= ucwords($word);
									if(method_exists($objectList, $functionName)){
										call_user_func_array(array($objectList,$functionName), array($_REQUEST[$item]));
									} else {
										$objectList->filter($item, '%'.$_REQUEST[$item].'%', 'LIKE');
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
					$objectList->filterByAttribute(AttributeKey::getByID($filter['handle'])->getAttributeKeyHandle(), $filter['value'], $filter['comparison']);
				} else {
					if(!empty($filter['handle'])) {
						$functionName = 'filterBy';
						$handled = Loader::helper('text')->uncamelcase(v);
						switch($filter['handle']) { 
							case 'uID':
								$objectList->filterByUserID($filter['value']);
								break;
							default:
								$functionName = 'filterBy';
								$handled = Loader::helper('text')->uncamelcase($filter['handle']);
								$handled = explode("_", $handled);
								foreach($handled as $word) $functionName .= ucwords($word);
								if(method_exists($objectList, $functionName)){
									call_user_func_array(array($objectList,$functionName), array($filter['value']));
								} else {
									$objectList->filter($filter['handle'], '%'.$filter['value'].'%', 'LIKE');
								}
								break;
						}
					}
				}
			}
		}
		$req = $objectList->getSearchRequest();
		$this->set('searchRequest', $req);
		
		if ($_REQUEST['numResults']) {
			$objectList->setItemsPerPage($_REQUEST['numResults']);
		} elseif($objectList->getTotal()) {
			$objectList->setItemsPerPage($objectList->getTotal());
		}
		return $objectList;
	}
} ?>
