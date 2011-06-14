var ccmNextPrevious ={ 

	init:function(){   
		jQuery('input[name=linkStyle]').each(function(i,el){ 
			el.onclick=function(){ ccmNextPrevious.nextPrevLabelsShown(this); }
			el.onchange=function(){ ccmNextPrevious.nextPrevLabelsShown(this); }							   
		})
	},
	
	nextPrevLabelsShown:function(){
		var el=jQuery('input[name="linkStyle"]:checked')
		var displayed=(el.val()=='next_previous')?'block':'none'; 
		jQuery('#ccm_edit_pane_nextPreviousWrap').css('display',displayed);
	},
	
	validate:function(){
			var failed=0; 
			
			if(failed){
				ccm_isBlockError=1;
				return false;
			}
			return true;
	}, 
	
}

jQuery(function(){ ccmNextPrevious.init(); });
 
ccmValidateBlockForm = function() { return ccmNextPrevious.validate(); }