ccm_checkSelectedAdvancedSearchField = function(searchType, fieldset) {
	$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-option[search-field=date_added] input").each(function() {
		if ($(this).attr('id') == 'date_added_from') {
			$(this).attr('id', 'date_added_from' + fieldset);
		} else if ($(this).attr('id') == 'date_added_to') {
			$(this).attr('id', 'date_added_to' + fieldset);
		}
	});
	$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-option[search-field=date_changed] input").each(function() {
		if ($(this).attr('id') == 'date_changed_from') {
			$(this).attr('id', 'date_changed_from' + fieldset);
		} else if ($(this).attr('id') == 'date_changed_to') {
			$(this).attr('id', 'date_changed_to' + fieldset);
		}
	});

	$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-option-type-date_time input").each(function() {
		$(this).attr('id', $(this).attr('id') + fieldset);
	});
	
	
	$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-option[search-field=date_added] input").datepicker({
		showAnim: 'fadeIn'
	});
	$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-option[search-field=date_changed] input").datepicker({
		showAnim: 'fadeIn'
	});
	$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-option-type-date_time input").datepicker({
		showAnim: 'fadeIn'
	});
	$("#ccm-" + searchType + "-search-field-set" + fieldset + " .ccm-search-option-type-rating input").rating();		
}
ccm_setupNewObjectSearch = function() {
	$("#ccm-new-object-list-cb-all").click(function() {
		if ($(this).attr('checked') == true) {
			$('.ccm-list-record td.ccm-new-object-list-cb input[type=checkbox]').attr('checked', true);
			$("#ccm-new-object-list-multiple-operations").attr('disabled', false);
		} else {
			$('.ccm-list-record td.ccm-new-object-list-cb input[type=checkbox]').attr('checked', false);
			$("#ccm-new-object-list-multiple-operations").attr('disabled', true);
		}
	});
	$("td.ccm-new-object-list-cb input[type=checkbox]").click(function(e) {
		if ($("td.ccm-new-object-list-cb input[type=checkbox]:checked").length > 0) {
			$("#ccm-new-object-list-multiple-operations").attr('disabled', false);
		} else {
			$("#ccm-new-object-list-multiple-operations").attr('disabled', true);
		}
	});
	
	// if we're not in the dashboard, add to the multiple operations select menu

	$("#ccm-new-object-list-multiple-operations").change(function() {
		var action = $(this).val();
		switch(action) {
			case 'choose':
				var idstr = '';
				$("td.ccm-new-object-list-cb input[type=checkbox]:checked").each(function() {
					ccm_triggerSelectNewObject($(this).val(), $(this).attr('new-object-name'), $(this).attr('new-object-email'));
				});
				jQuery.fn.dialog.closeTop();
				break;
			case "properties": 
				oIDstring = 'table='+$(this).attr('table');
				$("td.ccm-new-object-list-cb input[type=checkbox]:checked").each(function() {
					oIDstring=oIDstring+'&newObjectID[]='+$(this).val();
				});
				jQuery.fn.dialog.open({
					width: 630,
					height: 450,
					modal: true,
					href: CCM_REL + '/index.php/tools/packages/virtual_tables/search/bulk_properties?' + oIDstring,
					title: ccmi18n.properties
				});
				break;	
			case "delete": 
				URIComponents = '{"table":"' + $(this).attr('table') + '","newObjectIDs":['
				$("td.ccm-new-object-list-cb input[type=checkbox]:checked").each(function() {
					URIComponents = URIComponents + '"' + $(this).val() + '",';
				});
				URIComponents = URIComponents.substring(0, URIComponents.length-1);
				URIComponents = URIComponents + ']}';
				
				jQuery.fn.dialog.open({
					width: 300,
					height: 100,
					modal: true,
					href: CCM_REL + '/index.php/tools/packages/virtual_tables/search/bulk_delete?json=' + encodeURIComponent(URIComponents),
					title: ccmi18n.properties
				});
				break;				
		}
		
		$(this).get(0).selectedIndex = 0;
	});

	$("div.ccm-new-object-search-advanced-groups-cb input[type=checkbox]").unbind();
	$("div.ccm-new-object-search-advanced-groups-cb input[type=checkbox]").click(function() {
		$("#ccm-new-object-advanced-search").submit();
	});

}

ccm_closeModalRefeshSearch = function() {
	jQuery.fn.dialog.closeTop();
	$("#ccm-new-object-advanced-search").submit();
}
ccm_deleteAndRefeshSearch = function(URIComponents) {
	$.ajax({
		url: CCM_REL + '/index.php/tools/packages/virtual_tables/search/bulk_delete?task=delete&json=' + URIComponents,
		async: false
	}).responseText;
	jQuery.fn.dialog.closeTop();
	$("#ccm-new-object-advanced-search").submit();
}