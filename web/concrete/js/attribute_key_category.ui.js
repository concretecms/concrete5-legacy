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
ccm_setupAttributeKeyCategoryItemSearch = function(searchInstance, akID) {

	ccm_setupAdvancedSearch(searchInstance);
	
	id = "#ccm-" + searchInstance + "-list-multiple-operations";
	if(typeof(akID) != 'undefined') {
		id = "#ccm-" + searchInstance + "-list-multiple-operations-" + akID;
	}
	
	$("#ccm-" + searchInstance + "-list-cb-all").click(function() {
		if ($(this).is(':checked')) {
			$('.ccm-list-record td.ccm-' + searchInstance + '-list-cb input[type=checkbox]').attr('checked', true);
			$(id).attr('disabled', false);
		} else {
			$('.ccm-list-record td.ccm-' + searchInstance + '-list-cb input[type=checkbox]').attr('checked', false);
			$(id).attr('disabled', true);
		}
	});
	$("td.ccm-" + searchInstance + "-list-cb input[type=checkbox]").click(function() {
		if ($("td.ccm-" + searchInstance + "-list-cb input[type=checkbox]:checked").length > 0) {
			$(id).attr('disabled', false);
		} else {
			$(id).attr('disabled', true);
		}
	});
	
	// if we're not in the dashboard, add to the multiple operations select menu

	$(id).change(function() {
		var action = $(this).val();
		switch(action) {
			case 'choose':
				var idstr = '';
				$("td.ccm-" + searchInstance + "-list-cb input[type=checkbox]:checked").each(function() {
					console.log($(this).parent().parent());
					ccm_triggerSelectAttributeKeyCategoryItem(akID, $(this).parent().parent());
				});
				jQuery.fn.dialog.closeTop();
				break;
			case "properties": 
				oIDstring = 'searchInstance='+searchInstance+'&akCategoryHandle='+$(this).attr('akCategoryHandle');
				$("td.ccm-" + searchInstance + "-list-cb input[type=checkbox]:checked").each(function() {
					oIDstring=oIDstring+'&newObjectID[]='+$(this).val();
				});
				jQuery.fn.dialog.open({
					width: 630,
					height: 450,
					modal: true,
					href: CCM_TOOLS_PATH +'/bricks/bulk_properties?' + oIDstring,
					title: ccmi18n.properties
				});
				break;	
			case "delete": 
				URIComponents = '{"akCategoryHandle":"' + $(this).attr('akCategoryHandle') + '","akcIDs":[';
				$("td.ccm-" + searchInstance + "-list-cb input[type=checkbox]:checked").each(function() {
					URIComponents = URIComponents + '"' + $(this).val() + '",';
				});
				URIComponents = URIComponents.substring(0, URIComponents.length-1);
				URIComponents = URIComponents + ']}';
				
				jQuery.fn.dialog.open({
					width: 300,
					height: 100,
					modal: true,
					href: CCM_TOOLS_PATH + '/bricks/bulk_delete?searchInstance='+searchInstance+'&akCategoryHandle='+$(this).attr('akCategoryHandle')+'&json=' + encodeURIComponent(URIComponents),
					title: ccmi18n.properties
				});
				break;				
		}
		
		$(this).get(0).selectedIndex = 0;
	});

	$("div.ccm-" + searchInstance + "-search-advanced-groups-cb input[type=checkbox]").unbind();
	$("div.ccm-" + searchInstance + "-search-advanced-groups-cb input[type=checkbox]").click(function() {
		$("#ccm-" + searchInstance + "-advanced-search").submit();
	});

}

ccm_closeModalRefeshSearch = function(searchInstance) {
	jQuery.fn.dialog.closeTop();
	$("#ccm-" + searchInstance + "-advanced-search").submit();
}
ccm_deleteAndRefeshSearch = function(URIComponents, searchInstance) {
	$.ajax({
		url: CCM_TOOLS_PATH + '/bricks/bulk_delete?task=delete&json=' + URIComponents
	}).responseText;
	jQuery.fn.dialog.closeTop();
	$("#ccm-" + searchInstance + "-advanced-search").submit();
}