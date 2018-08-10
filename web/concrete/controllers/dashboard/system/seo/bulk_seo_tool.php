<?php 
defined('C5_EXECUTE') or die("Access Denied.");
class DashboardSystemSeoBulkSeoToolController extends Concrete5_Controller_Dashboard_System_Seo_BulkSeoTool {
	public function getRequestedSearchResults() {
	
		$dh = Loader::helper('concrete/dashboard/sitemap');
		if (!$dh->canRead()) {
			return false;
		}
		
		$pageList = new PageList();
		$pageList->ignoreAliases();
		$pageList->enableStickySearchRequest();
		
		if ($_REQUEST['submit_search']) {
			$pageList->resetSearchRequest();
		}

		$req = $pageList->getSearchRequest();
		$pageList->displayUnapprovedPages();

		$pageList->sortBy('cDateModified', 'desc');

		$columns = PageSearchColumnSet::getCurrent();
		$this->set('columns', $columns);
		
		$cvName = htmlentities($req['cvName'], ENT_QUOTES, APP_CHARSET);
		
		if ($cvName != '') {
			$pageList->filterByName($cvName);
		}

		if ($req['cParentIDSearchField'] > 0) {
			if ($req['cParentAll'] == 1) {
				$pc = Page::getByID($req['cParentIDSearchField']);
				$cPath = $pc->cPath;
				$pageList->filterByPath($cPath);
			} else {
				$pageList->filterByParentID($req['cParentIDSearchField']);
			}
			$parentDialogOpen = 1;
		}

		$keywords = htmlentities($req['keywords'], ENT_QUOTES, APP_CHARSET);
		$pageList->filterByKeywords($keywords, true);

		if ($req['numResults']) {
			$pageList->setItemsPerPage($req['numResults']);
		}

		if ($req['ctID']) {
			$pageList->filterByCollectionTypeID($req['ctID']);
		}

		if ($_REQUEST['noKeywords'] == 1){
			$pageList->filter('CollectionSearchIndexAttributes.ak_meta_keywords', NULL ,'=');
			$this->set('keywordCheck', true);
			$parentDialogOpen = 1;
		}
		
		if ($_REQUEST['noDescription'] == 1){
			$pageList->filter('CollectionSearchIndexAttributes.ak_meta_description', NULL ,'=');
			$this->set('descCheck', true);
			$parentDialogOpen = 1;
		}
		
		$this->set('searchRequest', $req);
		$this->set('parentDialogOpen', $parentDialogOpen);
		
		return $pageList;
	}
}
