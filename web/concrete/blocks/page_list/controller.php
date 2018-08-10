<?php

	defined('C5_EXECUTE') or die("Access Denied.");
	class PageListBlockController extends Concrete5_Controller_Block_PageList {
		public function getPageList() {
			Loader::model('page_list');
			$db = Loader::db();
			$bID = $this->bID;
			if ($this->bID) {
				$q = "select num, cParentID, cThis, orderBy, ctID, displayAliases, rss from btPageList where bID = '$bID'";
				$r = $db->query($q);
				if ($r) {
					$row = $r->fetchRow();
				}
			} else {
				$row['num'] = $this->num;
				$row['cParentID'] = $this->cParentID;
				$row['cThis'] = $this->cThis;
				$row['orderBy'] = $this->orderBy;
				$row['ctID'] = $this->ctID;
				$row['rss'] = $this->rss;
				$row['displayAliases'] = $this->displayAliases;
			}
			

			$pl = new PageList();
			$pl->setNameSpace('b' . $this->bID);
			
			$cArray = array();

			switch($row['orderBy']) {
				case 'display_asc':
					$pl->sortByDisplayOrder();
					break;
				case 'display_desc':
					$pl->sortByDisplayOrderDescending();
					break;
				case 'chrono_asc':
					$pl->sortByPublicDate();
					break;
				case 'alpha_asc':
					$pl->sortByName();
					break;
				case 'alpha_desc':
					$pl->sortByNameDescending();
					break;
				default:
					$pl->sortByPublicDateDescending();
					break;
			}

			$num = (int) $row['num'];
			
			$pl->setItemsPerPage($num);			

			$c = Page::getCurrentPage();
			if (is_object($c)) {
				$this->cID = $c->getCollectionID();
			}
			
			Loader::model('attribute/categories/collection');
			if ($this->displayFeaturedOnly == 1) {
				$cak = CollectionAttributeKey::getByHandle('is_featured');
				if (is_object($cak)) {
					$pl->filterByIsFeatured(1);
				}
			}
			if (!$row['displayAliases']) {
				$pl->filterByIsAlias(0);
			}
			$pl->filter('cvName', '', '!=');			
		
			if ($row['ctID']) {
				$pl->filterByCollectionTypeID($row['ctID']);
			}
			
			$columns = $db->MetaColumns(CollectionAttributeKey::getIndexedSearchTable());
			if (isset($columns['AK_EXCLUDE_PAGE_LIST'])) {
				$pl->filter(false, '(ak_exclude_page_list = 0 or ak_exclude_page_list is null)');
			}
			
			if ( intval($row['cParentID']) != 0) {
				$cParentID = ($row['cThis']) ? $this->cID : $row['cParentID'];
				if ($this->includeAllDescendents) {
					$pl->filterByPath(Page::getByID($cParentID)->cPath);
				} else {
					$pl->filterByParentID($cParentID);
				}
			}
			return $pl;
		}

	
	}
