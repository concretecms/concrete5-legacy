ccm_triggerSelectUser = function(uID, uName) {
	ccm_sitemapSelectPermissionsEntity('uID', uID, uName);
}

ccm_triggerSelectGroup = function (gID, gName) {
	ccm_sitemapSelectPermissionsEntity('gID', gID, gName);
}

$(function() {	
	$("#ug-selector").dialog();	
	ccm_sitemapActivatePermissionsSelector();	
});

ccm_sitemapSelectPermissionsEntity = function(selector, id, name) {
	var html = $('#ccm-permissions-entity-base').html();
	var i = 0;
	$('input[name*="'+selector+'['+id+']]"').each(function() { i++; });
	if(i < 2) { 
		$('#ccm-permissions-entities-wrapper').append('<div class="ccm-permissions-entity">' + html + '<\/div>');
		var p = $('.ccm-permissions-entity');
		var ap = p[p.length - 1];
		$(ap).find('h2 span').html(name);
		$(ap).find('input[type=hidden]').val(selector + '[' + id +']');
		$(ap).find('input[type=radio]').each(function() {
			$(this).attr('id', selector + '[' + id + ']' + $(this).attr('name') );
			$(this).attr('name', selector + '[' + id + ']' + $(this).attr('name') );
		});
	
		ccm_sitemapActivatePermissionsSelector();
	} else {
		alert('That selection has already been added to the list.');
	}
}

ccm_sitemapActivatePermissionsSelector = function() {
	$("a.ccm-permissions-remove").click(function() {
		$(this).parent().parent().fadeOut(100, function() {
			$(this).remove();
		});
	});
}
