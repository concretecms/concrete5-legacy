	function toggleCustomPage(value) {
		if (value == "custom") {
			jQuery("#ccm-autonav-page-selector").css('display','block');
		} else {
			jQuery("#ccm-autonav-page-selector").hide();
		}
	}

	function toggleSubPageLevels(value) {
		if (value == "none") {
			jQuery("#displaySubPageLevels").get(0)[0].selected = true;
			jQuery("#displaySubPageLevels").get(0).disabled = true;
			document.getElementById("displaySubPageLevels").disabled = true;
		} else {
			jQuery("#displaySubPageLevels").get(0).disabled = false;
		}
	}

	function toggleSubPageLevelsNum(value) {
		if (value == "custom") {
			jQuery("#divSubPageLevelsNum").css('display','block');
		} else {
			jQuery("#divSubPageLevelsNum").hide();
		}
	}

	reloadPreview = function(blockForm) {
		orderBy = jQuery("select[name=orderBy]").val();
		displayPages = jQuery("select[name=displayPages]").val();
		displaySubPages = jQuery("select[name=displaySubPages]").val();
		displaySubPageLevels = jQuery("select[name=displaySubPageLevels]").val();
		displaySubPageLevelsNum = jQuery("input[name=displaySubPageLevelsNum]").val();
		displayUnavailablePages = jQuery("input[name=displayUnavailablePages]").val();
		displayPagesCID = jQuery("input[name=displayPagesCID]").val();
		displayPagesIncludeSelf = jQuery("input[name=displayUnavailablePages]").val();

		if(displayPages == "custom" && !displayPagesCID) { return false; }
		
		//jQuery("#ccm-dialog-throbber").css('visibility', 'visible');

		var loaderHTML = '<div style="padding: 20px; text-align: center"><img src="' + CCM_IMAGE_PATH + '/throbber_white_32.gif"></div>';
		jQuery('#ccm-autonavPane-preview').html(loaderHTML);
		
		jQuery.ajax({
			type: 'POST',
			url: jQuery("input[name=autonavPreviewPane]").val(),
			data: 'orderBy=' + orderBy + '&cID=' + jQuery("input[name=autonavCurrentCID]").val() + '&displayPages=' + displayPages + '&displaySubPages=' + displaySubPages + '&displaySubPageLevels=' + displaySubPageLevels + '&displaySubPageLevelsNum=' + displaySubPageLevelsNum + '&displayUnavailablePages=' + displayUnavailablePages + '&displayPagesCID=' + displayPagesCID + '&displayPagesIncludeSelf=' + displayPagesIncludeSelf,
			success: function(resp) {
				//jQuery("#ccm-dialog-throbber").css('visibility', 'hidden');
				jQuery("#ccm-autonavPane-preview").html(resp);
				jQuery("#ccm-auto-nav").css('opacity',1);
			}		
		});
	}
	
	function reloadCCMCall() {
		reloadPreview(document.blockForm);
	}
	
	autonavTabSetup = function() {
		jQuery('ul#ccm-autonav-tabs li a').each( function(num,el){ 
			el.onclick=function(){
				var pane=this.id.replace('ccm-autonav-tab-','');
				autonavShowPane(pane);
			}
		});		
	}
	
	autonavShowPane = function (pane){
		jQuery('ul#ccm-autonav-tabs li').each(function(num,el){ jQuery(el).removeClass('ccm-nav-active') });
		jQuery(document.getElementById('ccm-autonav-tab-'+pane).parentNode).addClass('ccm-nav-active');
		jQuery('div.ccm-autonavPane').each(function(num,el){ el.style.display='none'; });
		jQuery('#ccm-autonavPane-'+pane).css('display','block');
		if(pane=='preview') reloadPreview(document.blockForm);
	}
	
	jQuery(function() {	
		autonavTabSetup();		
	});

