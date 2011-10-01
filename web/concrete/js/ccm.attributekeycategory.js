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
	
	//Prototype for the ccm_akcItemSearchForm widget
	var ccm_akcItemSearchForm = {
		widgetEventPrefix:"ccm_akcItemSearchForm_".toLowerCase(),
		options:{
			baseId:null,
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
			return $("#"+this.getBaseId()+"_results").data("ccm_akcItemSearchResults");
		},
		
		getRelEl:function(sel){
			return $(this.getRelElId(sel));
		},
		
		getRelElId:function(sel){
			return "#"+this.element.attr("id")+sel;
		},
		
		getBaseId:function(){
			var id = this.options.baseId || this.element.attr("id").replace(/^(.+)_\w+$/, "$1");
			console.log(this.widgetEventPrefix, id, this.element.attr("id"));
			return id;
		},
		
		bind:function(){
			arguments[0] = (this.widgetEventPrefix+arguments[0]).toLowerCase();
			this.element.bind.apply(this.element, arguments);
		}
	};
	
	$.widget("ccm.ccm_akcItemSearchForm", ccm_akcItemSearchForm);
	
	
	//Prototype for the ccm_akcItemSearchResults widget
	var ccm_akcItemSearchResults = {
		widgetEventPrefix:"ccm_akcItemSearchResults_".toLowerCase(),
		options:{
			baseId:null,
			akcHandle:null,
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
					baseId:I.options.baseId,
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
			return $("#"+this.getBaseId()+"_form").data("ccm_akcItemSearchForm");
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
			},
			properties:function(data){
				var params = {
						searchInstance:data.widget.options.searchInstance,
						baseId:data.widget.options.baseId,
						akCategoryHandle:data.widget.options.akcHandle
					},
					ids = data.$item.find("input[name=ID]").map(function(){
						return this.value;
					}).get();
				params["newObjectID[]"] = ids;
				//console.log($.param(params));
		
				jQuery.fn.dialog.open({
					width: 630,
					height: 450,
					modal: true,
					href: CCM_TOOLS_PATH +'/bricks/bulk_properties?' + $.param(params),
					title: ccmi18n.properties
				});	
			},
			"delete":function(data){
				var params = {
						searchInstance:data.widget.options.searchInstance,
						baseId:data.widget.options.baseId,
						akCategoryHandle:data.widget.options.akcHandle
					},
					ids = data.$item.find("input[name=ID]").map(function(){
						return this.value;
					}).get();
				params["akcID[]"] = ids;
		
				jQuery.fn.dialog.open({
					width: 300,
					height: 100,
					modal: true,
					href: CCM_TOOLS_PATH +'/bricks/bulk_delete?' + $.param(params),
					title: ccmi18n.properties
				});	
			}
		},
		
		execItemAction:function(key, $item, extraArgs){
			var I = this,
				data = {key:key, $item:$item, widget:I};
			
			if($.isArray(extraArgs)){
				//data = data.push.apply(data, extraArgs);
				$.extend(data, extraArgs);
			}
			
			//Execute the action
			var result = null;
			
			//copy the data object
			data = $.extend(true, {}, data);
			
			if($.isFunction(I.itemActions[key])){
				result = I.itemActions[key].apply($item.get(0), [data]);
			}
			
			if(result !== false){
				var args = [key+"item", null, data];
				I._trigger.apply(I, args);
			}	
					
		},
		
		bind:ccm_akcItemSearchForm.bind,		
		getRelEl:ccm_akcItemSearchForm.getRelEl,
		getRelElId:ccm_akcItemSearchForm.getRelElId,
		getBaseId:ccm_akcItemSearchForm.getBaseId
	};
	
	//Register the ccm_akcItemSearchResults widget
	$.widget("ccm.ccm_akcItemSearchResults", ccm_akcItemSearchResults);
	
	
	
	
	
	//Prototype for the jQuery.ccm_akcItemSelector widget
	var ccm_akcItemSelector = {
		widgetEventPrefix:"ccm_akcItemSelector_".toLowerCase(),
		options:{
			max:Infinity,
			baseId:null,
			akcHandle:null,
			searchInstance:null,
			selectItemDialog:{
				href:CCM_TOOLS_PATH+"/bricks/search_dialog",
				width:"90%",
				height:"70%",
				modal:false
			},
			itemSearchParams:{
				mode:"choose_multiple"
			},
			itemSelector:"tr.ccm-list-record"
		},
		_init:function(){
			var I = this,
				baseId = this.getBaseId();		
			
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
			this.element.find(".ccm-akc-select-item").bind("click", function(){
				if(I._tbody.children(I.options.itemSelector).length < I.options.max){
					I.openItemSearch();
				}
			});
			this.toggleAddItem();

			
			//Listen for selected items
			$("#"+baseId+"_results").live(ccm_akcItemSearchResults.widgetEventPrefix+"chooseitem", function(evt, data){
				data.$item.each(function(){
					I.addItem($(this));
				});
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
			
			//If we've reached the max items, remove the last ones before we add the new one
			while(this._tbody.children(this.options.itemSelector).length >= this.options.max){		
				this._tbody.children(this.options.itemSelector).last().remove();
			}
			
			//Now add the new item
			if(position < 1){
				this._tbody.prepend($item);
			}else if(position == null || position > this._tbody.children(this.options.itemSelector).length){
				this._tbody.append($item);	
			}else{
				this._tbody.children(this.options.itemSelector).eq(position).insertAfter($item);
			}
			
			//Toggle the add button, if we're at the max again
			this.toggleAddItem();
			
			//Remove the "none selected" row
			var $noneSelected = this._tbody.children(".ccm-akc-selected-item-none");
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
		
		execItemAction:ccm_akcItemSearchResults.execItemAction,		
		
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
		
		openItemSearch:function(opts, params){
			
			var parameters = $.extend({
					fieldName:this.options.fieldName,
					akCategoryHandle:this.options.akcHandle,
					baseId:this.options.baseId
				}, this.options.itemSearchParams, params),
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
		
		toggleAddItem:function(){
			var I = this,
				$add = this.element.find(".ccm-akc-select-item");
			if(this._tbody.children(this.options.itemSelector).length >= this.options.max){
				$add.css({opacity:".3"});
			}else{
				$add.css({opacity:""});
			}
		},
		
		bind:ccm_akcItemSearchForm.bind,		
		getRelEl:ccm_akcItemSearchForm.getRelEl,
		getRelElId:ccm_akcItemSearchForm.getRelElId,
		getBaseId:ccm_akcItemSearchForm.getBaseId
		
	};
	
	
	//Register the jQuery.ccm_akcItemSelector widget
	$.widget("ccm.ccm_akcItemSelector", ccm_akcItemSelector);
	
	

})();