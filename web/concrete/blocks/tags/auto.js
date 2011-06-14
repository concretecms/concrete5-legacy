var tags ={
	init:function(){		
		this.tabSetup();
		this.showHideDisplayType();
		
		jQuery('input[name="displayMode"]').change(function() {
			tags.showHideDisplayType();
		});
		
	},	
	tabSetup: function(){
		jQuery('ul#ccm-tags-tabs li a').each( function(num,el){ 
			el.onclick=function(){
				var pane = this.id.replace('ccm-tags-tab-','');
				tags.showPane(pane);
			}
		});		
	},
	showPane:function(pane){
		jQuery('ul#ccm-tags-tabs li').each(function(num,el){ jQuery(el).removeClass('ccm-nav-active') });
		jQuery(document.getElementById('ccm-tags-tab-'+pane).parentNode).addClass('ccm-nav-active');
		jQuery('div.ccm-tagsPane').each(function(num,el){ el.style.display='none'; });
		jQuery('#ccm-tagsPane-'+pane).css('display','block');
	},
	
	validate:function(){
		return true;
	},
	
	showHideDisplayType:function() {
		if(jQuery('#displayMode1').attr('checked')) {
			jQuery('#ccm-tags-display-cloud').hide();
			jQuery('#ccm-tags-display-page').show();
		} else {
			jQuery('#ccm-tags-display-page').hide();
			jQuery('#ccm-tags-display-cloud').show();
		}
	}
}
jQuery(function(){ tags.init(); });

ccmValidateBlockForm = function() { return tags.validate(); }