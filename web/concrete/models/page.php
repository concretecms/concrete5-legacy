<?
defined('C5_EXECUTE') or die("Access Denied.");
class Page extends Concrete5_Model_Page {
    
    //URLエンコード対応
	function getCollectionPath() {
		return $this->getEncodePath($this->cPath);
	}

	public static function getCollectionPathFromID($cID) {
		$db = Loader::db();
		$path = $db->GetOne("select cPath from PagePaths inner join CollectionVersions on (PagePaths.cID = CollectionVersions.cID and CollectionVersions.cvIsApproved = 1) where PagePaths.cID = ?", array($cID));
		$path .= '/';
		return $this->getEncodePath($path);
	}
	
	//PagePathのエンコード処理
	function getEncodePath($path){
	    if(mb_strpos($path,"/") !== false){
	      $path = explode("/",$path);
	      $path = array_map("rawurlencode",$path);
	      $newPath = implode("/",$path);
	    }else if(is_null($path)){
          $newPath = NULL;
	    }else{
	      $newPath = rawurlencode($path);
	    } 
	    return str_replace('%21','!',$newPath);
	}

	function getEscapePath($path){
        return htmlentities($path,ENT_QUOTES, APP_CHARSET);
	}
	
		public function rescanPagePaths($newPaths) {
		$db = Loader::db();
		$txt = Loader::helper('text');

		// First, get the list of page paths from the DB.
		$ppaths = $this->getPagePaths();

		// Second, reset all of their cPath values to null.
		$paths = array();
		foreach ($ppaths as $ppath) {
			if (!$ppath['ppIsCanonical']) {
				$paths[$ppath['ppID']] = null;
			}
		}

		// Third, fill in the cPath values from the user updated data.
		foreach ($newPaths as $key=>$val) {
			if (!empty($val)) {
				// Auto-prepend a slash if one is missing.
				$val = trim($val, '/');
				$pathSegments = explode('/', $val);
				$newVal = '/';
				foreach($pathSegments as $pathSegment) {
					$newVal .= $pathSegment . '/';
				}
				$newVal = substr($newVal, 0, strlen($newVal) - 1);
				$newVal = str_replace('-', PAGE_PATH_SEPARATOR, $newVal);

				$paths[$key] = $newVal;
			}
		}
		
		// Fourth, delete, update, or insert page paths as necessary.
		foreach ($paths as $key=>$val) {
			if (empty($val)) {
				$v = array($this->cID, $key);
				$q = "delete from PagePaths where cID = ? and ppID = ?";
			} else if (is_numeric($key)) {
				$val = $this->uniquifyPagePath($val);
				$v = array($val, $this->cID, $key);
				$q = "update PagePaths set cPath = ?, ppIsCanonical = 0 where cID = ? and ppID = ?";
			} else {
				$val = $this->uniquifyPagePath($val);
				$v = array($this->cID, $val);
				$q = "insert into PagePaths (cID, cPath, ppIsCanonical) values (?, ?, 0)";
			}
			$r = $db->query($q, $v);
		}
	}

}