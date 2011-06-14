// cannot rely on jQuery being loaded here

var ccm_uiLoaded = false;
var ccm_siteActivated = true;
var ccm_animEffects = false;

ccm_parseJSON = function(resp, onNoError) {
	if (resp.error) {
		alert(resp.message);	
	} else {
		onNoError();
	}
}

ccm_deactivateSite = function(onDone) {
	if (ccm_siteActivated == false) {
		return false;
	}
	
	if (jQuery("#ccm-overlay").length < 1) {
		jQuery(document.body).append('<div id="ccm-overlay"></div>');
	}
	
	jQuery("embed,object").each(function() {
		jQuery(this).attr('ccm-style-old-visibility', jQuery(this).css('visibility'));
		jQuery(this).css('visibility', 'hidden');
	});
	
	if (ccm_animEffects) {
		jQuery("#ccm-overlay").fadeIn(100);
	} else {
		jQuery("#ccm-overlay").show();
	}
	
	ccm_siteActivated = false;
	if (typeof onDone == 'function') {
		onDone();
	}
}

ccm_activateSite = function() {
	if (ccm_animEffects) {
		jQuery("#ccm-overlay").fadeOut(100);
	} else {
		jQuery("#ccm-overlay").hide();
	}
	
	jQuery("embed,object").each(function() {
		jQuery(this).css('visibility', jQuery(this).attr('ccm-style-old-visibility'));
	});

	ccm_siteActivated = true;
	ccm_topPaneDeactivated = false;
}

ccm_addHeaderItem = function(item, type) {
	if (type == 'CSS') {
		if (!(jQuery('head').children('link[href*=' + item + ']').length)) {
			jQuery('head').append('<link rel="stylesheet" type="text/css" href="' + item + '?ts=' + new Date().getTime() + '" />');
		}
	} else if (type == 'JAVASCRIPT') {
		if (!(jQuery('head').children('script[src*=' + item + ']').length)) {
			jQuery('head').append('<script type="text/javascript" src="' + item + '?ts=' + new Date().getTime() + '"></script>');
		}

	}
}

// called in versions popup
ccm_disableLinks = function() {
	td = document.createElement("DIV");
	td.style.position = "absolute";
	td.style.top = "0px";
	td.style.left = "0px";
	td.style.width = "100%";
	td.style.height = "100%";
	td.style.zIndex = "1000";
	document.body.appendChild(td);
}