var ccm_pageMenus = new Array();
var ccm_uiLoaded = true;
var ccm_deactivateTimer = false;
var ccm_deactivateTimerTime = 2000;
var ccm_topPaneDeactivated = false;
var ccm_topPaneTargetURL = false;
var ccm_selectedDomID = false;
var ccm_isBlockError = false;
var ccm_blockError = "";
var ccm_menuActivated = false;
var ccm_bcEnabled = false;
var ccm_bcEnabledTimer = false;
var ccm_arrangeMode = false;

ccm_menuInit = function(obj) {
	
	if (CCM_EDIT_MODE && (!CCM_ARRANGE_MODE)) {
		switch(obj.type) {
			case "BLOCK":
				jQuery("#b" + obj.bID + "-" + obj.aID).mouseover(function(e) {
					if (!ccm_menuActivated) {
						ccm_activate(obj, "#b" + obj.bID + "-" + obj.aID);
					}
				});
				break;
			case "AREA":
				jQuery("#a" + obj.aID + "controls").mouseover(function(e) {
				if (!ccm_menuActivated) {
					ccm_activate(obj, "#a" + obj.aID + "controls");
				}
				});
				break;
		}
	}	
}

ccm_showBlockMenu = function(obj, e) {
	ccm_hideMenus();
	e.stopPropagation();
	ccm_menuActivated = true;
	
	// now, check to see if this menu has been made
	var bobj = document.getElementById("ccm-block-menu" + obj.bID + "-" + obj.aID);

	if (!bobj) {
		// create the 1st instance of the menu
		el = document.createElement("DIV");
		el.id = "ccm-block-menu" + obj.bID + "-" + obj.aID;
		el.className = "ccm-menu";
		el.style.display = "none";
		document.body.appendChild(el);
		
		bobj = jQuery("#ccm-block-menu" + obj.bID + "-" + obj.aID);
		bobj.css("position", "absolute");
		
		//contents  of menu
		var html = '<div class="ccm-menu-tl"><div class="ccm-menu-tr"><div class="ccm-menu-t"></div></div></div>';
		html += '<div class="ccm-menu-l"><div class="ccm-menu-r">';
		html += '<ul>';
		//html += '<li class="header"></li>';
		if (obj.canWrite) {
			html += (obj.editInline) ? '<li><a class="ccm-icon" id="menuEdit' + obj.bID + '-' + obj.aID + '" href="' + CCM_DISPATCHER_FILENAME + '?cID=' + CCM_CID + '&bID=' + obj.bID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&isGlobal=' + obj.isGlobal + '&btask=edit#_edit' + obj.bID + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/edit_small.png)">' + ccmi18n.editBlock + '</span></a></li>'
				: '<li><a class="ccm-icon" dialog-title="' + ccmi18n.editBlock + ' ' + obj.btName + '" dialog-modal="false" dialog-on-close="ccm_blockWindowAfterClose()" dialog-width="' + obj.width + '" dialog-height="' + obj.height + '" id="menuEdit' + obj.bID + '-' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_block_popup.php?cID=' + CCM_CID + '&bID=' + obj.bID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&isGlobal=' + obj.isGlobal + '&btask=edit"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/edit_small.png)">' + ccmi18n.editBlock + '</span></a></li>';
		}
		html += '<li><a class="ccm-icon" dialog-title="' + ccmi18n.copyBlockToScrapbook + '" dialog-modal="false" dialog-width="250" dialog-height="100" id="menuAddToScrapbook' + obj.bID + '-' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/pile_manager.php?cID=' + CCM_CID + '&bID=' + obj.bID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&btask=add"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/paste_small.png)">' + ccmi18n.copyBlockToScrapbook + '</span></a></li>';

		if (obj.canArrange) {
			html += '<li><a class="ccm-icon" id="menuArrange' + obj.bID + '-' + obj.aID + '" href="javascript:ccm_arrangeInit()"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/up_down.png)">' + ccmi18n.arrangeBlock + '</span></a></li>';

			//html += '<li><a class="ccm-icon" id="menuArrange' + obj.bID + '-' + obj.aID + '" href="' + CCM_DISPATCHER_FILENAME + '?cID=' + CCM_CID + '&btask=arrange"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/up_down.png)">' + ccmi18n.arrangeBlock + '</span></a></li>';
		}
		if (obj.canDelete) {
			html += '<li><a class="ccm-icon" id="menuDelete' + obj.bID + '-' + obj.aID + '" href="#" onclick="javascript:ccm_deleteBlock(' + obj.bID + ',' + obj.aID + ', \'' + encodeURIComponent(obj.arHandle) + '\', \'' + obj.deleteMessage + '\');return false;"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/delete_small.png)">' + ccmi18n.deleteBlock + '</span></a></li>';
		} 		
		if (obj.canWrite) {
			html += '<li class="header"></li>';
			if (obj.canDesign) {
				html += '<li><a class="ccm-icon" dialog-modal="false" dialog-title="' + ccmi18n.changeBlockBaseStyle + '" dialog-width="450" dialog-height="420" id="menuChangeCSS' + obj.bID + '-' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_block_popup.php?cID=' + CCM_CID + '&bID=' + obj.bID + '&isGlobal=' + obj.isGlobal + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&btask=block_css&modal=true&width=300&height=100" title="' + ccmi18n.changeBlockCSS + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/design_small.png)">' + ccmi18n.changeBlockCSS + '</span></a></li>';
			}
			html += '<li><a class="ccm-icon" dialog-modal="false" dialog-title="' + ccmi18n.changeBlockTemplate + '" dialog-width="300" dialog-height="100" id="menuChangeTemplate' + obj.bID + '-' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_block_popup.php?cID=' + CCM_CID + '&bID=' + obj.bID + '&isGlobal=' + obj.isGlobal + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&btask=template&modal=true&width=300&height=100" title="' + ccmi18n.changeBlockTemplate + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/wrench.png)">' + ccmi18n.changeBlockTemplate + '</span></a></li>';
		}

		if (obj.canModifyGroups || obj.canAliasBlockOut || obj.canSetupComposer) {
			html += '<li class="header"></li>';
		}

		if (obj.canModifyGroups) {
			html += '<li><a title="' + ccmi18n.setBlockPermissions + '" class="ccm-icon" dialog-width="400" dialog-height="380" id="menuBlockGroups' + obj.bID + '-' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_block_popup.php?cID=' + CCM_CID + '&bID=' + obj.bID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&btask=groups" dialog-title="' + ccmi18n.setBlockPermissions + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/permissions_small.png)">' + ccmi18n.setBlockPermissions + '</span></a></li>';
		}
		if (obj.canAliasBlockOut) {
			html += '<li><a class="ccm-icon" dialog-width="550" dialog-height="450" id="menuBlockAliasOut' + obj.bID + '-' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_block_popup.php?cID=' + CCM_CID + '&bID=' + obj.bID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&btask=child_pages" dialog-title="' + ccmi18n.setBlockAlias + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/template_block.png)">' + ccmi18n.setBlockAlias + '</span></a></li>';
		}
		if (obj.canSetupComposer) {
			html += '<li><a class="ccm-icon" dialog-width="300" dialog-modal="false" dialog-height="150" id="menuBlockSetupComposer' + obj.bID + '-' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_block_popup.php?cID=' + CCM_CID + '&bID=' + obj.bID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&btask=composer" dialog-title="' + ccmi18n.setBlockComposerSettings + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/template_block.png)">' + ccmi18n.setBlockComposerSettings + '</span></a></li>';
		}
		

		html += '</ul>';
		html += '</div></div>';
		html += '<div class="ccm-menu-bl"><div class="ccm-menu-br"><div class="ccm-menu-b"></div></div></div>';
		bobj.append(html);
		
		// add dialog elements where necessary
		if (obj.canWrite && (!obj.editInline)) {
			jQuery('a#menuEdit' + obj.bID + '-' + obj.aID).dialog();
			jQuery('a#menuChangeTemplate' + obj.bID + '-' + obj.aID).dialog();
			jQuery('a#menuChangeCSS' + obj.bID + '-' + obj.aID).dialog();	
		}
		if (obj.canAliasBlockOut) {
			jQuery('a#menuBlockAliasOut' + obj.bID + '-' + obj.aID).dialog();
		}
		if (obj.canSetupComposer) {
			jQuery('a#menuBlockSetupComposer' + obj.bID + '-' + obj.aID).dialog();
		}
		if (obj.canModifyGroups) {
			jQuery("#menuBlockGroups" + obj.bID + '-' + obj.aID).dialog();
		}
		jQuery("#menuAddToScrapbook" + obj.bID + '-' + obj.aID).dialog(); 

	} else {
		bobj = jQuery("#ccm-block-menu" + obj.bID + '-' + obj.aID);
	}
	
	ccm_fadeInMenu(bobj, e);

}

ccm_openAreaAddBlock = function(arHandle, addOnly) {
	if (!addOnly) {	
		addOnly = 0;
	}
	
	jQuery.fn.dialog.open({
		title: ccmi18n.blockAreaMenu,
		href: CCM_TOOLS_PATH + '/edit_area_popup.php?cID=' + CCM_CID + '&atask=add&arHandle=' + arHandle + '&addOnly=' + addOnly,
		width: 550,
		modal: false,
		height: 380,
		onClose: function() {
			ccm_activateHeader();
		}
	});
}

ccm_showAreaMenu = function(obj, e) {
	var addOnly = (obj.addOnly)?1:0;

	if (e.shiftKey) {
		ccm_openAreaAddBlock(obj.arHandle, addOnly);
	} else {
		ccm_hideMenus();
		e.stopPropagation();
		ccm_menuActivated = true;
		
		// now, check to see if this menu has been made
		var aobj = document.getElementById("ccm-area-menu" + obj.aID);
		
		if (!aobj) {
			// create the 1st instance of the menu
			el = document.createElement("DIV");
			el.id = "ccm-area-menu" + obj.aID;
			el.className = "ccm-menu";
			el.style.display = "none";
			document.body.appendChild(el);
			
			aobj = jQuery("#ccm-area-menu" + obj.aID);
			aobj.css("position", "absolute");
			
			//contents  of menu
			var html = '<div class="ccm-menu-tl"><div class="ccm-menu-tr"><div class="ccm-menu-t"></div></div></div>';
			html += '<div class="ccm-menu-l"><div class="ccm-menu-r">';
			html += '<ul>';
			//html += '<li class="header"></li>';
			if (obj.canAddBlocks) {
				html += '<li><a class="ccm-icon" dialog-title="' + ccmi18n.addBlockNew + '" dialog-modal="false" dialog-width="550" dialog-height="380" id="menuAddNewBlock' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_area_popup.php?cID=' + CCM_CID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&atask=add&addOnly=' + addOnly + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/add.png)">' + ccmi18n.addBlockNew + '</span></a></li>';
				html += '<li><a class="ccm-icon" dialog-title="' + ccmi18n.addBlockPaste + '" dialog-modal="false" dialog-width="550" dialog-height="380" id="menuAddPaste' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_area_popup.php?cID=' + CCM_CID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&atask=paste&addOnly=' + addOnly + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/paste_small.png)">' + ccmi18n.addBlockPaste + '</span></a></li>';
			}
			if (obj.canAddBlocks && (obj.canDesign || obj.canLayout)) {
				html += '<li class="header"></li>';
			}
			if (obj.canLayout) {
				html += '<li><a class="ccm-icon" dialog-title="' + ccmi18n.addAreaLayout + '" dialog-modal="false" dialog-width="550" dialog-height="280" id="menuAreaLayout' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_area_popup.php?cID=' + CCM_CID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&atask=layout"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/layout_small.png)">' + ccmi18n.addAreaLayout + '</span></a></li>';
			}
			if (obj.canDesign) {
				html += '<li><a class="ccm-icon" dialog-title="' + ccmi18n.changeAreaCSS + '" dialog-modal="false" dialog-width="450" dialog-height="420" id="menuAreaStyle' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_area_popup.php?cID=' + CCM_CID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&atask=design"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/design_small.png)">' + ccmi18n.changeAreaCSS + '</span></a></li>';
			}
			if (obj.canWrite && obj.canModifyGroups) { 
				html += '<li class="header"></li>';			
			}
			if (obj.canModifyGroups) {
				html += '<li><a title="' + ccmi18n.setAreaPermissions + '" dialog-modal="false" class="ccm-icon" dialog-width="580" dialog-height="420" id="menuAreaGroups' + obj.aID + '" href="' + CCM_TOOLS_PATH + '/edit_area_popup.php?cID=' + CCM_CID + '&arHandle=' + encodeURIComponent(obj.arHandle) + '&atask=groups" dialog-title="' + ccmi18n.setAreaPermissions + '"><span style="background-image: url(' + CCM_IMAGE_PATH + '/icons/permissions_small.png)">' + ccmi18n.setAreaPermissions + '</span></a></li>';
			}
			
			html += '</ul>';
			html += '</div></div>';
			html += '<div class="ccm-menu-bl"><div class="ccm-menu-br"><div class="ccm-menu-b"></div></div></div>';
			aobj.append(html);
			
			// add dialog elements where necessary
			if (obj.canAddBlocks) {
				jQuery('a#menuAddNewBlock' + obj.aID).dialog();
				jQuery('a#menuAddPaste' + obj.aID).dialog(); 
			}
			if (obj.canWrite) {
				jQuery('a#menuAreaStyle' + obj.aID).dialog();
				jQuery('a#menuAreaLayout' + obj.aID).dialog();
			}
			if (obj.canModifyGroups) {
				jQuery('a#menuAreaGroups' + obj.aID).dialog();
			}
		
		} else {
			aobj = jQuery("#ccm-area-menu" + obj.aID);
		}

		ccm_fadeInMenu(aobj, e);		

	}
}

ccm_hideHighlighter = function() {
	jQuery("#ccm-highlighter").css('display', 'none');
}

ccm_addError = function(err) {
	if (!ccm_isBlockError) {
		ccm_blockError += '<ul>';
	}
	
	ccm_isBlockError = true;
	ccm_blockError += "<li>" + err + "</li>";;
}

ccm_resetBlockErrors = function() {
	ccm_isBlockError = false;
	ccm_blockError = "";
}

ccm_deleteBlock = function(bID, aID, arHandle, msg) {
	if (confirm(msg)) {
		ccm_mainNavDisableDirectExit();
		// got to grab the message too, eventually
		ccm_hideHighlighter();
		ccm_menuActivated = true;
		jQuery.ajax({
 		type: 'POST',
 		url: CCM_DISPATCHER_FILENAME,
 		data: 'cID=' + CCM_CID + '&ccm_token=' + CCM_SECURITY_TOKEN + '&isAjax=true&btask=remove&bID=' + bID + '&arHandle=' + arHandle,
 		success: function(resp) {
 			ccm_hideHighlighter();
 			jQuery("#b" + bID + '-' + aID).fadeOut(100, function() {
 				ccm_menuActivated = false;
 			});
 			ccmAlert.hud(ccmi18n.deleteBlockMsg, 2000, 'delete_small', ccmi18n.deleteBlock);
 		}});		
	}
}

ccm_hideMenus = function() {
	/* 1st, hide all items w/the css menu class */
	jQuery("div.ccm-menu").hide();
	ccm_menuActivated = false;
}

ccm_parseBlockResponse = function(r, currentBlockID, task) {
	try { 
		resp = eval('(' + r + ')');
		if (resp.error == true) {
			var message = '<ul>'
			for (i = 0; i < resp.response.length; i++) {						
				message += '<li>' + resp.response[i] + '<\/li>';
			}
			message += '<\/ul>';
			ccmAlert.notice(ccmi18n.error, message);
		} else {
			ccm_blockWindowClose();
			var isGlobal = 0;
			if (resp.isGlobalBlock == true) {
				isGlobal = 1;
			}
			if (resp.cID) {
				cID = resp.cID; 
			} else {
				cID = CCM_CID;
			}
			var action = CCM_TOOLS_PATH + '/edit_block_popup?cID=' + cID + '&isGlobal=' + isGlobal + '&bID=' + resp.bID + '&arHandle=' + encodeURIComponent(resp.arHandle) + '&btask=view_edit_mode';	 
			jQuery.get(action, 		
				function(r) { 
					if (jQuery("#ccm-scrapbook-list").length > 0) {
						window.location.reload();
					} 
					if (task == 'add') {
						if (jQuery("#a" + resp.aID + " div.ccm-area-styles-a"+ resp.aID).length > 0) {
							jQuery("#a" + resp.aID + " div.ccm-area-styles-a"+ resp.aID).append(r);
						} else {
							jQuery("#a" + resp.aID).append(r);
						}
					} else {
						jQuery('#b' + currentBlockID + '-' + resp.aID).before(r).remove();
					}
					jQuery.fn.dialog.hideLoader();
					ccm_mainNavDisableDirectExit();
					setTimeout(function() {
						ccm_menuActivated = false;
					}, 200);
					if (task == 'add') {
						ccmAlert.hud(ccmi18n.addBlockMsg, 2000, 'add', ccmi18n.addBlock);
						// second closetop. Not very elegant
						jQuery.fn.dialog.closeTop();
					} else {
						ccmAlert.hud(ccmi18n.updateBlockMsg, 2000, 'success', ccmi18n.updateBlock);
					}
				}
			);
		}
	} catch(e) { 
		ccmAlert.notice(ccmi18n.error, r); 
	}
}

ccm_mainNavDisableDirectExit = function(disableShow) {
	// make sure that exit edit mode is enabled
	jQuery("li.ccm-main-nav-exit-edit-mode-direct").remove();
	if (!disableShow) {
		jQuery("li.ccm-main-nav-exit-edit-mode").show();
	}
}

ccm_setupBlockForm = function(form, currentBlockID, task) {
	form.ajaxForm({
		type: 'POST',
		iframe: true,
		beforeSubmit: function() {
			ccm_hideHighlighter();
			jQuery('input[name=ccm-block-form-method]').val('AJAX');
			jQuery.fn.dialog.showLoader();
			ccm_menuActivated = true;
			return ccm_blockFormSubmit();
		},
		success: function(r) {
			ccm_parseBlockResponse(r, currentBlockID, task);
		}
	});
	
	/*
	
	// this code works better but doesn't work with tinymce?
	form.submit(function() {
		ccm_hideHighlighter();
		jQuery.fn.dialog.showLoader();
		ccm_menuActivated = true;
		if (ccm_blockFormSubmit()) {
			jQuery('input[name=ccm-block-form-method]').val('AJAX');
			jQuery(this).ajaxSubmit({
				success: function(r) {ccm_parseBlockResponse(r, currentBlockID, task); }
			});
		}
		return false;
	});

	*/
}



ccm_activate = function(obj, domID) { 
	if (ccm_topPaneDeactivated) {
		return false;
	}
	
	if (ccm_arrangeMode) {
		return false;
	}
	
	if (ccm_selectedDomID) {
		jQuery(ccm_selectedDomID).removeClass('selected');
	}
	
	aobj = jQuery(domID);
	aobj.addClass('selected');
	ccm_selectedDomID = domID;
	
	offs = aobj.offset();

	/* put highlighter over item. THanks dimensions plugin! */
	
	jQuery("#ccm-highlighter").css("width", aobj.outerWidth());
	jQuery("#ccm-highlighter").css("height", aobj.outerHeight());
	jQuery("#ccm-highlighter").css("top", offs.top);
	jQuery("#ccm-highlighter").css("left", offs.left);
	jQuery("#ccm-highlighter").css("display", "block");
	/*
	jQuery("#ccmMenuHighlighter").mouseover(
		function() {clearTimeout(ccm_deactivateTimer)}
	);
	*/
	jQuery("#ccm-highlighter").unbind('click');
	jQuery("#ccm-highlighter").click(
		function(e) {
			switch(obj.type) {
				case "BLOCK":
					ccm_showBlockMenu(obj, e);
					break;
				case "AREA":
					ccm_showAreaMenu(obj,e);
					break;
			}
		}
	);
}

ccm_editInit = function() {
	ccm_setupHeaderMenu();

	document.write = function() {
		// stupid javascript in html blocks
		void(0);
	}

	jQuery(document.body).append('<div style="position: absolute; display:none" id="ccm-highlighter">&nbsp;</div>');
	jQuery(document).click(function() {ccm_hideMenus();});
	jQuery(document.body).css('user-select', 'none');
	jQuery(document.body).css('-khtml-user-select', 'none');
	jQuery(document.body).css('-webkit-user-select', 'none');
	jQuery(document.body).css('-moz-user-select', 'none');

	jQuery("a").click(function(e) {
		ccm_hideMenus();
		return false;	
	});
	
	/* setup header actions */
	/*
	jQuery('#ccm-main-nav li').each(function(){
		//this.onmouseover=function(){alert('this')}							
	});
	*/
	
	jQuery("a#ccm-nav-mcd").click(function() {
		ccm_showPane(this, CCM_TOOLS_PATH + "/edit_collection_popup.php?ctask=mcd&cID=" + CCM_CID);
	});
	
	jQuery("a#ccm-nav-versions").click(function() {
		ccm_showPane(this, CCM_TOOLS_PATH + "/versions.php?cID=" + CCM_CID);
	});
	
	jQuery("a#ccm-nav-exit-edit").click(function() {
		ccm_showPane(this, CCM_TOOLS_PATH + "/check_in.php?cID=" + CCM_CID);
	});
	
	jQuery("a#ccm-nav-exit-edit-direct").click(function() {
		window.location.href=jQuery(this).attr('href');
	});
	

	jQuery("a#ccm-nav-permissions").click(function() {
		ccm_showPane(this, CCM_TOOLS_PATH + "/edit_collection_popup.php?ctask=edit_permissions&cID=" + CCM_CID);
	});

	jQuery("a#ccm-nav-design").click(function() {
		ccm_showPane(this, CCM_TOOLS_PATH + "/edit_collection_popup.php?ctask=set_theme&cID=" + CCM_CID);
	});

	jQuery("a#ccm-nav-properties").click(function() {
		ccm_showPane(this, CCM_TOOLS_PATH + "/edit_collection_popup.php?ctask=edit_metadata&cID=" + CCM_CID);
	});
	
	jQuery("#ccm-highlighter").mouseout(function() {
		if (!ccm_menuActivated) {
			ccm_hideHighlighter();
		}
	});
		
}

ccm_hidePane = function(onDone) {
	var wrappane = jQuery("#ccm-page-detail");
	jQuery("li.ccm-nav-active").removeClass('ccm-nav-active');
	if (ccm_animEffects) {
		wrappane.fadeOut(60, function() {
			if(!ccm_onPaneCloseObj) ccm_activateSite();
			else ccm_siteActivated = true;
			jQuery(window).unbind('keypress');
			ccm_activateHeader();
			if (typeof onDone == 'function') {
				onDone();
			}
		});
	} else {
		wrappane.hide();
		if(!ccm_onPaneCloseObj) ccm_activateSite();
		else ccm_siteActivated = true;
		jQuery(window).unbind('keypress');
	
		ccm_activateHeader();
	
		if (typeof onDone == 'function') {
			onDone();
		}
	}
}

ccm_deactivateHeader = function(obj) { 
	ccm_topPaneDeactivated = true;
	jQuery("ul#ccm-main-nav").addClass('ccm-pane-open'); 
	if(ccm_dialogOpen) jQuery("ul#ccm-main-nav li").addClass('ccm-nav-rolloversOff');	
	else jQuery("ul#ccm-main-nav li").addClass('ccm-nav-inactive');		
	ccm_hideBreadcrumb();
	if (obj) {
		jQuery(obj).parent().removeClass('ccm-nav-inactive');
		jQuery(obj).parent().addClass('ccm-nav-active');
	}
}

ccm_activateHeader = function() {
	ccm_topPaneDeactivated = false;
	jQuery("ul#ccm-main-nav").removeClass('ccm-pane-open');
	jQuery("ul#ccm-main-nav li").removeClass('ccm-nav-inactive');
	jQuery("ul#ccm-main-nav li").removeClass('ccm-nav-rolloversOff');
}
var ccm_onPaneCloseObj=null;
var ccm_onPaneCloseTargetURL=null;
ccm_showPane = function(obj, targetURL) {
	if (ccm_topPaneDeactivated && ccm_dialogOpen) {
		return false;
	}	
	if(typeof(obj.blur)=='function') obj.blur();
	ccm_onPaneCloseObj=null;
	ccm_onPaneCloseTargetURL=null;
	if (ccm_topPaneDeactivated){
		ccm_onPaneCloseObj=obj;
		ccm_onPaneCloseTargetURL=targetURL;
		ccm_hidePane( function(){ ccm_showPane(ccm_onPaneCloseObj,ccm_onPaneCloseTargetURL) } );
		return false;
	} else {
		ccm_doShowPane(obj, targetURL);
	}
}

ccm_doShowPane = function(obj, targetURL) {
	// jump to the top of the page
	window.scrollTo(0,0);
	
	// loop through header nav items, turn them all off except our current one
	ccm_deactivateHeader(obj);

	ccm_deactivateSite(function() {;
		var wrappane = jQuery("#ccm-page-detail");
		var conpane = jQuery("#ccm-page-detail-content");
		ccm_hideMenus();
		ccm_hideHighlighter();
		ccm_topPaneTargetURL = targetURL;
		jQuery(window).keypress(function(e) {
			if (e.keyCode == 27) {
				ccm_hidePane();
			}
		});
		
		jQuery.ajax({
			type: 'GET',
			url: targetURL + "&random=" + (new Date().getTime()),
			success: function(msg) {
				conpane.html(msg);
				jQuery("#ccm-page-detail-content .dialog-launch").dialog();			
				if (ccm_animEffects) {
					wrappane.fadeIn(60, function() {
						ccm_removeHeaderLoading();
					});
				} else {
					wrappane.show();
					ccm_removeHeaderLoading();
				}
			}
		});
	});
}

ccm_triggerSelectUser = function(uID, uName, uEmail) {
	alert(uID);
	alert(uName);
	alert(uEmail);
}

ccm_setupUserSearch = function() {
	jQuery("#ccm-user-list-cb-all").click(function() {
		if (jQuery(this).attr('checked') == true) {
			jQuery('.ccm-list-record td.ccm-user-list-cb input[type=checkbox]').attr('checked', true);
			jQuery("#ccm-user-list-multiple-operations").attr('disabled', false);
		} else {
			jQuery('.ccm-list-record td.ccm-user-list-cb input[type=checkbox]').attr('checked', false);
			jQuery("#ccm-user-list-multiple-operations").attr('disabled', true);
		}
	});
	jQuery("td.ccm-user-list-cb input[type=checkbox]").click(function(e) {
		if (jQuery("td.ccm-user-list-cb input[type=checkbox]:checked").length > 0) {
			jQuery("#ccm-user-list-multiple-operations").attr('disabled', false);
		} else {
			jQuery("#ccm-user-list-multiple-operations").attr('disabled', true);
		}
	});
	
	// if we're not in the dashboard, add to the multiple operations select menu

	jQuery("#ccm-user-list-multiple-operations").change(function() {
		var action = jQuery(this).val();
		switch(action) {
			case 'choose':
				var idstr = '';
				jQuery("td.ccm-user-list-cb input[type=checkbox]:checked").each(function() {
					ccm_triggerSelectUser(jQuery(this).val(), jQuery(this).attr('user-name'), jQuery(this).attr('user-email'));
				});
				jQuery.fn.dialog.closeTop();
				break;
			case "properties": 
				uIDstring = '';
				jQuery("td.ccm-user-list-cb input[type=checkbox]:checked").each(function() {
					uIDstring=uIDstring+'&uID[]='+jQuery(this).val();
				});
				jQuery.fn.dialog.open({
					width: 630,
					height: 450,
					modal: false,
					href: CCM_TOOLS_PATH + '/users/bulk_properties?' + uIDstring,
					title: ccmi18n.properties				
				});
				break;				
		}
		
		jQuery(this).get(0).selectedIndex = 0;
	});

	jQuery("div.ccm-user-search-advanced-groups-cb input[type=checkbox]").unbind();
	jQuery("div.ccm-user-search-advanced-groups-cb input[type=checkbox]").click(function() {
		jQuery("#ccm-user-advanced-search").submit();
	});

}

ccm_triggerSelectGroup = function(gID, gName) {
	alert(gID);
	alert(gName);
}

ccm_setupGroupSearch = function() {
	jQuery('div.ccm-group a').unbind();
	jQuery('div.ccm-group a').each(function(i) {
		var gla = jQuery(this);
		jQuery(this).click(function() {
			ccm_triggerSelectGroup(gla.attr('group-id'), gla.attr('group-name'));
			jQuery.fn.dialog.closeTop();
			return false;
		});
	});	
	jQuery("#ccm-group-search").ajaxForm({
		beforeSubmit: function() {
			jQuery("#ccm-group-search-wrapper").html("");	
		},
		success: function(resp) {
			jQuery("#ccm-group-search-wrapper").html(resp);	
		}
	});
	
	/* setup paging */
	jQuery("div#ccm-group-paging a").click(function() {
		jQuery("#ccm-group-search-wrapper").html("");	
		jQuery.ajax({
			type: "GET",
			url: jQuery(this).attr('href'),
			success: function(resp) {
				//jQuery("#ccm-dialog-throbber").css('visibility','hidden');
				jQuery("#ccm-group-search-wrapper").html(resp);
			}
		});
		return false;
	});
}

ccm_saveArrangement = function() {
	
	ccm_mainNavDisableDirectExit(true);
	var serial = '';
	jQuery('div.ccm-area').each(function() {
		areaStr = '&area[' + jQuery(this).attr('id').substring(1) + '][]=';
		
		bArray = jQuery(this).sortable('toArray');
		for (i = 0; i < bArray.length; i++ ) {
			if (bArray[i] != '' && bArray[i].substring(0, 1) == 'b') {
				// make sure to only go from b to -, meaning b28-9 becomes "28"
				var bID = bArray[i].substring(1, bArray[i].indexOf('-'));
				var bObj = jQuery('#' + bArray[i]);
				bID += '-' + bObj.attr('custom-style');
				serial += areaStr + bID;
			}
		}
	});
	
 	jQuery.ajax({
 		type: 'POST',
 		url: CCM_DISPATCHER_FILENAME,
 		data: 'cID=' + CCM_CID + '&ccm_token=' + CCM_SECURITY_TOKEN + '&btask=ajax_do_arrange' + serial,
 		success: function(msg) {
			jQuery("div.ccm-area").removeClass('ccm-move-mode');
			jQuery('div.ccm-block-arrange').each(function() {
				jQuery(this).addClass('ccm-block');
				jQuery(this).removeClass('ccm-block-arrange');
			});
			ccm_arrangeMode = false;
			jQuery("li.ccm-main-nav-arrange-option").fadeOut(300, function() {
				jQuery("li.ccm-main-nav-edit-option").fadeIn(300, function() {
					ccm_removeHeaderLoading();
				});
			});
 			ccmAlert.hud(ccmi18n.arrangeBlockMsg, 2000, 'up_down', ccmi18n.arrangeBlock);
 		}});
}

ccm_arrangeInit = function() {
	//jQuery(document.body).append('<img src="' + CCM_IMAGE_PATH + '/topbar_throbber.gif" width="16" height="16" id="ccm-topbar-loader" />');
	
	ccm_arrangeMode = true;
	
	ccm_hideHighlighter();
	ccm_menuActivated = true;
	
	jQuery('div.ccm-block').each(function() {
		jQuery(this).addClass('ccm-block-arrange');
		jQuery(this).removeClass('ccm-block');
	});
	
	ccm_setupHeaderMenu();
	jQuery("li.ccm-main-nav-edit-option").fadeOut(300, function() {
		jQuery("li.ccm-main-nav-arrange-option").fadeIn(300);
	});
	
	jQuery("div.ccm-area").each(function() {
		jQuery(this).addClass('ccm-move-mode');
		jQuery(this).sortable({
			items: 'div.ccm-block-arrange',
			connectWith: jQuery("div.ccm-area"),
			accept: 'div.ccm-block-arrange',
			opacity: 0.5
		});
	});
	
	jQuery("a#ccm-nav-save-arrange").click(function() {
		ccm_saveArrangement();
	});
}

ccm_init = function() {
	ccm_setupHeaderMenu();
	// blink notification if it exists
	//jQuery("#ccm-notification").fadeIn();
	jQuery("a#ccm-nav-edit").click(function() {
		if (!ccm_topPaneDeactivated) {
			setTimeout(function() {
				// stupid safari? wtf?
				window.location.href = CCM_DISPATCHER_FILENAME + '?cID=' + CCM_CID + '&ctask=check-out&ccm_token=' + CCM_SECURITY_TOKEN;
			}, 50);
		}
	});
	jQuery("a#ccm-nav-add").click(function() {
		ccm_showPane(this, CCM_TOOLS_PATH + "/edit_collection_popup.php?ctask=add&cID=" + CCM_CID);
	});
}

ccm_selectSitemapNode = function(cID, cName) {
	alert(cID);
	alert(cName);
}

ccm_goToSitemapNode = function(cID, cName) {
	window.location.href= CCM_DISPATCHER_FILENAME + '?cID=' + cID;
}

ccm_fadeInMenu = function(bobj, e) {
	var mwidth = bobj.width();
	var mheight = bobj.height();
	var posX = e.pageX + 2;
	var posY = e.pageY + 2;
	
	if (jQuery(window).height() < e.clientY + mheight) {
		posY = e.pageY - mheight + 20;
	} else {
		posY = posY - 20;
	}
	
	if (jQuery(window).width() < e.clientX + mwidth) {
		posX = e.pageX - mwidth + 15;
	} else {
		posX = posX - 15;
	}
	
	// the 15 and 20 is because of the way we're styling these menus
	
	bobj.css("top", posY + "px");
	bobj.css("left", posX + "px");
	
	if (ccm_animEffects) {
		bobj.fadeIn(60);
	} else {
		bobj.show();
	}
}

ccm_blockWindowClose = function() {
	jQuery.fn.dialog.closeTop();
	ccm_blockWindowAfterClose();
}

ccm_blockWindowAfterClose = function() {
	ccmValidateBlockForm = function() {return true;}
}

ccm_blockFormSubmit = function() {
	if (typeof window.ccmValidateBlockForm == 'function') {
		r = window.ccmValidateBlockForm();
		if (!r) {
			jQuery.fn.dialog.hideLoader();
		}
		if (ccm_isBlockError) {
			if(ccm_blockError) {
				ccmAlert.notice(ccmi18n.error, ccm_blockError + '</ul>');
			}
			ccm_resetBlockErrors();
			return false;
		}
	}
	return true;
}

ccm_setupGridStriping = function(tbl) {
	jQuery("#" + tbl + " tr").removeClass();
	var j = 0;
	jQuery("#" + tbl + " tr").each(function() {
		if (jQuery(this).css('display') != 'none') {					
			if (j % 2 == 0) {
				jQuery(this).addClass('ccm-row-alt');
			}
			j++;
		}
	});
}

ccm_headerMenuPreloads = function(){ 
	var ccmLoadingIcon = new Image();
	ccmLoadingIcon.src = CCM_IMAGE_PATH + "/icons/icon_header_loading.gif";
	
	var ccmHeaderImg = new Image();// preload image
	ccmHeaderImg.src = CCM_IMAGE_PATH + "/bg_header_active.png";
}

ccm_setupDashboardHeaderMenu = function(){	
	ccm_headerMenuPreloads();
	jQuery("#ccm-nav-dashboard-help").dialog();
}

ccm_dashboardRequestRemoteInformation = function() {
	jQuery.get(CCM_TOOLS_PATH + '/dashboard/get_remote_information');
}


ccm_getMarketplaceItem = function(args) {
	var mpID = args.mpID;
	var closeTop = args.closeTop;
	
	this.onComplete = function() { }

	if (args.onComplete) {
		ccm_getMarketplaceItem.onComplete = args.onComplete;
	}
	
	if (closeTop) {
		jQuery.fn.dialog.closeTop(); // this is here due to a weird safari behavior
	}
	jQuery.fn.dialog.showLoader();
	// first, we check our local install to ensure that we're connected to the
	// marketplace, etc..
	params = {'mpID': mpID};
	jQuery.getJSON(CCM_TOOLS_PATH + '/marketplace/connect', params, function(resp) {
		jQuery.fn.dialog.hideLoader();
		if (resp.isConnected) {
			if (!resp.purchaseRequired) {
				jQuery.fn.dialog.open({
					title: ccmi18n.community,
					href:  CCM_TOOLS_PATH + '/marketplace/download?install=1&mpID=' + mpID,
					width: 350,
					modal: false,
					height: 240
				});
			}

		} else {
			jQuery.fn.dialog.open({
				title: ccmi18n.community,
				href:  CCM_TOOLS_PATH + '/marketplace/frame?mpID=' + mpID,
				width: '90%',
				modal: false,
				height: '70%'
			});
		}
	});
}

ccm_setupHeaderMenu = function() {
	
	ccm_headerMenuPreloads();

	jQuery("ul#ccm-main-nav a").click(function() {
		if (!ccm_topPaneDeactivated) {
			jQuery(this).addClass('ccm-nav-loading');
		}
	});
	jQuery("ul#ccm-system-nav a").click(function() {
		jQuery(this).addClass('ccm-nav-loading');
	});
	jQuery("#ccm-nav-help").dialog();
	jQuery("#ccm-nav-sitemap").dialog();
	jQuery("#ccm-nav-file-manager").dialog();
	jQuery("a#ccm-nav-dashboard").click(function() {
		var dash = jQuery(this).attr('href');
		setTimeout(function() {
			// stupid safari? wtf?
			window.location.href = dash;
		}, 50);
		
	});
	
	jQuery("a#ccm-nav-logout").click(function() {
		var href = jQuery(this).attr('href');
		setTimeout(function() {
			// stupid safari? wtf?
			window.location.href = href;
		}, 50);
		
	});
	
}

ccm_removeHeaderLoading = function() {
	jQuery("a.ccm-nav-loading").removeClass('ccm-nav-loading');
}

ccm_showBreadcrumb = function() {
/*jQuery("#ccm-bc").animate({
	top: '50x',
	}, {
		duration: 200,
		easing: 'easeInBounce'
	});*/
	jQuery("#ccm-bc").show();
	jQuery("#ccm-bc").css('top', '49px');
}

ccm_hideBreadcrumb = function() {
	/*
	jQuery("#ccm-bc").animate({
	top: '0px',
	}, {
		duration: 200,
		easing: 'easeOutQuad'
	});*/
	jQuery("#ccm-bc").css('top', '0px');
	jQuery("#ccm-bc").hide();
	ccm_bcEnabled = false;

}

ccm_setupBreadcrumb = function() {
	
	if (jQuery("#ccm-bc").get().length > 0) {
		jQuery("#ccm-page-controls").mouseover(function() {
			if (ccm_siteActivated) { 
				if (!ccm_bcEnabled) {
					ccm_showBreadcrumb();
				}
				ccm_bcEnabled = true;
			}
		});
		jQuery("#ccm-bc").mouseover(function() {
			ccm_bcEnabled = true;
			if (ccm_bcEnabledTimer) {
				clearTimeout(ccm_bcEnabledTimer);
				ccm_bcEnabledTimer = false;
			}
		});
		jQuery("#ccm-bc").mouseout(function() {
			ccm_bcEnabled = false;
			ccm_bcEnabledTimer = setTimeout(function() {
				if (!ccm_bcEnabled) {
					ccm_hideBreadcrumb();
				}
			}, 500);
		});
	}
}

/** 
 * JavaScript localization. Provide a key and then reference that key in PHP somewhere (where it will be translated)
 */
ccm_t = function(key) {
	return jQuery("input[name=ccm-string-" + key + "]").val();
}

jQuery(function() {
	/*
	
	b1 = new Image();// preload image
	b1.src = CCM_IMAGE_PATH + "/button_l.png";
	b2 = new Image();// preload image
	b2.src = CCM_IMAGE_PATH + "/button_l_active.png";
	b3 = new Image();// preload image
	b3.src = CCM_IMAGE_PATH + "/button_r.png";
	b4 = new Image();// preload image
	b4.src = CCM_IMAGE_PATH + "/button_r_active.png";
	b5 = new Image();// preload image
	b5.src = CCM_IMAGE_PATH + "/button_scroller_l_active.png";
	b6 = new Image();// preload image
	b6.src = CCM_IMAGE_PATH + "/button_scroller_r_active.png";
	
	// menu assets
	m1 = new Image();// preload image
	m1.src = CCM_IMAGE_PATH + "/bg_menu_rb.png";
	m2 = new Image();// preload image
	m2.src = CCM_IMAGE_PATH + "/bg_menu_b.png";
	m3 = new Image();// preload image
	m3.src = CCM_IMAGE_PATH + "/bg_menu_lb.png";
	m4 = new Image();// preload image
	m4.src = CCM_IMAGE_PATH + "/bg_menu_r.png";
	m5 = new Image();// preload image
	m5.src = CCM_IMAGE_PATH + "/bg_menu_l.png";
	m6 = new Image();// preload image
	m6.src = CCM_IMAGE_PATH + "/bg_menu_rt.png";
	m7 = new Image();// preload image
	m7.src = CCM_IMAGE_PATH + "/bg_menu_t.png";
	m8 = new Image();// preload image
	m8.src = CCM_IMAGE_PATH + "/bg_menu_lt.png";
		
	*/
	
	if (jQuery.browser.msie) {
		ccm_animEffects = false;
	} else {
		ccm_animEffects = true;
	}


});
 

//make sure the user isn't using internet explorer 6
/*
jQuery(function(){
	if( jQuery.browser.msie ){
		var versionParts = jQuery.browser.version.split('.'); 
		if( parseInt(versionParts[0])==6 ) 
			alert( ccmi18n.noIE6 );
	}
});
*/

/* Block Styles Customization Popup */
var ccmCustomStyle = {   
	tabs:function(aLink,tab){
		jQuery('.ccm-styleEditPane').hide();
		jQuery('#ccm-styleEditPane-'+tab).show();
		jQuery(aLink.parentNode.parentNode).find('li').removeClass('ccm-nav-active');
		jQuery(aLink.parentNode).addClass('ccm-nav-active');
		return false;
	},
	resetAll:function(){
		if (!confirm( ccmi18n.confirmCssReset)) {  
			return false;
		}
		jQuery.fn.dialog.showLoader();

		jQuery('#ccm-reset-style').val(1);
		jQuery('#ccmCustomCssForm').get(0).submit();
		return true;
	},
	showPresetDeleteIcon: function() {
		if (jQuery('select[name=cspID]').val() > 0) {
			jQuery("#ccm-style-delete-preset").show();		
		} else {
			jQuery("#ccm-style-delete-preset").hide();
		}	
	},
	deletePreset: function() {
		var cspID = jQuery('select[name=cspID]').val();
		if (cspID > 0) {
			
			if( !confirm(ccmi18n.confirmCssPresetDelete) ) return false;
			
			var action = jQuery('#ccm-custom-style-refresh-action').val() + '&deleteCspID=' + cspID + '&subtask=delete_custom_style_preset';
			jQuery.fn.dialog.showLoader();
			
			jQuery.get(action, function(r) {
				jQuery("#ccm-custom-style-wrapper").html(r);
				jQuery.fn.dialog.hideLoader();
			});
		}
	},
	initForm: function() {
		if (jQuery("#cspFooterPreset").length > 0) {
			jQuery("#ccmCustomCssFormTabs input, #ccmCustomCssFormTabs select, #ccmCustomCssFormTabs textarea").bind('change click', function() {
				jQuery("#cspFooterPreset").show();
				jQuery("#cspFooterNoPreset").remove();
				jQuery("#ccmCustomCssFormTabs input, #ccmCustomCssFormTabs select").unbind('change click');
			});		
		}
		jQuery('input[name=cspPresetAction]').click(function() {
			if (jQuery(this).val() == 'create_new_preset' && jQuery(this).attr('checked')) {
				jQuery('input[name=cspName]').attr('disabled', false).focus();
			} else { 
				jQuery('input[name=cspName]').val('').attr('disabled', true); 
			}
		});
		ccmCustomStyle.showPresetDeleteIcon();
		
		ccmCustomStyle.lastPresetID=parseInt(jQuery('select[name=cspID]').val());
		
		jQuery('select[name=cspID]').change(function(){ 
			var cspID = parseInt(jQuery(this).val());
			var selectedCsrID = parseInt(jQuery('input[name=selectedCsrID]').val());
			
			if(ccmCustomStyle.lastPresetID==cspID) return false;
			ccmCustomStyle.lastPresetID=cspID;
			
			jQuery.fn.dialog.showLoader();
			if (cspID > 0) {
				var action = jQuery('#ccm-custom-style-refresh-action').val() + '&cspID=' + cspID;
			} else {
				var action = jQuery('#ccm-custom-style-refresh-action').val() + '&csrID=' + selectedCsrID;
			}
			
			
			jQuery.get(action, function(r) {
				jQuery("#ccm-custom-style-wrapper").html(r);
				jQuery.fn.dialog.hideLoader();
			});
			
		});
		
		jQuery('#ccmCustomCssForm').submit(function() {
			if (jQuery('input[name=cspCreateNew]').attr('checked') == true) {
				if (jQuery('input[name=cspName]').val() == '') { 
					jQuery('input[name=cspName]').focus();
					alert(ccmi18n.errorCustomStylePresetNoName);
					return false;
				}
			}

			jQuery.fn.dialog.showLoader();		
			return true;
		});
		
		//IE bug fix 0 can't focus on txt fields if new block just added 
		if(!parseInt(ccmCustomStyle.lastPresetID))  
			setTimeout('jQuery("#ccmCustomCssFormTabs input").attr("disabled", false).get(0).focus()',500);
	},
	validIdCheck:function(el,prevID){
		var selEl = jQuery('#'+el.value); 
		if( selEl && selEl.get(0) && selEl.get(0).id!=prevID ){		
			jQuery('#ccm-styles-invalid-id').css('display','block');
		}else{
			jQuery('#ccm-styles-invalid-id').css('display','none');
		}
	}
}