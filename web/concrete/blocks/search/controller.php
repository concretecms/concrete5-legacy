<?php
defined('C5_EXECUTE') or die("Access Denied.");

class SearchBlockController extends Concrete5_Controller_Block_Search {

	protected $hColor = array('#EFE795','#D1E095','#95F0C8','#95DAF0','#9E95F0');

	public function highlightedMarkup($fulltext, $highlight) {
	    if(!$highlight){
	       return $fulltext;
	    }
	    $highlight = str_replace("　"," ",$highlight);
	    if(strpos($highlight," ") !== false){
	       $highlights = explode(" ",$highlight);
	    }else{
	       $highlights = array($highlight);
	    }
		$this->hText = $fulltext;
		foreach($highlights as $hkey=>$highlight){
    		$this->hHighlight  = str_replace(array('"',"'","&quot;"),'',$highlight); // strip the quotes as they mess the regex
    		$this->hText = @preg_replace( "#$this->hHighlight#i", '<span style="background-color:'. $this->hColor[$hkey] .';">$0</span>', $this->hText );
		}
		return $this->hText; 
	}
	
	public function highlightedExtendedMarkup($fulltext, $highlight) {
		$text = @preg_replace("#\n|\r#", ' ', $fulltext);
		
		$matches = array();
	    if(strpos($highlight," ") !== false){
	       $highlights = explode(" ",$highlight);
	    }else{
	       $highlights = array($highlight);
	    }
	    foreach($highlights as $key=>$highlight){
	       $fulltext = str_replace($highlight,'<span style="background-color:'. $this->hColor[0] .';">'.$highlight."</span>",$fulltext);
	    }
	    $fulltext = explode('<span style="background-color:'. $this->hColor[0] .';">',$fulltext);
	    foreach($fulltext as $key=>$text){
	       if($key == 0){
	           $result = mb_substr($text,strlen($text)-40,40,"UTF-8");
	           continue;
	       }
	       $texts = explode("</span>",$text);
	       if(strlen($texts[1]) > 47){
	           $result .= '<span style="background-color:'. $this->hColor[$key] .';">'.$texts[0].'</span>'.mb_substr($texts[1], 0 , 47 , "UTF-8")."…";
	       }else{
	           $result .= '<span style="background-color:'. $this->hColor[$key] .';">'.$text;
	       }
	    }
        return $result;
	}

	public function setHighlightColor($color) {
		$this->hColor = array($color);
	}
}