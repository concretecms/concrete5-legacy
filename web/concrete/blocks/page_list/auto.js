var pageList ={
	servicesDir: jQuery("input[name=pageListToolsDir]").val(),
	init:function(){
		this.blockForm=document.forms['ccm-block-form'];
		this.cParentIDRadios=this.blockForm.cParentID;
		for(var i=0;i<this.cParentIDRadios.length;i++){
			this.cParentIDRadios[i].onclick  = function(){ pageList.locationOtherShown(); }
			this.cParentIDRadios[i].onchange = function(){ pageList.locationOtherShown(); }			
		}
		
		this.rss=document.forms['ccm-block-form'].rss;
		for(var i=0;i<this.rss.length;i++){
			this.rss[i].onclick  = function(){ pageList.rssInfoShown(); }
			this.rss[i].onchange = function(){ pageList.rssInfoShown(); }			
		}
		
		this.truncateSwitch=jQuery('#ccm-pagelist-truncateSummariesOn');
		this.truncateSwitch.click(function(){ pageList.truncationShown(this); });
		this.truncateSwitch.change(function(){ pageList.truncationShown(this); });
		
		this.tabSetup();
	},	
	tabSetup: function(){
		jQuery('ul#ccm-pagelist-tabs li a').each( function(num,el){ 
			el.onclick=function(){
				var pane=this.id.replace('ccm-pagelist-tab-','');
				pageList.showPane(pane);
			}
		});		
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
	showPane:function(pane){
		jQuery('ul#ccm-pagelist-tabs li').each(function(num,el){ jQuery(el).removeClass('ccm-nav-active') });
		jQuery(document.getElementById('ccm-pagelist-tab-'+pane).parentNode).addClass('ccm-nav-active');
		jQuery('div.ccm-pagelistPane').each(function(num,el){ el.style.display='none'; });
		jQuery('#ccm-pagelistPane-'+pane).css('display','block');
		if(pane=='preview') this.loadPreview();
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
	rssInfoShown:function(){
		for(var i=0;i<this.rss.length;i++){
			if( this.rss[i].checked && this.rss[i].value=='1' ){
				jQuery('#ccm-pagelist-rssDetails').css('display','block');
				return; 
			}				
		}
		jQuery('#ccm-pagelist-rssDetails').css('display','none');
	},
	loadPreview:function(){
		var loaderHTML = '<div style="padding: 20px; text-align: center"><img src="' + CCM_IMAGE_PATH + '/throbber_white_32.gif"></div>';
		jQuery('#ccm-pagelistPane-preview').html(loaderHTML);
		var qStr=jQuery(this.blockForm).formSerialize();
		jQuery.ajax({ 
			url: this.servicesDir+'preview_pane.php?'+qStr,
			success: function(msg){ jQuery('#ccm-pagelistPane-preview').html(msg); }
		});
	},
	validate:function(){
			var failed=0;
			
			var rssOn=jQuery('#ccm-pagelist-rssSelectorOn');
			var rssTitle=jQuery('#ccm-pagelist-rssTitle');
			if( rssOn && rssOn.attr('checked') && rssTitle && rssTitle.val().length==0 ){
				alert(ccm_t('feed-name'));
				rssTitle.focus();
				failed=1;
			}
			
			if(failed){
				ccm_isBlockError=1;
				return false;
			}
			return true;
	}	
}
jQuery(function(){ pageList.init(); });

ccmValidateBlockForm = function() { return pageList.validate(); }