<?php 
defined('C5_EXECUTE') or die("Access Denied.");

class SearchBlockController extends BlockController {
	
	protected $btTable = 'btSearch';
	protected $btInterfaceWidth = "400";
	protected $btInterfaceHeight = "240";
	
	public $title = "";
	public $buttonText = ">"; 
	public $baseSearchPath = "";
	public $resultsURL = "";
		
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
	    foreach($highlights as $highlight){
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
	           $result .= '<span style="background-color:'. $this->hColor[0] .';">'.$texts[0].'</span>'.mb_substr($texts[1], 0 , 47 , "UTF-8")."…";
	       }else{
	           $result .= '<span style="background-color:'. $this->hColor[0] .';">'.$text;
	       }
	    }
        return $result;
	}
	
	/** 
	 * Used for localization. If we want to localize the name/description we have to include this
	 */
	public function getBlockTypeDescription() {
		return t("Add a search box to your site.");
	}
	
	public function getBlockTypeName() {
		return t("Search");
	}		
	
	public function getJavaScriptStrings() {
		return array('search-title' => t('Please enter a valid search title.'));
	}
	
	function __construct($obj = null) {		
		parent::__construct($obj);
		if ($this->title == '') {
			$this->title=t("Search");
		}
	}
	
	public function indexExists() {
		$db = Loader::db();
		$numRows = $db->GetOne('select count(cID) from PageSearchIndex');
		return ($numRows > 0);
	}
	
	function view(){
		$c = Page::getCurrentPage(); 
		$this->set('title', $this->title);
		$this->set('buttonText', $this->buttonText);
		$this->set('baseSearchPath', $this->baseSearchPath);			
		
		//auto target is the form action that is used if none is explicity set by the user
		$autoTarget= $c->getCollectionPath();
		/* 
		 * This code is weird. I don't know why it's here or what it does 
		 
		if( is_array($_REQUEST['search_paths']) ){
			foreach($_REQUEST['search_paths'] as $search_path){
				$autoTarget=str_replace('search_paths[]='.$search_path,'',$autoTarget);
				$autoTarget=str_replace('search_paths%5B%5D='.$search_path,'',$autoTarget);
			}
		}
		$autoTarget=str_replace('page='.$_REQUEST['page'],'',$autoTarget);
		$autoTarget=str_replace('submit='.$_REQUEST['submit'],'',$autoTarget);
		$autoTarget=str_replace(array('&&&&','&&&','&&'),'',$autoTarget);
		*/
		
		$resultTargetURL = ($this->resultsURL != '') ? $this->resultsURL : $autoTarget;			
		$this->set('resultTargetURL', $resultTargetURL);

		//run query if display results elsewhere not set, or the cID of this page is set
		if( !empty($_REQUEST['query']) || isset($_REQUEST['akID']) || isset($_REQUEST['month']))  { 
			$this->do_search();
		}						
	}
	
	function save($data) { 
		$args['title'] = isset($data['title']) ? $data['title'] : '';
		$args['buttonText'] = isset($data['buttonText']) ? $data['buttonText'] : '';
		$args['baseSearchPath'] = isset($data['baseSearchPath']) ? $data['baseSearchPath'] : '';
		if( $args['baseSearchPath']=='OTHER' && intval($data['searchUnderCID'])>0 ){
			$customPathC = Page::getByID( intval($data['searchUnderCID']) );
			if( !$customPathC )	$args['baseSearchPath']='';
			else $args['baseSearchPath'] = $customPathC->getCollectionPath();
		}
		if( trim($args['baseSearchPath'])=='/' || strlen(trim($args['baseSearchPath']))==0 )
			$args['baseSearchPath']='';	
		$args['resultsURL'] = ( $data['externalTarget']==1 && strlen($data['resultsURL'])>0 ) ? trim($data['resultsURL']) : '';		
		parent::save($args);
	}
	
	public $reservedParams=array('page=','query=','search_paths[]=','submit=','search_paths%5B%5D=' );
	
	function do_search() {
		$q = $_REQUEST['query'];
		// i have NO idea why we added this in rev 2000. I think I was being stupid. - andrew
		// $_q = trim(preg_replace('/[^A-Za-z0-9\s\']/i', ' ', $_REQUEST['query']));
		$_q = $q;
		Loader::library('database_indexed_search');
		$ipl = new IndexedPageList();
		$aksearch = false;
		$ipl->ignoreAliases();
		if (is_array($_REQUEST['akID'])) {
			Loader::model('attribute/categories/collection');
			foreach($_REQUEST['akID'] as $akID => $req) {
				$fak = CollectionAttributeKey::getByID($akID);
				if (is_object($fak)) {
					$type = $fak->getAttributeType();
					$cnt = $type->getController();
					$cnt->setAttributeKey($fak);
					$cnt->searchForm($ipl);
					$aksearch = true;
				}
			}
		}

		if (isset($_REQUEST['month']) && isset($_REQUEST['year'])) {
			$month = strtotime($_REQUEST['year'] . '-' . $_REQUEST['month'] . '-01');
			$month = date('Y-m-', $month);
			$ipl->filterByPublicDate($month . '%', 'like');
			$aksearch = true;
		}
		
		
		if (empty($_REQUEST['query']) && $aksearch == false) {
			return false;		
		}
		
		$ipl->setSimpleIndexMode(true);
		if (isset($_REQUEST['query'])) {
			$ipl->filterByKeywords($_q);
		}
		
		if( is_array($_REQUEST['search_paths']) ){ 			
			foreach($_REQUEST['search_paths'] as $path) {
				if(!strlen($path)) continue;
				$ipl->filterByPath($path);
			}
		} else if ($this->baseSearchPath != '') {
			$ipl->filterByPath($this->baseSearchPath);
		}
		
		$ipl->filter(false, '(ak_exclude_search_index = 0 or ak_exclude_search_index is null)');

		$res = $ipl->getPage(); 
		
		foreach($res as $r) { 
			$results[] = new IndexedSearchResult($r['cID'], $r['cName'], $r['cDescription'], $r['score'], $r['cPath'], $r['content']);
		}
				
		$this->set('query', $q);
		$this->set('paginator', $ipl->getPagination());
		$this->set('results', $results);
		$this->set('do_search', true);
	}		
	
}

?>
