ccm_setupAttributeKeyCategoryItemSearch = function(searchInstance, akID) {alert("ccm_setupAttributeKeyCategoryItemSearch");
	if(typeof alreadyRun === 'undefined') alreadyRun = Array();
	if(typeof beendone === 'undefined') beendone = Array();
	if(typeof alreadyRun[searchInstance] === 'undefined' || typeof beendone[searchInstance] !== 'undefined') {
		alreadyRun[searchInstance] = 1;
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
						ccm_triggerSelectAttributeKeyCategoryItem(akID, $(this).closest("tr"));
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
};

ccm_closeModalRefeshSearch = function(searchInstance) {
	alert("ccm_closeModalRefeshSearch");
	jQuery.fn.dialog.closeTop();
	$("#ccm-" + searchInstance + "-advanced-search").submit();
};
ccm_deleteAndRefeshSearch = function(URIComponents, searchInstance) {
	alert("ccm_deleteAndRefeshSearch");
	$.ajax({
		url: CCM_TOOLS_PATH + '/bricks/bulk_delete?task=delete&json=' + URIComponents
	}).responseText;
	jQuery.fn.dialog.closeTop();
	$("#ccm-" + searchInstance + "-advanced-search").submit();
};




(function(){
	var $ = jQuery,
		undefined,
		console;
		
	if(typeof window.console === "undefined"){
		console = {};
		console.debug = console.info = console.warn = console.error = console.log = function(){};	
	}else{
		console = window.console;	
	}
	
	//Prototype for the ccm_AttributeKeyCategoryItemSearchForm widget
	var ccm_AttributeKeyCategoryItemSearchForm = {
		widgetEventPrefix:"ccm_AttributeKeyCategoryItemSearchForm_".toLowerCase(),
		options:{
			searchInstance:null
		},
		_init:function(){
			var I = this;
			
			I._initForm();			
			I._initSearchFields();
		},
		
		_initForm:function(){
			var I = this;
			
			this.element.ajaxForm({
				beforeSubmit: $.proxy(I._beforeSearchSubmit, I),				
				success: $.proxy(I._onSearchSuccess, I)
			});			
			
		},
		
		_initSearchFields:function(){
			var I = this,
				$fBase = this.getRelEl("-field-base"),
				$fWrap = this.getRelEl("-fields-wrapper"),
				$fAdd = this.getRelEl("-add-option"),
				$fTypeBases = this.getRelEl("-field-base-elements");


			//Setup the add field button
			$fAdd.click(function(){
				$fBase.clone().appendTo($fWrap).slideDown(200).removeAttr("id");
			});
			
			//Setup remove field buttons
			$fWrap.delegate(".ccm-search-remove-option", "click", function(evt){
				var $field = $(this).closest("div");
				$field.slideUp(200, function(){
					$(this).closest("div").remove();
				});				
			});
			
			//Setup field type selects
			$fWrap.delegate(".ccm-input-select", "change", function(evt){
				var $select = $(this),
					$fTypeBase = $fTypeBases.find("[search-field='"+$select.val()+"']"),
					$fContent = $select.closest("table").find("td.ccm-selected-field-content").empty(),
					$field = $fTypeBase.clone().appendTo($fContent).fadeIn();
				
				//Set the hidden field
				$select.next(".ccm-selected-search-field").val($select.val());
				
				//Uniquify element ids
				$field.find("[id]").each(function(){
					this.id += "_"+$fWrap.find(".ccm-search-field").length;
				});
				
				//Setup date fields - this should really be done by whatever generates the fields
				$field.find(".ccm-input-date-wrapper input").datepicker({
					showAnim: 'fadeIn'
				});
				
				console.log($fTypeBase, $fTypeBases);
			});
			
			
		},
		
		_beforeSearchSubmit:function(){
			this.disable();
		},
		_onSearchSuccess:function(resp){
			
			this.getResultsWidget().element.replaceWith(resp);
			this.enable();
		},
		
		enable:function(){
			this.element.find(":submit").removeAttr("disabled");
			this.element.find(".ccm-search-loading").hide();
			
			return this._setOption( "disabled", false );
		},
		
		disable:function(){
			this.element.find(":submit").attr("disabled","disabled");
			this.element.find(".ccm-search-loading").show();
			
			return this._setOption( "disabled", true );
		},
		
		getResultsWidget:function(){
			return $("#"+this.options.searchInstance+"_results").data("ccm_AttributeKeyCategoryItemSearchResults");
		},
		
		getRelEl:function(sel){
			return $(this.getRelElId(sel));
		},
		
		getRelElId:function(sel){
			return "#"+this.element.attr("id")+sel;
		},
		
		bind:function(){
			arguments[0] = (this.widgetEventPrefix+arguments[0]).toLowerCase();
			this.element.bind.apply(this.element, arguments);
		}
	};
	
	$.widget("ccm.ccm_AttributeKeyCategoryItemSearchForm", ccm_AttributeKeyCategoryItemSearchForm);
	
	
	//Prototype for the ccm_AttributeKeyCategoryItemSearchResults widget
	var ccm_AttributeKeyCategoryItemSearchResults = {
		widgetEventPrefix:"ccm_AttributeKeyCategoryItemSearchResults_".toLowerCase(),
		options:{
			searchInstance:null,
			mode:"choose_multiple",
			itemSelector:"tr.ccm-list-record"
		},
		_init:function(){
			var I = this,
				id = this.element.attr("id");
			
			this._initItems();	
			this._initSorting();		
			this._initPaging();
			this._initColumnConfig();			
			
		},
		
		_initItems:function(){
			var I = this;
			//Bind to the checkboxes
			this.element.delegate(this.options.itemSelector +" > td > input[name=ID]", "change", function(evt){
				I.execItemAction(this.checked ? "select" : "unselect", $(this).closest("tr"));
			});
			
			//Bind to the item containing rows
			this.element.delegate(this.options.itemSelector +" > td:not(:has(input[name=ID]))", "click", function(evt){
				if(I.options.mode === "choose_multiple" || I.options.mode === "choose" || I.options.mode === "admin"){
					I.execItemAction("choose", $(this).closest("tr"));
				}
			});
			
			//Bind to the "all" checkbox
			this.getRelEl("-list-cb-all").bind("change", function(){				
				I.selectAllToggle(this.checked);
			});
			
			//Bind to the multiple operations checkbox
			this.getRelEl("-list-multiple-operations").bind("change", function(){
				var action = this.value;
				if($.trim(this.value).length){
					I.execItemAction(this.value, I.getItems(true));
				}
				
			});
			
		},
		
		_initSorting:function(){
			var I = this;
			//Setup ajax sorting
			this.element.find(".ccm-results-list th a").not(".ccm-search-add-column").click(function() {
				$.get($(this).attr("href"), function(data){
					I.element.replaceWith(data);
					$("div.ccm-dialog-content").attr('scrollTop', 0);
				});
				return false;
			});			
		},
		
		_initPaging:function(){
			//Setup ajax paging
			this.element.find("div.ccm-pagination a").click(function() {
				$.get($(this).attr("href"), function(data){
					I.element.replaceWith(data);
					$("div.ccm-dialog-content").attr('scrollTop', 0);
				});
				return false;
			});
		},
		
		_initColumnConfig:function(){
			var I = this;
			//Setup column config
			this.element.find("a.ccm-search-add-column").click(function(){
				var params = {
					searchInstance:I.options.searchInstance,
					columns:$('input[name=columns_'+I.options.searchInstance+']').val(),
					persistantBID: $('input[name=persistantBID_'+I.options.searchInstance+']').val()
				};
				jQuery.fn.dialog.open({
					width: 550,
					height: 350,
					modal: false,
					href: $(this).attr('href')+"&"+$.param(params),
					title: ccmi18n.customizeSearch				
				});
				return false;
				
			});
			
		},
		
		
		getFormWidget:function(){
			return $("#"+this.options.searchInstance+"_form").data("ccm_AttributeKeyCategoryItemSearchForm");
		},
		
		getItems:function(ifSelected){
			var id = this.element.attr("id"),
				$items = this.element.find(this.options.itemSelector);
			if(ifSelected === true){
				$items = $items.filter(":has(input[name=ID]:checked)");
			}else if(ifSelected === false){
				$items = $items.not(":has(input[name=ID]:checked)");
			}
			return $items;
		},
		
		selectAllToggle:function(isChecked){			
			this.execItemAction(isChecked ? "select" : "unselect", this.getItems());
		},
		
		multipleActionsToggle:function(isEnabled){
			var id = this.element.attr("id"),
				$items = this.getItems(),
				$dropdown = $("#"+id+"-list-multiple-operations");
				
			if(isEnabled === true || ($items.has("input[name=ID]:checked").length && isEnabled !== false)){
				$dropdown.removeAttr("disabled");
			}else{
				$dropdown.attr("disabled","disabled");	
			}
		},
		
		itemActions:{
			select:function(data){
				data.$item.find("input[name=ID]").prop("checked", true);				
				data.widget.multipleActionsToggle();
			},
			unselect:function(data){
				data.$item.find("input[name=ID]").prop("checked", false);
				data.widget.multipleActionsToggle();
			},
			choose:function(data){
				console.log(arguments);
			}
		},
		
		execItemAction:function(key, $item, extraArgs){
			var I = this,
				data = {key:key, $item:$item, widget:I};
			
			if($.isArray(extraArgs)){
				//data = data.push.apply(data, extraArgs);
				$.extend(data, extraArgs);
			}
			
			//Dispatch an even for actions to multiple items. 
			if($item.length > 1){
				var multiResult = I._trigger.apply(I, [key+"items", null, data]);
				//If the event is stopped by one of the handlers, we stop here and do not execute the action on multiple items.
				if(multiResult === false){
					return;	
				}
			}
			
			//Execute the action
			$item.each(function(i, item){
				var $item = $(item),
					result = null;
				
				//copy the data object
				data = $.extend(true, {}, data);
				
				if($.isFunction(I.itemActions[key])){
					result = I.itemActions[key].apply($item.get(0), [data]);
				}
				
				if(result !== false){
					var args = [key+"item", null, data];
					I._trigger.apply(I, args);
				}
			});	
					
		},
		
		bind:ccm_AttributeKeyCategoryItemSearchForm.bind,		
		getRelEl:ccm_AttributeKeyCategoryItemSearchForm.getRelEl,
		getRelElId:ccm_AttributeKeyCategoryItemSearchForm.getRelElId
	};
	
	//Register the ccm_AttributeKeyCategoryItemSearchResults widget
	$.widget("ccm.ccm_AttributeKeyCategoryItemSearchResults", ccm_AttributeKeyCategoryItemSearchResults);
	
	
	
	
	
	//Prototype for the jQuery.ccm_attributeKeyCategoryItemSelector widget
	var ccm_attributeKeyCategoryItemSelector = {
		widgetEventPrefix:"ccm_attributeKeyCategoryItemSelector_".toLowerCase(),
		options:{
			selectItemDialog:{
				href:CCM_TOOLS_PATH+"/bricks/search_dialog",
				width:"90%",
				height:"70%",
				modal:false
			},
			selectItemParameters:{
				mode:"choose_multiple"
			},
			itemSelector:"tr.ccm-list-record"
		},
		_create:function(){
			if(!this.options.selectItemParameters.searchInstance){
				this.options.selectItemParameters.searchInstance = this.widgetName+"_search"+(new Date()).getMilliseconds();
			}
		},
		_init:function(){
			var I = this;			
			
			this._table = this.element.find("table").first();
			this._tbody = this._table.children("tbody");
			
			//Setup the item list wrapper
			this._tbody.sortable({axis:"y", delay:300});
			this._tbody.disableSelection();
			
			this._tbody.bind("sortstop", function(event, ui) {
				I.restripe();
			  	//Not sure what the best way to relay this is yet, or if we need to at all.
			});		
			
			//Delegate item actions
			this._tbody.children("tr").each(function(){
				//I.delegateItemActions($(this), $(this));
			});
			
			this.restripe();
			
			
			//Setup the item selector trigger
			this.element.find(".ccm-attribute-key-category-select-item").bind("click", function(){
				I.openItemSelect();
			});
			
			
			$("#"+this.options.selectItemParameters.searchInstance+"_results").live(ccm_AttributeKeyCategoryItemSearchResults.widgetEventPrefix+"chooseitems", function(evt, data){
				data.$item.each(function(){
					I.addItem($(this));
				});
				jQuery.fn.dialog.closeTop();	
				return false;
				
			});
			
			$("#"+this.options.selectItemParameters.searchInstance+"_results").live(ccm_AttributeKeyCategoryItemSearchResults.widgetEventPrefix+"chooseitem", function(evt, data){
				I.addItem(data.$item);
				jQuery.fn.dialog.closeTop();	
			});
			
			
			this._delegateItemActions(this._tbody, this.options.itemSelector +" > td > a", "click");
			
		},
		addItem:function($row, position){
			
			var $item = $row,
				$id = $item.find("input[name=ID]"),
				$existing = this._tbody.find("input[name='"+ this.options.fieldName +"'][value='"+ $id.val() +"']"); //see if this item already exists
			
			if($existing.length){
				$item = $existing.closest(this.options.itemSelector).remove();

				//this.delegateItemActions($item, $item);
			}else{
				//Remove uncessary columns
				var $cols = $item.children("td");
					
				$cols.first().remove();
				$cols.last().remove();
				$cols.last().remove();
				$item.append(this.options.itemActionsCell);
				
				$item.children("td").last().append("<input type='hidden' name='"+this.options.fieldName+"' value='"+$id.val()+"' />");
				
				//this.delegateItemActions($item, $item);
			}
			
			if(position < 1){
				this._tbody.prepend($item);
			}else if(position == null || position > this._tbody.children(this.options.itemSelector).length){
				this._tbody.append($item);	
			}else{
				this._tbody.children(this.options.itemSelector).eq(position).insertAfter($item);
			}
			
			var $noneSelected = this._tbody.children(".ccm-attribute-key-category-selected-item-none");
			if($noneSelected.length){
				$noneSelected.remove();	
			}
			
			this.restripe();
			
			
			this._trigger("addItem", null, $row, position);
		},
		
		itemActions:{
			remove:function(data){
				data.$item.fadeOut(300, function(){
					data.$item.remove();
					data.widget.restripe();
				});				
			}
		},
		
		
		
		execItemAction:ccm_AttributeKeyCategoryItemSearchResults.execItemAction,
		
		
		
		_delegateItemActions:function($context, selector, eventName, $item){
			var I = this;
			
			$context.delegate(selector, eventName||"click", function(){
			
				var $a = $(this),
					classes = $a.attr("class") ? $.trim($a.attr("class")).split(/\s+/g) : [],
					$item = $item || $a.closest(I.options.itemSelector);
				
				for(var c = 0; c < classes.length; c++){
					var key = classes[c];
					if($.isFunction(I.itemActions[key])){
						I.execItemAction(key, $item);
					}
				}
				
			});
			
		},
		
		openItemSelect:function(opts, params){
			
			var parameters = $.extend({fieldName:this.options.fieldName}, this.options.selectItemParameters, params),
				dialogOpts = $.extend({}, this.options.selectItemDialog, opts);
				
			dialogOpts.href += "?"+$.param(parameters);
			
			dialogOpts.onOpen = function(){ };
			dialogOpts.onClose = function(){ };
			
			jQuery.fn.dialog.open(dialogOpts);
			
		},
		
		restripe:function(){
			
			var $items = this._tbody.children(this.options.itemSelector);
			$items.removeClass("ccm-list-record-alt").filter(":odd").addClass("ccm-list-record-alt");
			this._trigger("restripe");
		},
		
		
		bind:ccm_AttributeKeyCategoryItemSearchForm.bind,		
		getRelEl:ccm_AttributeKeyCategoryItemSearchForm.getRelEl,
		getRelElId:ccm_AttributeKeyCategoryItemSearchForm.getRelElId
		
	};
	
	
	//Register the jQuery.ccm_attributeKeyCategoryItemSelector widget
	$.widget("ccm.ccm_attributeKeyCategoryItemSelector", ccm_attributeKeyCategoryItemSelector);
	
	

})();