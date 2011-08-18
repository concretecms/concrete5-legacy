ccm_setupAttributeKeyCategoryItemSearch = function(searchInstance, akID) {
	if(typeof alreadyRun === 'undefined') alreadyRun = Array();
	if(typeof alreadyRun[searchInstance] === 'undefined') {
		alreadyRun[searchInstance] = true;
		ccm_setupAdvancedSearch(searchInstance);
		
		$("#ccm-" + searchInstance + "-list-cb-all").live('click', function() {
			if ($(this).is(':checked')) {
				$('.ccm-' + searchInstance + '-list-cb input[type=checkbox]').attr('checked', true);
				$("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', false);
			} else {
				$('.ccm-' + searchInstance + '-list-cb input[type=checkbox]').attr('checked', false);
				$("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', true);
			}
		});
		$(".ccm-" + searchInstance + "-list-cb input[type=checkbox]").live('click', function() {
			if ($(".ccm-" + searchInstance + "-list-cb input[type=checkbox]:checked").length > 0) {
				$("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', false);
			} else {
				$("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', true);
			}
		});
		
		// if we're not in the dashboard, add to the multiple operations select menu
	
		$("#ccm-" + searchInstance + "-list-multiple-operations").live('change', function() {
			var action = $(this).val();
			switch(action) {
				case 'choose':
					$("td.ccm-" + searchInstance + "-list-cb input[type=checkbox]:checked").each(function() {
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
		$("div.ccm-" + searchInstance + "-search-advanced-groups-cb input[type=checkbox]").live('click', function() {
			$("#ccm-" + searchInstance + "-advanced-search").submit();
		});
	}
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