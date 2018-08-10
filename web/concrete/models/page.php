<?php
defined('C5_EXECUTE') or die("Access Denied.");
class Page extends Concrete5_Model_Page {
    
    //URLエンコード対応
	function getCollectionPath() {
		return self::getEncodePath($this->cPath);
	}

	public static function getCollectionPathFromID($cID) {
		$db = Loader::db();
		$path = $db->GetOne("select cPath from PagePaths inner join CollectionVersions on (PagePaths.cID = CollectionVersions.cID and CollectionVersions.cvIsApproved = 1) where PagePaths.cID = ?", array($cID));
		$path .= '/';
		return self::getEncodePath($path);
	}
	
	//PagePathのエンコード処理
	public static function getEncodePath($path){
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
	
}