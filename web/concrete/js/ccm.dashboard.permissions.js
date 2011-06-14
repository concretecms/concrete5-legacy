ccm_triggerSelectUser = function(uID, uName) {
	ccm_sitemapSelectPermissionsEntity('uID', uID, uName);
}

ccm_triggerSelectGroup = function (gID, gName) {
	ccm_sitemapSelectPermissionsEntity('gID', gID, gName);
}

jQuery(function() {	
	jQuery("#ug-selector").dialog();	
	ccm_sitemapActivatePermissionsSelector();	
});

ccm_sitemapSelectPermissionsEntity = function(selector, id, name) {
	var html = jQuery('#ccm-permissions-entity-base').html();
	jQuery('#ccm-permissions-entities-wrapper').append('<div class="ccm-permissions-entity">' + html + '<\/div>');
	var p = jQuery('.ccm-permissions-entity');
	var ap = p[p.length - 1];
	jQuery(ap).find('h2 span').html(name);
	jQuery(ap).find('input[type=hidden]').val(selector + '_' + id);
	jQuery(ap).find('input[type=radio]').each(function() {
		jQuery(this).attr('name', selector + '_' + id + '_' + jQuery(this).attr('name'));
	});
	
	ccm_sitemapActivatePermissionsSelector();	
}

ccm_sitemapActivatePermissionsSelector = function() {
	jQuery("a.ccm-permissions-remove").click(function() {
		jQuery(this).parent().parent().fadeOut(100, function() {
			jQuery(this).remove();
		});
	});
}