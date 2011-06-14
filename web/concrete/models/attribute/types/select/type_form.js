var ccmAttributesHelper={   
	valuesBoxDisabled:function(typeSelect){
		var attrValsInterface=document.getElementById('attributeValuesInterface')
		var requiredVals=document.getElementById('reqValues');
		var allowOther=document.getElementById('allowOtherValuesWrap');
		var offMsg=document.getElementById('attributeValuesOffMsg');
		if (typeSelect.value == 'SELECT' || typeSelect.value == 'SELECT_MULTIPLE') {
			attrValsInterface.style.display='block';
			requiredVals.style.display='inline'; 
			if(allowOther) allowOther.style.display='block';
			offMsg.style.display='none';			
		} else {  
			requiredVals.style.display='none'; 
			attrValsInterface.style.display='none';
			if(allowOther) allowOther.style.display='none';
			offMsg.style.display='block'; 
		}	
	},  
	
	deleteValue:function(val){
		if(confirm(ccmi18n.deleteAttributeValue)) {
			jQuery('#akSelectValueWrap_'+val).remove();				
		}
	},
	
	editValue:function(val){ 
		if(jQuery('#akSelectValueDisplay_'+val).css('display')!='none'){
			jQuery('#akSelectValueDisplay_'+val).css('display','none');
			jQuery('#akSelectValueEdit_'+val).css('display','block');		
		}else{
			jQuery('#akSelectValueDisplay_'+val).css('display','block');
			jQuery('#akSelectValueEdit_'+val).css('display','none');
			var txtValue =  jQuery('#akSelectValueStatic_'+val).html();
			jQuery('#akSelectValueField_'+val).val( jQuery('<div/>').html(txtValue).text());
		}
	},
	
	changeValue:function(val){ 
		var txtValue = jQuery('<div/>').text(jQuery('#akSelectValueField_'+val).val()).html();		
		jQuery('#akSelectValueStatic_'+val).html( txtValue );
		this.editValue(val)
	},
	
	makeSortable: function() {
		jQuery("div#attributeValuesWrap").sortable({
			cursor: 'move',
			opacity: 0.5
		});
	},
	
	saveNewOption:function(){
		var newValF=jQuery('#akSelectValueFieldNew');
		var val = jQuery('<div/>').text(newValF.val()).html();
		if(val=='') {
			return;
		}
		var ts = 't' + new Date().getTime();
		var template=document.getElementById('akSelectValueWrapTemplate'); 
		var newRowEl=document.createElement('div');
		newRowEl.innerHTML=template.innerHTML.replace(/template_clean/ig,ts).replace(/template/ig,val);
		newRowEl.id="akSelectValueWrap_"+ts;
		newRowEl.className='akSelectValueWrap';
		jQuery('#attributeValuesWrap').append(newRowEl);		
		newValF.val(''); 
	},
	
	clrInitTxt:function(field,initText,removeClass,blurred){
		if(blurred && field.value==''){
			field.value=initText;
			jQuery(field).addClass(removeClass);
			return;	
		}
		if(field.value==initText) field.value='';
		if(jQuery(field).hasClass(removeClass)) jQuery(field).removeClass(removeClass);
	},
	
	addEnterClick:function(e,fn){
		/* 
		// this approach is totally !@#&* unreliable in IE because IE sucks
		
		var form = jQuery("#ccm-attribute-key-form");
		form.submit(function() {return false;});
		var keyCode = (e.keyCode ? e.keyCode : e.which);
		if(keyCode == 13 && typeof(fn)=='function' ) {
			fn();
			setTimeout(function() { 
				form.unbind();
			}, 100);
		}
		*/
		
	}
}
