
var ccm_totalAdvancedSearchFields = 0;
var ccm_alLaunchType = new Array();
var ccm_alActiveAssetField = "";
var ccm_alProcessorTarget = "";
var ccm_alDebug = false;

ccm_triggerSelectFile = function(fID, af) {
	if (af == null) {
		var af = ccm_alActiveAssetField;
	}
	//alert(af);
	var obj = jQuery('#' + af + "-fm-selected");
	var dobj = jQuery('#' + af + "-fm-display");
	dobj.hide();
	obj.show();
	obj.load(CCM_TOOLS_PATH + '/files/selector_data?fID=' + fID + '&ccm_file_selected_field=' + af, function() {
		/*
		jQuery(this).find('a.ccm-file-manager-clear-asset').click(function(e) {
			var field = jQuery(this).attr('ccm-file-manager-field');
			ccm_clearFile(e, field);
		});
		*/
		obj.attr('fID', fID);
		obj.attr('ccm-file-manager-can-view', obj.children('div').attr('ccm-file-manager-can-view'));
		obj.attr('ccm-file-manager-can-edit', obj.children('div').attr('ccm-file-manager-can-edit'));
		obj.attr('ccm-file-manager-can-admin', obj.children('div').attr('ccm-file-manager-can-admin'));
		obj.attr('ccm-file-manager-can-replace', obj.children('div').attr('ccm-file-manager-can-replace'));
		
		obj.click(function(e) {
			e.stopPropagation();
			ccm_alActivateMenu(jQuery(this),e);
		});
		
		if (typeof(ccm_triggerSelectFileComplete)  == 'function') {
			ccm_triggerSelectFileComplete(fID, af);
		}
	});
	var vobj = jQuery('#' + af + "-fm-value");
	vobj.attr('value', fID);
	ccm_alSetupFileProcessor();
}

ccm_alGetFileData = function(fID, onComplete) {
	jQuery.getJSON(CCM_TOOLS_PATH + '/files/get_data.php?fID=' + fID, function(resp) {
		onComplete(resp);
	});
}

ccm_clearFile = function(e, af) {
	e.stopPropagation();
	var obj = jQuery('#' + af + "-fm-selected");
	var dobj = jQuery('#' + af + "-fm-display");
	var vobj = jQuery('#' + af + "-fm-value");
	vobj.attr('value', 0);
	obj.hide();
	dobj.show();
}

ccm_activateFileManager = function(altype, searchInstance) {
	//delegate event handling to table container so clicks
	//to our star don't interfer with clicks to our rows
	ccm_alSetupSelectFiles(searchInstance);
	
	jQuery(document).click(function(e) {		
		e.stopPropagation();
		ccm_alSelectNone();
	});

	ccm_setupAdvancedSearch(searchInstance);
	
	if (altype == 'DASHBOARD') {
		jQuery(".dialog-launch").dialog();
	}
	
	ccm_alLaunchType[searchInstance] = altype;
	
	ccm_alSetupCheckboxes(searchInstance);
	ccm_alSetupFileProcessor();
	ccm_alSetupSingleUploadForm();
	
	jQuery("form#ccm-" + searchInstance + "-advanced-search select[name=fssID]").change(function() {
		if (altype == 'DASHBOARD') { 
			window.location.href = CCM_DISPATCHER_FILENAME + '/dashboard/files/search?fssID=' + jQuery(this).val();
		} else {
			jQuery.fn.dialog.showLoader();
			var url = jQuery("div#ccm-" + searchInstance + "-overlay-wrapper input[name=dialogAction]").val() + "&refreshDialog=1&fssID=" + jQuery(this).val();
			jQuery.get(url, function(resp) {
				jQuery.fn.dialog.hideLoader();
				jQuery("div#ccm-" + searchInstance + "-overlay-wrapper").html(resp);
				jQuery("div#ccm-" + searchInstance + "-overlay-wrapper a.dialog-launch").dialog();
			});
		}
	});

	jQuery("form#ccm-" + searchInstance + "-advanced-search a.ccm-search-saved-exit").click(function() {
		if (altype == 'DASHBOARD') { 
			window.location.href = CCM_DISPATCHER_FILENAME + '/dashboard/files/search';
		} else {
			jQuery.fn.dialog.showLoader();
			var url = jQuery("div#ccm-" + searchInstance + "-overlay-wrapper input[name=dialogAction]").val() + "&refreshDialog=1";
			jQuery.get(url, function(resp) {
				jQuery.fn.dialog.hideLoader();
				jQuery("div#ccm-" + searchInstance + "-overlay-wrapper").html(resp);
				jQuery("div#ccm-" + searchInstance + "-overlay-wrapper a.dialog-launch").dialog();
			});
		}
	});

	ccm_searchActivatePostFunction[searchInstance] = function() {
		ccm_alSetupCheckboxes(searchInstance);
		ccm_alSetupSelectFiles();
	}
	
	
	// setup upload form
}

ccm_alSetupSingleUploadForm = function() {
	jQuery(".ccm-file-manager-submit-single").submit(function() {  
		jQuery(this).attr('target', ccm_alProcessorTarget);
		ccm_alSubmitSingle(jQuery(this).get(0));	 
	});
}

ccm_activateFileSelectors = function() {
	jQuery(".ccm-file-manager-launch").unbind();
	jQuery(".ccm-file-manager-launch").click(function() {
		ccm_alLaunchSelectorFileManager(jQuery(this).parent().attr('ccm-file-manager-field'));	
	});
}

ccm_alLaunchSelectorFileManager = function(selector) {
	ccm_alActiveAssetField = selector;
	var filterStr = "";
	
	var types = jQuery('#' + selector + '-fm-display input.ccm-file-manager-filter');
	if (types.length) {
		for (i = 0; i < types.length; i++) {
			filterStr += '&' + jQuery(types[i]).attr('name') + '=' + jQuery(types[i]).attr('value');		
		}
	}
	
	ccm_launchFileManager(filterStr);
}

// public method - do not remove or rename
ccm_launchFileManager = function(filters) {
	jQuery.fn.dialog.open({
		width: '90%',
		height: '70%',
		modal: false,
		href: CCM_TOOLS_PATH + "/files/search_dialog?ocID=" + CCM_CID + "&search=1" + filters,
		title: ccmi18n_filemanager.title
	});
}

ccm_launchFileSetPicker = function(fsID) {
	jQuery.fn.dialog.open({
		width: 500,
		height: 160,
		modal: false,
		href: CCM_TOOLS_PATH + '/files/pick_set?oldFSID=' + fsID,
		title: ccmi18n_filemanager.sets				
	});
}

ccm_alSubmitSetsForm = function(searchInstance) {
	ccm_deactivateSearchResults(searchInstance);
	jQuery.fn.dialog.showLoader();
	jQuery("#ccm-" + searchInstance + "-add-to-set-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		jQuery.fn.dialog.hideLoader();		
		jQuery("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
			jQuery("#ccm-" + searchInstance + "-sets-search-wrapper").load(CCM_TOOLS_PATH + '/files/search_sets_reload', {'searchInstance': searchInstance}, function() {
				ccm_parseAdvancedSearchResponse(resp, searchInstance);
			});
		});
	});
}

ccm_alSubmitPasswordForm = function(searchInstance) {
	ccm_deactivateSearchResults(searchInstance);
	jQuery("#ccm-" + searchInstance + "-password-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		jQuery("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
			ccm_parseAdvancedSearchResponse(resp, searchInstance);
		});
	});
}

ccm_alSubmitStorageForm = function(searchInstance) {
	ccm_deactivateSearchResults(searchInstance);
	jQuery("#ccm-" + searchInstance + "-storage-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		jQuery("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
			ccm_parseAdvancedSearchResponse(resp, searchInstance);
		});
	});
}

ccm_alSubmitPermissionsForm = function(searchInstance) {
	ccm_deactivateSearchResults(searchInstance);
	jQuery("#ccm-" + searchInstance + "-permissions-form").ajaxSubmit(function(resp) {
		jQuery.fn.dialog.closeTop();
		jQuery("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
			ccm_parseAdvancedSearchResponse(resp, searchInstance);
		});
	});
}

		
ccm_alSetupSetsForm = function(searchInstance) {
	// activate file set search
	jQuery('#fsAddToSearchName').liveUpdate('ccm-file-search-add-to-sets-list', 'fileset');

	// Setup the tri-state checkboxes
	jQuery(".ccm-file-set-add-cb a").each(function() {
		var cb = jQuery(this);
		var startingState = cb.attr("ccm-tri-state-startup");
		jQuery(this).click(function() {
			var selectedState = jQuery(this).attr("ccm-tri-state-selected");
			var toSetState = 0;
			switch(selectedState) {
				case '0':
					if (startingState == '1') {
						toSetState = '1';
					} else {
						toSetState = '2';
					}
					break;
				case '1':
					toSetState = '2';
					break;
				case '2':
					toSetState = '0';
					break;
			}
			
			jQuery(this).attr('ccm-tri-state-selected', toSetState);
			jQuery(this).find('input').val(toSetState);
			jQuery(this).find('img').attr('src', CCM_IMAGE_PATH + '/checkbox_state_' + toSetState + '.png');
		});
	});
	jQuery("#ccm-" + searchInstance + "-add-to-set-form").submit(function() {
		ccm_alSubmitSetsForm(searchInstance);
		return false;
	});
}

ccm_alSetupPasswordForm = function() {
	jQuery("#ccm-file-password-form").submit(function() {
		ccm_alSubmitPasswordForm();
		return false;
	});
}	
ccm_alRescanFiles = function() {
	var turl = CCM_TOOLS_PATH + '/files/rescan?';
	var files = arguments;
	for (i = 0; i < files.length; i++) {
		turl += 'fID[]=' + files[i] + '&';
	}
	jQuery.fn.dialog.open({
		title: ccmi18n_filemanager.rescan,
		href: turl,
		width: 350,
		modal: false,
		height: 200,
		onClose: function() {
			if (files.length == 1) {
				jQuery('#ccm-file-properties-wrapper').html('');
				jQuery.fn.dialog.showLoader();
				
				// open the properties window for this bad boy.
				jQuery("#ccm-file-properties-wrapper").load(CCM_TOOLS_PATH + '/files/properties?fID=' + files[0] + '&reload=1', false, function() {
					jQuery.fn.dialog.hideLoader();
					jQuery(this).find(".dialog-launch").dialog();

				});				
			}
		}
	});
}

	
ccm_alSelectPermissionsEntity = function(selector, id, name) {
	var html = jQuery('#ccm-file-permissions-entity-base').html();
	jQuery('#ccm-file-permissions-entities-wrapper').append('<div class="ccm-file-permissions-entity">' + html + '<\/div>');
	var p = jQuery('.ccm-file-permissions-entity');
	var ap = p[p.length - 1];
	jQuery(ap).find('h2 span').html(name);
	jQuery(ap).find('input[type=hidden]').val(selector + '_' + id);
	jQuery(ap).find('input[type=radio]').each(function() {
		jQuery(this).attr('name', jQuery(this).attr('name') + '_' + selector + '_' + id);
	});
	jQuery(ap).find('div.ccm-file-access-extensions input[type=checkbox]').each(function() {
		jQuery(this).attr('name', jQuery(this).attr('name') + '_' + selector + '_' + id + '[]');
	});
	
	ccm_alActivateFilePermissionsSelector();	
}

ccm_alActivateFilePermissionsSelector = function() {
	jQuery("tr.ccm-file-access-add input").unbind();
	jQuery("tr.ccm-file-access-add input").click(function() {
		var p = jQuery(this).parents('div.ccm-file-permissions-entity')[0];
		if (jQuery(this).val() == ccmi18n_filemanager.PTYPE_CUSTOM) {
			jQuery(p).find('div.ccm-file-access-add-extensions').show();				
		} else {
			jQuery(p).find('div.ccm-file-access-add-extensions').hide();				
		}
	});
	jQuery("tr.ccm-file-access-file-manager input").click(function() {
		var p = jQuery(this).parents('div.ccm-file-permissions-entity')[0];
		if (jQuery(this).val() != ccmi18n_filemanager.PTYPE_NONE) {
			jQuery(p).find('tr.ccm-file-access-add').show();				
			jQuery(p).find('tr.ccm-file-access-edit').show();				
			jQuery(p).find('tr.ccm-file-access-admin').show();
			//jQuery(p).find('div.ccm-file-access-add-extensions').show();				
		} else {
			jQuery(p).find('tr.ccm-file-access-add').hide();				
			jQuery(p).find('tr.ccm-file-access-edit').hide();				
			jQuery(p).find('tr.ccm-file-access-admin').hide();				
			jQuery(p).find('div.ccm-file-access-add-extensions').hide();				
		}
	});


	jQuery("a.ccm-file-permissions-remove").click(function() {
		jQuery(this).parent().parent().fadeOut(100, function() {
			jQuery(this).remove();
		});
	});
	jQuery("input[name=toggleCanAddExtension]").unbind();
	jQuery("input[name=toggleCanAddExtension]").click(function() {
		var ext = jQuery(this).parent().parent().find('div.ccm-file-access-extensions');
		
		if (jQuery(this).attr('checked') == 1) {
			ext.find('input').attr('checked', true);
		} else {
			ext.find('input').attr('checked', false);
		}
	});
}

ccm_alSetupVersionSelector = function() {
	jQuery("#ccm-file-versions-grid input[type=radio]").click(function() {
		jQuery('#ccm-file-versions-grid tr').removeClass('ccm-file-versions-grid-active');
		
		var trow = jQuery(this).parent().parent();
		var fID = trow.attr('fID');
		var fvID = trow.attr('fvID');
		var postStr = 'task=approve_version&fID=' + fID + '&fvID=' + fvID;
		jQuery.post(CCM_TOOLS_PATH + '/files/properties', postStr, function(resp) {
			trow.addClass('ccm-file-versions-grid-active');
			trow.find('td').show('highlight', {
				color: '#FFF9BB'
			});
		});
	});
	
	jQuery(".ccm-file-versions-remove").click(function() {
		var trow = jQuery(this).parent().parent();
		var fID = trow.attr('fID');
		var fvID = trow.attr('fvID');
		var postStr = 'task=delete_version&fID=' + fID + '&fvID=' + fvID;
		jQuery.post(CCM_TOOLS_PATH + '/files/properties', postStr, function(resp) {
			trow.fadeOut(200, function() {
				trow.remove();
			});
		});
		return false;
	});
}

ccm_alDeleteFiles = function(searchInstance) {
	jQuery("#ccm-" + searchInstance + "-delete-form").ajaxSubmit(function(resp) {
		ccm_parseJSON(resp, function() {	
			jQuery.fn.dialog.closeTop();
			ccm_deactivateSearchResults(searchInstance);
			jQuery("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
				ccm_parseAdvancedSearchResponse(resp, searchInstance);
			});
		});
	});
}

ccm_alDuplicateFiles = function(searchInstance) {
	jQuery("#ccm-" + searchInstance + "-duplicate-form").ajaxSubmit(function(resp) {
		ccm_parseJSON(resp, function() {	
			jQuery.fn.dialog.closeTop();
			ccm_deactivateSearchResults(searchInstance);
			var r = eval('(' + resp + ')');

			jQuery("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
				ccm_parseAdvancedSearchResponse(resp, searchInstance);
				var highlight = new Array();
				for (i = 0; i < r.fID.length; i++ ){
					fID = r.fID[i];
					ccm_uploadedFiles.push(fID);
					highlight.push(fID);
				}
				ccm_alRefresh(highlight, searchInstance);
				ccm_filesUploadedDialog(searchInstance);				
			});
		});
	});
}

ccm_alSetupSelectFiles = function() {
	jQuery('.ccm-file-list').unbind();
	/*
	jQuery('.ccm-file-list').click(function(e){
		e.stopPropagation();
		if (jQuery(e.target).is('img.ccm-star')) {	
			var fID = jQuery(e.target).parents('tr.ccm-list-record')[0].id;
			fID = fID.substring(3);
			ccm_starFile(e.target,fID);
		}
		else{
			jQuery(e.target).parents('tr.ccm-list-record').each(function(){
				ccm_alActivateMenu(jQuery(this), e);		
			});
		}
	});
	*/
	
	jQuery('.ccm-file-list tr.ccm-list-record').click(function(e) {
		e.stopPropagation();
		ccm_alActivateMenu(jQuery(this), e);
	});
	jQuery('.ccm-file-list img.ccm-star').click(function(e) {
		e.stopPropagation();
		var fID = jQuery(e.target).parents('tr.ccm-list-record')[0].id;
		fID = fID.substring(3);
		ccm_starFile(e.target,fID);
	});
	jQuery("div.ccm-file-list-thumbnail-image img").hover(function(e) { 
		var fID = jQuery(this).parent().attr('fID');
		var obj = jQuery('#fID' + fID + 'hoverThumbnail'); 
		if (obj.length > 0) { 
			var tdiv = obj.find('div');
			var pos = obj.position();
			tdiv.css('top', pos.top);
			tdiv.css('left', pos.left);
			tdiv.show();
		}
	}, function() {
		var fID = jQuery(this).parent().attr('fID');
		var obj = jQuery('#fID' + fID + 'hoverThumbnail');
		var tdiv = obj.find('div');
		tdiv.hide(); 
	});
}

ccm_alSetupCheckboxes = function(searchInstance) {
	jQuery("#ccm-" + searchInstance + "-list-cb-all").unbind();	
	jQuery("#ccm-" + searchInstance + "-list-cb-all").click(function() {
		ccm_hideMenus();
		if (jQuery(this).attr('checked') == true) {
			jQuery('#ccm-' + searchInstance + '-search-results td.ccm-file-list-cb input[type=checkbox]').attr('checked', true);
			jQuery("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', false);
		} else {
			jQuery('#ccm-' + searchInstance + '-search-results td.ccm-file-list-cb input[type=checkbox]').attr('checked', false);
			jQuery("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', true);
		}
	});
	jQuery("#ccm-" + searchInstance + "-search-results td.ccm-file-list-cb input[type=checkbox]").click(function(e) {
		e.stopPropagation();
		ccm_hideMenus();
		ccm_alRescanMultiFileMenu(searchInstance);
	});
	jQuery("#ccm-" + searchInstance + "-search-results td.ccm-file-list-cb").click(function(e) {
		e.stopPropagation();
		ccm_hideMenus();
		jQuery(this).find('input[type=checkbox]').click();
		ccm_alRescanMultiFileMenu(searchInstance);
	});
	
	// if we're not in the dashboard, add to the multiple operations select menu
	if (ccm_alLaunchType[searchInstance] != 'DASHBOARD' && ccm_alLaunchType[searchInstance] != 'BROWSE') {
		var chooseText = ccmi18n_filemanager.select;
		jQuery("#ccm-" + searchInstance + "-list-multiple-operations option:eq(0)").after("<option value=\"choose\">" + chooseText + "</option>");
	}
	jQuery("#ccm-" + searchInstance + "-list-multiple-operations").change(function() {
		var action = jQuery(this).val();
		var fIDstring = ccm_alGetSelectedFileIDs(searchInstance);
		switch(action) {
			case 'choose':
				var fIDs = new Array();
				jQuery("#ccm-" + searchInstance + "-search-results td.ccm-file-list-cb input[type=checkbox]:checked").each(function() {
					fIDs.push(jQuery(this).val());
				});
				ccm_alSelectFile(fIDs, true);
				break;
			case "delete":
				jQuery.fn.dialog.open({
					width: 500,
					height: 400,
					modal: false,
					href: CCM_TOOLS_PATH + '/files/delete?' + fIDstring + '&searchInstance=' + searchInstance,
					title: ccmi18n_filemanager.deleteFile				
				});
				break;
			case "duplicate":
				jQuery.fn.dialog.open({
					width: 500,
					height: 400,
					modal: false,
					href: CCM_TOOLS_PATH + '/files/duplicate?' + fIDstring + '&searchInstance=' + searchInstance,
					title: ccmi18n_filemanager.duplicateFile				
				});
				break;
			case "sets":
				jQuery.fn.dialog.open({
					width: 500,
					height: 400,
					modal: false,
					href: CCM_TOOLS_PATH + '/files/add_to?' + fIDstring + '&searchInstance=' + searchInstance,
					title: ccmi18n_filemanager.sets				
				});
				break;
			case "properties": 
				jQuery.fn.dialog.open({
					width: 690,
					height: 440,
					modal: false,
					href: CCM_TOOLS_PATH + '/files/bulk_properties?' + fIDstring + '&searchInstance=' + searchInstance,
					title: ccmi18n.properties				
				});
				break;				
			case "rescan":
				jQuery.fn.dialog.open({
					width: 350,
					height: 200,
					modal: false,
					href: CCM_TOOLS_PATH + '/files/rescan?' + fIDstring + '&searchInstance=' + searchInstance,
					title: ccmi18n_filemanager.rescan,
					onClose: function() {
						jQuery("#ccm-" + searchInstance + "-advanced-search").submit();			
					}
				});
				break;
			case "download":
				window.frames[ccm_alProcessorTarget].location = CCM_TOOLS_PATH + '/files/download?' + fIDstring;
				break;
		}
		
		jQuery(this).get(0).selectedIndex = 0;
	});

	// activate the file sets checkboxes
	ccm_alSetupFileSetSearch(searchInstance);
}

ccm_alSetupFileSetSearch = function(searchInstance) {
	jQuery(".ccm-" + searchInstance + "-search-advanced-sets-cb input[type=checkbox]").unbind();
	jQuery(".ccm-" + searchInstance + "-search-advanced-sets-cb input[type=checkbox]").click(function() {
		jQuery("input[name=fsIDNone][instance=" + searchInstance + "]").attr('checked', false);
		jQuery("#ccm-" + searchInstance + "-advanced-search").submit();
	});
	
	// activate file set search
	jQuery('div.ccm-file-sets-search-wrapper-input input').liveUpdate('ccm-file-search-advanced-sets-list', 'fileset');
	
	jQuery("input[name=fsIDNone][instance=" + searchInstance + "]").unbind();
	jQuery("input[name=fsIDNone][instance=" + searchInstance + "]").click(function() {
		if (jQuery(this).attr('checked')) {
			jQuery(".ccm-" + searchInstance + "-search-advanced-sets-cb input[type=checkbox]").attr('checked', false);
			jQuery(".ccm-" + searchInstance + "-search-advanced-sets-cb input[type=checkbox]").attr('disabled', true);
		} else {
			jQuery(".ccm-" + searchInstance + "-search-advanced-sets-cb input[type=checkbox]").attr('disabled', false);
		}
		jQuery("#ccm-" + searchInstance + "-advanced-search").submit();
	});
}

ccm_alGetSelectedFileIDs = function(searchInstance) {
	var fidstr = '';
	jQuery("#ccm-" + searchInstance + "-search-results td.ccm-file-list-cb input[type=checkbox]:checked").each(function() {
		fidstr += 'fID[]=' + jQuery(this).val() + '&';
	});
	return fidstr;
}

ccm_alRescanMultiFileMenu = function(searchInstance) {
	if (jQuery("#ccm-" + searchInstance + "-search-results td.ccm-file-list-cb input[type=checkbox]:checked").length > 0) {
		jQuery("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', false);
	} else {
		jQuery("#ccm-" + searchInstance + "-list-multiple-operations").attr('disabled', true);
	}
}

ccm_alSetupFileProcessor = function() {
	if (ccm_alProcessorTarget != '') {
		return false;
	}
	
	var ts = parseInt(new Date().getTime().toString().substring(0, 10)); 
	var ifr; 
	try { //IE7 hack
	  ifr = document.createElement('<iframe name="ccm-al-upload-processor'+ts+'">');
	} catch (ex) {
	  ifr = document.createElement('iframe');
	}	
	ifr.id = 'ccm-al-upload-processor' + ts;
	ifr.name = 'ccm-al-upload-processor' + ts;
	ifr.style.border='0px';
	ifr.style.width='0px';
	ifr.style.height='0px';
	ifr.style.display = "none";
	document.body.appendChild(ifr);
	
	if (ccm_alDebug) {
		ccm_alProcessorTarget = "_blank";
	} else {
		ccm_alProcessorTarget = 'ccm-al-upload-processor' + ts;
	}
}

ccm_alSubmitSingle = function(form) {
	if (jQuery(form).find(".ccm-al-upload-single-file").val() == '') { 
		alert(ccmi18n_filemanager.uploadErrorChooseFile);
		return false;
	} else { 
		jQuery(form).find('.ccm-al-upload-single-submit').hide();
		jQuery(form).find('.ccm-al-upload-single-loader').show();
	}
}

ccm_alResetSingle = function () {
	jQuery('.ccm-al-upload-single-file').val('');
	jQuery('.ccm-al-upload-single-loader').hide();
	jQuery('.ccm-al-upload-single-submit').show();
}

var ccm_uploadedFiles=[];
ccm_filesUploadedDialog = function(searchInstance) { 
	if(document.getElementById('ccm-file-upload-multiple-tab')) 
		jQuery.fn.dialog.closeTop()
	var fIDstring='';
	for( var i=0; i< ccm_uploadedFiles.length; i++ )
		fIDstring=fIDstring+'&fID[]='+ccm_uploadedFiles[i];
	jQuery.fn.dialog.open({
		width: 690,
		height: 440,
		modal: false,
		href: CCM_TOOLS_PATH + '/files/bulk_properties/?'+fIDstring + '&uploaded=true&searchInstance=' + searchInstance,
		onClose: function() {
			ccm_deactivateSearchResults(searchInstance);
			jQuery("#ccm-" + searchInstance + "-advanced-search").ajaxSubmit(function(resp) {
				ccm_parseAdvancedSearchResponse(resp, searchInstance);
			});
		},
		title: ccmi18n_filemanager.uploadComplete				
	});
	ccm_uploadedFiles=[];
}

ccm_alSetupUploadDetailsForm = function(searchInstance) {
	jQuery("#ccm-" + searchInstance + "-update-uploaded-details-form").submit(function() {
		ccm_alSubmitUploadDetailsForm(searchInstance);
		return false;
	});
}

ccm_alSubmitUploadDetailsForm = function(searchInstance) {
	jQuery.fn.dialog.showLoader();
	jQuery("#ccm-" + searchInstance + "-update-uploaded-details-form").ajaxSubmit(function(r1) {
		var r1a = eval('(' + r1 + ')');
		var form = jQuery("#ccm-" + searchInstance + "-advanced-search");
		if (form.length > 0) {
			form.ajaxSubmit(function(resp) {
				jQuery("#ccm-" + searchInstance + "-sets-search-wrapper").load(CCM_TOOLS_PATH + '/files/search_sets_reload', {'searchInstance': searchInstance}, function() {
					jQuery.fn.dialog.hideLoader();
					jQuery.fn.dialog.closeTop();
					ccm_parseAdvancedSearchResponse(resp, searchInstance);
					ccm_alHighlightFileIDArray(r1a);
				});
			});
		} else {
			jQuery.fn.dialog.hideLoader();
			jQuery.fn.dialog.closeTop();
		}
	});
}

ccm_alRefresh = function(highlightFIDs, searchInstance, fileSelector) {
	var ids = highlightFIDs;
	ccm_deactivateSearchResults(searchInstance);
	jQuery("#ccm-" + searchInstance + "-search-results").load(CCM_TOOLS_PATH + '/files/search_results', {
		'ccm_order_by': 'fvDateAdded',
		'ccm_order_dir': 'desc', 
		'fileSelector': fileSelector,
		'searchInstance': searchInstance
	}, function() {
		ccm_activateSearchResults(searchInstance);
		if (ids != false) {
			ccm_alHighlightFileIDArray(ids);
		}
		ccm_alSetupSelectFiles();

	});
}

ccm_alHighlightFileIDArray = function(ids) {
	for (i = 0; i < ids.length; i++) {
		var td = jQuery('tr[fID=' + ids[i] + '] td');
		var oldBG = td.css('backgroundColor');
		td.animate({ backgroundColor: '#FFF9BB'}, { queue: true, duration: 1000 }).animate( {backgroundColor: oldBG}, 500);
	}
}

ccm_alSelectFile = function(fID) {
	
	if (typeof(ccm_chooseAsset) == 'function') {
		var qstring = '';
		if (typeof(fID) == 'object') {
			for (i = 0; i < fID.length; i++) {
				qstring += 'fID[]=' + fID[i] + '&';
			}
		} else {
			qstring += 'fID=' + fID;
		}
		
		jQuery.getJSON(CCM_TOOLS_PATH + '/files/get_data.php?' + qstring, function(resp) {
			ccm_parseJSON(resp, function() {
				for(i = 0; i < resp.length; i++) {
					ccm_chooseAsset(resp[i]);
				}
				jQuery.fn.dialog.closeTop();
			});
		});
		
	} else {
		if (typeof(fID) == 'object') {
			for (i = 0; i < fID.length; i++) {
				ccm_triggerSelectFile(fID[i]);
			}
		} else {
			ccm_triggerSelectFile(fID);
		}
		jQuery.fn.dialog.closeTop();	
	}

}

ccm_alActivateMenu = function(obj, e) {
	
	// Is this a file that's already been chosen that we're selecting?
	// If so, we need to offer the reset switch
	
	var selectedFile = jQuery(obj).find('div[ccm-file-manager-field]');
	var selector = '';
	if(selectedFile.length > 0) {
		selector = selectedFile.attr('ccm-file-manager-field');
	}
	ccm_hideMenus();
	
	var fID = jQuery(obj).attr('fID');
	var searchInstance = jQuery(obj).attr('ccm-file-manager-instance');

	// now, check to see if this menu has been made
	var bobj = document.getElementById("ccm-al-menu" + fID + searchInstance + selector);
	
	// This immediate click mode has promise, but it's annoying more than it's helpful
	/*
	if (ccm_alLaunchType != 'DASHBOARD' && selector == '') {
		// then we are in file list mode in the site, which means we 
		// we don't give out all the options in the list
		ccm_alSelectFile(fID);
		return;
	}
	*/
	
	if (!bobj) {
		// create the 1st instance of the menu
		el = document.createElement("DIV");
		el.id = "ccm-al-menu" + fID + searchInstance + selector;
		el.className = "ccm-menu";
		el.style.display = "none";
		document.body.appendChild(el);
		
		var filepath = jQuery(obj).attr('al-filepath'); 
		bobj = jQuery("#ccm-al-menu" + fID + searchInstance + selector);
		bobj.css("position", "absolute");
		
		//contents  of menu
		var html = '<div class="ccm-menu-tl"><div class="ccm-menu-tr"><div class="ccm-menu-t"></div></div></div>';
		html += '<div class="ccm-menu-l"><div class="ccm-menu-r">';
		html += '<ul>';
		if (ccm_alLaunchType[searchInstance] != 'DASHBOARD' && ccm_alLaunchType[searchInstance] != 'BROWSE') {
			// if we're launching this at the selector level, that means we've already chosen a file, and this should instead launch the library
			var onclick = (selectedFile.length > 0) ? 'ccm_alLaunchSelectorFileManager(\'' + selector + '\')' : 'ccm_alSelectFile(' + fID + ')';
			var chooseText = (selectedFile.length > 0) ? ccmi18n_filemanager.chooseNew : ccmi18n_filemanager.select;
			html += '<li><a class="ccm-icon" dialog-modal="false" dialog-width="90%" dialog-height="70%" dialog-title="' + ccmi18n_filemanager.select + '" id="menuSelectFile' + fID + '" href="javascript:void(0)" onclick="' + onclick + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/add.png)">'+ chooseText + '<\/span><\/a><\/li>';
		}
		if (selectedFile.length > 0) {
			html += '<li><a class="ccm-icon" href="javascript:void(0)" id="menuClearFile' + fID + searchInstance + selector + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/remove.png)">'+ ccmi18n_filemanager.clear + '<\/span><\/a><\/li>';
		}
		
		if (ccm_alLaunchType[searchInstance] != 'DASHBOARD'  && ccm_alLaunchType[searchInstance] != 'BROWSE' && selectedFile.length > 0) {
			html += '<li class="header"></li>';	
		}
		if (jQuery(obj).attr('ccm-file-manager-can-view') == '1') {
			html += '<li><a class="ccm-icon dialog-launch" dialog-modal="false" dialog-width="90%" dialog-height="75%" dialog-title="' + ccmi18n_filemanager.view + '" id="menuView' + fID + '" href="' + CCM_TOOLS_PATH + '/files/view?fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/design_small.png)">'+ ccmi18n_filemanager.view + '<\/span><\/a><\/li>';
		} else {
			html += '<li><a class="ccm-icon" id="menuDownload' + fID + '" target="' + ccm_alProcessorTarget + '" href="' + CCM_TOOLS_PATH + '/files/download?fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/design_small.png)">'+ ccmi18n_filemanager.download + '<\/span><\/a><\/li>';	
		}
		if (jQuery(obj).attr('ccm-file-manager-can-edit') == '1') {
			html += '<li><a class="ccm-icon dialog-launch" dialog-modal="false" dialog-width="90%" dialog-height="75%" dialog-title="' + ccmi18n_filemanager.edit + '" id="menuEdit' + fID + '" href="' + CCM_TOOLS_PATH + '/files/edit?fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/edit_small.png)">'+ ccmi18n_filemanager.edit + '<\/span><\/a><\/li>';
		}
		html += '<li><a class="ccm-icon dialog-launch" dialog-modal="false" dialog-width="680" dialog-height="450" dialog-title="' + ccmi18n_filemanager.properties + '" id="menuProperties' + fID + '" href="' + CCM_TOOLS_PATH + '/files/properties?searchInstance=' + searchInstance + '&fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/wrench.png)">'+ ccmi18n_filemanager.properties + '<\/span><\/a><\/li>';
		if (jQuery(obj).attr('ccm-file-manager-can-replace') == '1') {
			html += '<li><a class="ccm-icon dialog-launch" dialog-modal="false" dialog-width="300" dialog-height="250" dialog-title="' + ccmi18n_filemanager.replace + '" id="menuFileReplace' + fID + '" href="' + CCM_TOOLS_PATH + '/files/replace?searchInstance=' + searchInstance + '&fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/paste_small.png)">'+ ccmi18n_filemanager.replace + '<\/span><\/a><\/li>';
		}
		if (jQuery(obj).attr('ccm-file-manager-can-duplicate') == '1') {
			html += '<li><a class="ccm-icon" id="menuFileDuplicate' + fID + '" href="javascript:void(0)" onclick="ccm_alDuplicateFile(' + fID + ',\'' + searchInstance + '\')"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/add.png)">'+ ccmi18n_filemanager.duplicate + '<\/span><\/a><\/li>';
		}
		html += '<li><a class="ccm-icon dialog-launch" dialog-modal="false" dialog-width="500" dialog-height="400" dialog-title="' + ccmi18n_filemanager.sets + '" id="menuFileSets' + fID + '" href="' + CCM_TOOLS_PATH + '/files/add_to?searchInstance=' + searchInstance + '&fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/window_new.png)">'+ ccmi18n_filemanager.sets + '<\/span><\/a><\/li>';
		if (jQuery(obj).attr('ccm-file-manager-can-admin') == '1' || jQuery(obj).attr('ccm-file-manager-can-delete') == '1') {
			html += '<li class="header"></li>';
		}
		if (jQuery(obj).attr('ccm-file-manager-can-admin') == '1') {
			html += '<li><a class="ccm-icon dialog-launch" dialog-modal="false" dialog-width="400" dialog-height="380" dialog-title="' + ccmi18n_filemanager.permissions + '" id="menuFilePermissions' + fID + '" href="' + CCM_TOOLS_PATH + '/files/permissions?searchInstance=' + searchInstance + '&fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/permissions_small.png)">'+ ccmi18n_filemanager.permissions + '<\/span><\/a><\/li>';
		}
		if (jQuery(obj).attr('ccm-file-manager-can-delete') == '1') {
			html += '<li><a class="ccm-icon dialog-launch" dialog-modal="false" dialog-width="500" dialog-height="400" dialog-title="' + ccmi18n_filemanager.deleteFile + '" id="menuDeleteFile' + fID + '" href="' + CCM_TOOLS_PATH + '/files/delete?searchInstance=' + searchInstance + '&fID=' + fID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/delete_small.png)">'+ ccmi18n_filemanager.deleteFile + '<\/span><\/a><\/li>';
		}
		html += '</ul>';
		html += '</div></div>';
		html += '<div class="ccm-menu-bl"><div class="ccm-menu-br"><div class="ccm-menu-b"></div></div></div>';
		bobj.append(html);
		
		jQuery("#ccm-al-menu" + fID + searchInstance + selector + " a.dialog-launch").dialog();
		
		jQuery('a#menuClearFile' + fID + searchInstance + selector).click(function(e) {
			ccm_clearFile(e, selector);
			ccm_hideMenus();
		});

	} else {
		bobj = jQuery("#ccm-al-menu" + fID + searchInstance + selector);
	}
	
	ccm_fadeInMenu(bobj, e);

}

ccm_alSelectNone = function() {
	ccm_hideMenus();
}

var checkbox_status = false;
toggleCheckboxStatus = function(form) {
	if(checkbox_status) {
		for (i = 0; i < form.elements.length; i++) {
			if (form.elements[i].type == "checkbox") {
				form.elements[i].checked = false;
			}
		}	
		checkbox_status = false;
	}
	else {
		for (i = 0; i < form.elements.length; i++) {
			if (form.elements[i].type == "checkbox") {
				form.elements[i].checked = true;
			}
		}	
		checkbox_status = true;	
	}
}	

ccm_alDuplicateFile = function(fID, searchInstance) {
	var postStr = 'fID=' + fID + '&searchInstance=' + searchInstance;
	
	jQuery.post(CCM_TOOLS_PATH + '/files/duplicate', postStr, function(resp) {
		var r = eval('(' + resp + ')');
		
		if (r.error == 1) {
		 	ccmAlert.notice(ccmi18n.error, r.message);		
		 	return false;
		 }
		
		
		var highlight = new Array();
		if (r.fID) {
			highlight.push(r.fID);
			ccm_alRefresh(highlight, searchInstance);
			ccm_uploadedFiles.push(r.fID);
			ccm_filesUploadedDialog(searchInstance);
		}
	});
}

ccm_alSelectMultipleIncomingFiles = function(obj) {
	if (jQuery(obj).attr('checked')) {
		jQuery("input.ccm-file-select-incoming").attr('checked', true);
	} else {
		jQuery("input.ccm-file-select-incoming").attr('checked', false);
	}
}

ccm_starFile = function (img,fID) {				
	var action = '';
	if (jQuery(img).attr('src').indexOf(CCM_STAR_STATES.unstarred) != -1) {
		jQuery(img).attr('src',jQuery(img).attr('src').replace(CCM_STAR_STATES.unstarred,CCM_STAR_STATES.starred));
		action = 'star';
	}
	else {
		jQuery(img).attr('src',jQuery(img).attr('src').replace(CCM_STAR_STATES.starred,CCM_STAR_STATES.unstarred));
		action = 'unstar';
	}
	
	jQuery.post(CCM_TOOLS_PATH + '/' + CCM_STAR_ACTION,{'action':action,'file-id':fID},function(data, textStatus){
		//callback, in case we want to do some post processing
	});
}
