<?php 

defined('C5_EXECUTE') or die("Access Denied.");
class PageList extends Concrete5_Model_PageList {

	/** 
	 * Filters by "keywords" (which searches everything including filenames, title, tags, users who uploaded the file, tags)
	 */
	public function filterByKeywords($keywords, $simple = false) {
		$db = Loader::db();
		if(strpos($keywords," ") !== false){
		  $_keywords = explode(" ",$keywords);
		  $kw = array();
		  $qk = array();
		  foreach($_keywords as $keyword){
    		$kw[] = $db->quote($keyword);
    		$qk[] = $db->quote('%' . $keyword . '%');
          }
          unset($_keywords);
		}else{
    		$kw = $db->quote($keywords);
    		$qk = $db->quote('%' . $keywords . '%');
		}
		Loader::model('attribute/categories/collection');		
		$keys = CollectionAttributeKey::getSearchableIndexedList();
		$attribsStr = '';
		foreach ($keys as $ak) {
			$cnt = $ak->getController();			
			$attribsStr.=' OR ' . $cnt->searchKeywords($keywords);
		}

		if ($simple || $this->indexModeSimple) { // $this->indexModeSimple is set by the IndexedPageList class
		    if(is_array($kw)){
		        $qcName = implode(" AND psi.cName like ",$qk);
		        $qcName = "psi.cName like ".$qcName;
		        $qcDescription = implode(" AND psi.cDescription like ",$qk);
		        $qcDescription = "psi.cDescription like ".$qcDescription;
		        $qcontent = implode(" AND psi.content like ",$qk);
		        $qcontent = "psi.content like ".$qcontent;
                $this->filter(false, "((".$qcName.") or (".$qcDescription.") or (".$qcontent.") {$attribsStr})");;
		    }else{
                $this->filter(false, "(psi.cName like $qk or psi.cDescription like $qk or psi.content like $qk {$attribsStr})");
			}		
		} else {
		    if(is_array($kw)){
		      $kw = implode(" ",$kw);
		    }
			$this->indexedSearch = true;
			$this->indexedKeywords = $keywords;
			$this->autoSortColumns[] = 'cIndexScore';
			$this->filter(false, "((match(psi.cName, psi.cDescription, psi.content) against ({$kw})) {$attribsStr})");
		}
	}
}
class PageSearchColumnSet extends Concrete5_Model_PageSearchColumnSet {}
class PageSearchDefaultColumnSet extends Concrete5_Model_PageSearchDefaultColumnSet {}
class PageSearchAvailableColumnSet extends Concrete5_Model_PageSearchAvailableColumnSet {}
