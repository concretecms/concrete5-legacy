<?php 

defined('C5_EXECUTE') or die("Access Denied.");
class ValidationAntispamAskimetLibraryHelper {
	public function check($comment){
	loader::library('3rdparty/Zend/Service/akismet');
	$akismet = new akismet('akismet_api_key');
		if(!$akismet->error) {
		    if($akismet->is_spam($comment)) {
		        return true;
		    } else {
		        return false;
		    }
		}
	}
}

?>
