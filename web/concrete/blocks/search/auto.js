// JavaScript Document

var searchBlock ={
	
	init:function(){
		jQuery('#ccm-searchBlock-externalTarget').click(function(){  searchBlock.showResultsURL(this);  });
	},

	validate:function(){
		var failed=0; 
		
		var titleF=jQuery('#ccm_search_block_title');
		var titleV=titleF.val();
		if(!titleV || titleV.length==0 ){
			alert(ccm_t('search-title'));
			titleF.focus();
			failed=1;
		}
		
		if(failed){
			ccm_isBlockError=1;
			return false;
		}
		return true;
	},
	
	showResultsURL:function(cb){
		if(cb.checked) jQuery('#ccm-searchBlock-resultsURL-wrap').css('display','block');
		else jQuery('#ccm-searchBlock-resultsURL-wrap').css('display','none');
	},
	
	pathSelector:function(el){
		var f=jQuery('#ccm-block-form').get(0);
		var isOther=0;
		for( var i=0; i<f.baseSearchPath.length; i++ ){
			if( f.baseSearchPath[i].id=='baseSearchPathOther' && f.baseSearchPath[i].checked ){
				isOther=1;
				break;
			}
		}
		if( isOther ) 
			 jQuery('#basePathSelector').css('display','block');
		else jQuery('#basePathSelector').css('display','none');
	}
}
jQuery(function(){ searchBlock.init(); });

ccm_selectSitemapNode = function(cID, cName) {
	//jQuery("#selectedCname").html(cName);
	jQuery("#searchUnderCID").val(cID);	
}
ccmValidateBlockForm = function() { return searchBlock.validate(); }