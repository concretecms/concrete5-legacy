ccm_setupAttributeKeyCategoryItemSearch = function(searchInstance, akID) {
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
	jQuery.fn.dialog.closeTop();
	$("#ccm-" + searchInstance + "-advanced-search").submit();
};
ccm_deleteAndRefeshSearch = function(URIComponents, searchInstance) {
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
	
	var ccm_attributeKeyCategoryItemSelector = {
		options:{
			selectItemDialog:{
				href:CCM_TOOLS_PATH+"/bricks/search_dialog",
				width:"90%",
				height:"70%",
				modal:false
			},
			selectItemParameters:{
				mode:"choose_multiple"	
			}
		},
		_init:function(){
			var I = this;
			
			console.log(this.options);			
			
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
				I.delegateItemActions($(this), $(this));
			});
			
			this.restripe();
			
			this.element.bind("", function(evt){
				console.log(arguments);
			});
			
			//Setup the item selector trigger
			this.element.find(".ccm-attribute-key-category-select-item").bind("click", function(){
				I.openItemSelect();
			});
			
		},
		addItem:function($row, position){
			
			var $item = $row,
				$id = $item.find("input[name=ID]"),
				$existing = this._tbody.find("input[name='"+ this.options.fieldName +"'][value='"+ $id.val() +"']"); //see if this item already exists
			
			if($existing.length){
				$item = $existing.closest("tr").remove();
			}else{
				//Remove uncessary columns
				var $cols = $item.children("td");
					
				$cols.first().remove();
				$cols.last().remove();
				$cols.last().remove();
				$item.append(this.options.itemActionsCell);
				
				$item.children("td").last().append("<input type='hidden' name='"+this.options.fieldName+"' value='"+$id.val()+"' />");
				
				this.delegateItemActions($item, $item);
			}
			
			if(position < 1){
				this._tbody.prepend($item);
			}else if(position == null || position > this._tbody.children("tr").length){
				this._tbody.append($item);	
			}else{
				this._tbody.children("tr").eq(position).insertAfter($item);
			}
			
			var $noneSelected = this._tbody.children(".ccm-attribute-key-category-selected-item-none");
			if($noneSelected.length){
				$noneSelected.remove();	
			}
			
			this.restripe();
			
			this._trigger('addItem', $row, position);
		},
		
		itemActions:{
			remove:function(action, $item, position, widget){
				//widget.removeItem($item.find("input").val());
				$item.remove();
			}
		},
		/*
		createItemAction:function(key, callback){
			
			var $actions = this.template.find(".item-actions"),
				$action = $actions.children("."+key);
			
			//Add action to template, if it doesn't already exist
			if(!$action.length){
				$action = $("<a class=\""+key+"\" title=\""+(key)+"\">"+key+"</a>");
			}
			
			this.itemActions[key] = callback;
			
		},
		*/
		
		delegateItemActions:function($item, $actionsWrap){
			var I = this;
			$actionsWrap.delegate("a", "click", function(){
			
				var $a = $(this);				
				var classes = $a.attr("class") ? $.trim($a.attr("class")).split(/\s+/g) : [];
				console.log(this, classes);
				for(var c = 0; c < classes.length; c++){
					var key = classes[c],
						$item = $a.closest("tr");
					if($.isFunction(I.itemActions[key])){
						var args = [key, $item, $item.prevAll("tr").length, I],
							result = I.itemActions[key].apply($item.get(0), args);
						
						if(result !== false){
							args.splice(0,0,key+"Item");
							I._trigger.apply(I, args);
						}
					}
				}
				
			});
			
		},
		
		openItemSelect:function(opts, params){
			
			var parameters = $.extend({fieldName:this.options.fieldName}, this.options.selectItemParameters, params),
				dialogOpts = $.extend({}, this.options.selectItemDialog, opts);
				
			dialogOpts.href += "?"+$.param(parameters);
			console.log(dialogOpts);
			
			this.takeover_ccm_triggerSelectAttributeKeyCategoryItem();
			jQuery.fn.dialog.open(dialogOpts);
			//this.element.find(".ccm-attribute-key-category-select-item").click();
			
		},
		
		restripe:function(){
			
			var $items = this._tbody.children("tr");
			$items.removeClass("ccm-list-record-alt").filter(":odd").addClass("ccm-list-record-alt");
			this._trigger("restripe");
		},
		
		takeover_ccm_triggerSelectAttributeKeyCategoryItem:function(){
			//IF THERE ARE ISSUES WITH THE ASSET PICKER COMMUNICATION, LOOK HERE FIRST
			var I = this,
				original = typeof(ccm_triggerSelectAttributeKeyCategoryItem)==="undefined" ? undefined : ccm_triggerSelectAttributeKeyCategoryItem;
			window.ccm_triggerSelectAttributeKeyCategoryItem = function(something, $row){
				//I.addItem(fileObj.fID, fileObj.title, fileObj.thumbnailLevel2);
				
				I.addItem($row);
				//Restore the original ccm_chooseAsset
				/* We can't restore it because of the multiple actions dropdown. There will need to be instructions for other integrations to do a similar function takeover
				if(original !== undefined){
					window.ccm_triggerSelectAttributeKeyCategoryItem = original;
				}*/
			};		
		},
		
	};
	
	
	//Create the widget
	$.widget("ccm.ccm_attributeKeyCategoryItemSelector", ccm_attributeKeyCategoryItemSelector);
	
	

})();