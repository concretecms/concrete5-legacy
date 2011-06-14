var dateNav ={
	servicesDir: jQuery("input[name=dateNavToolsDir]").val(),
	init:function(){
		this.blockForm=document.forms['ccm-block-form'];
		
		jQuery('#cParentIDLocation').change(function() {
			if(jQuery('#cParentIDLocation').val() == 'CUSTOM') {
			
			}
			
		});
		if (value == "custom") {
				jQuery("#ccm-autonav-page-selector").css('display','block');
			} else {
				jQuery("#ccm-autonav-page-selector").hide();
			}
		}
		
		/*
		this.cParentIDRadios=this.blockForm.cParentID;
		for(var i=0;i<this.cParentIDRadios.length;i++){
			this.cParentIDRadios[i].onclick  = function(){ dateNav.locationOtherShown(); }
			this.cParentIDRadios[i].onchange = function(){ dateNav.locationOtherShown(); }			
		}
		*/
				
		this.showDescriptionsRadios=this.blockForm.showDescriptions;
		for(var i=0;i<this.showDescriptionsRadios.length;i++){
			this.showDescriptionsRadios[i].onclick  = function(){ dateNav.showDescriptionOpts(); }
			this.showDescriptionsRadios[i].onchange = function(){ dateNav.showDescriptionOpts(); }			
		}		
				
		this.truncateSwitch=jQuery('#ccm-pagelist-truncateSummariesOn');
		this.truncateSwitch.click(function(){ dateNav.truncationShown(this); });
		this.truncateSwitch.change(function(){ dateNav.truncationShown(this); });

		this.truncateTitlesSwitch=jQuery('#ccm-pagelist-truncateTitlesOn');
		this.truncateTitlesSwitch.click(function(){ dateNav.titleTruncationShown(this); });
		this.truncateTitlesSwitch.change(function(){ dateNav.titleTruncationShown(this); });		
		 
	}, 
	showDescriptionOpts:function(){ 
		for(var i=0;i<this.showDescriptionsRadios.length;i++){
			if( this.showDescriptionsRadios[i].checked && this.showDescriptionsRadios[i].value=='1' ){
				jQuery('div#ccm-pagelist-summariesOptsWrap').css('display','block'); 
				return; 
			}				
		}
		jQuery('div#ccm-pagelist-summariesOptsWrap').css('display','none');
	},
	truncationShown:function(cb){ 
		var truncateTxt=jQuery('#ccm-pagelist-truncateTxt');
		var f=jQuery('#ccm-pagelist-truncateChars');
		if(cb.checked){
			truncateTxt.removeClass('faintText');
			f.attr('disabled',false);
		}else{
			truncateTxt.addClass('faintText');
			f.attr('disabled',true);
		}
	}, 
	titleTruncationShown:function(cb){ 
		var truncateTxt=jQuery('#ccm-pagelist-truncateTitleTxt');
		var f=jQuery('#ccm-pagelist-truncateTitleChars');
		if(cb.checked){
			truncateTxt.removeClass('faintText');
			f.attr('disabled',false);
		}else{
			truncateTxt.addClass('faintText');
			f.attr('disabled',true);
		}
	}, 	
	locationOtherShown:function(){
		for(var i=0;i<this.cParentIDRadios.length;i++){
			if( this.cParentIDRadios[i].checked && this.cParentIDRadios[i].value=='OTHER' ){
				jQuery('div.ccm-page-list-page-other').css('display','block');
				return; 
			}				
		}
		jQuery('div.ccm-page-list-page-other').css('display','none');
	}, 
	validate:function(){ 
		
		var failed=0; 
		
		if( failed==1 ){
			ccm_isBlockError=1;
			return false;
		}
		
		return true;
	}	
}
jQuery(function(){ dateNav.init(); });

ccmValidateBlockForm = function() { return dateNav.validate(); }