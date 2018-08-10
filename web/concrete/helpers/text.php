<?php
defined('C5_EXECUTE') or die("Access Denied.");
class TextHelper extends Concrete5_Helper_Text {

	/** 
	 * Takes text and returns it in the "lowercase-and-dashed-with-no-punctuation" format
	 * @param string $handle
	 * @return string $handle
	 */
    public function urlify(
        $handle,
        $maxlength = PAGE_PATH_SEGMENT_MAX_LENGTH,
        $locale = '',
        $removeExcludedWords = true
    ) {
        if(LANGUAGE == "ja"){
    		$handle = trim($handle);
    		$handle = str_replace(PAGE_PATH_SEPARATOR, '-', $handle);
    		$multi = array(
    			"ä"=>"ae",
    			"ö"=>"oe",
    			"ß"=>"ss",
    			"ü"=>"ue",
    			"æ"=>"ae",
    			"ø"=>"oe",
    			"å"=>"aa",
    			"é"=>"e",
    			"è"=>"e",
    			"?"=>"？"	
    		);
    		$handle = str_replace(array_keys($multi), array_values($multi), $handle);
    
    		$search = array("/[&]/", "/[\s]+/", "/[\/]/", "/-+/");
    		$replace = array("and", "-", "","-");
    		
    		if ($leaveSlashes) {
    			$search = array("/[&]/", "/[\s]+/",  "/-+/");
    			$replace = array("and", "-", "-");
    		}
    
    		$handle = preg_replace($search, $replace, $handle);
    		if (function_exists('mb_strtolower')) {
    			$handle = mb_strtolower($handle, APP_CHARSET);
    		} else {
    			$handle = strtolower($handle);
    		}
    		$handle = trim($handle, '-');
    		$handle = str_replace('-', PAGE_PATH_SEPARATOR, $handle);
	        return $handle;
        }else{
            return parent::urlify($handle, $maxlength, $locale, $removeExcludedWords);
        }
    }

}