var miniSurvey ={
	bid:0,
	serviceURL: jQuery("input[name=miniSurveyServices]").val() + '?block=form&',
	init: function(){ 
			this.tabSetup();
			this.answerTypes=document.forms['ccm-block-form'].answerType;
			this.answerTypesEdit=document.forms['ccm-block-form'].answerTypeEdit; 

			for(var i=0;i<this.answerTypes.length;i++){
				this.answerTypes[i].onclick=function(){miniSurvey.optionsCheck(this);miniSurvey.settingsCheck(this);}
				this.answerTypes[i].onchange=function(){miniSurvey.optionsCheck(this);miniSurvey.settingsCheck(this);}
			} 
			for(var i=0;i<this.answerTypesEdit.length;i++){
				this.answerTypesEdit[i].onclick=function(){miniSurvey.optionsCheck(this,'Edit');miniSurvey.settingsCheck(this,'Edit');}
				this.answerTypesEdit[i].onchange=function(){miniSurvey.optionsCheck(this,'Edit');miniSurvey.settingsCheck(this,'Edit');}
			} 			
			jQuery('#refreshButton').click( function(){ miniSurvey.refreshSurvey() } );
			jQuery('#addQuestion').click(   function(){ miniSurvey.addQuestion()   } );
			jQuery('#editQuestion').click(  function(){ miniSurvey.addQuestion('Edit')   } );
			jQuery('#cancelEditQuestion').click(   function(){ jQuery('#editQuestionForm').css('display','none') } );			
			this.serviceURL+='cID='+this.cID+'&arHandle='+this.arHandle+'&bID='+this.bID+'&btID='+this.btID+'&';
			miniSurvey.refreshSurvey();
		},	
	tabSetup: function(){
		jQuery('ul#ccm-formblock-tabs li a').each( function(num,el){ 
			el.onclick=function(){
				var pane=this.id.replace('ccm-formblock-tab-','');
				miniSurvey.showPane(pane);
			}
		});		
	},
	showPane:function(pane){
		jQuery('ul#ccm-formblock-tabs li').each(function(num,el){ jQuery(el).removeClass('ccm-nav-active') });
		jQuery(document.getElementById('ccm-formblock-tab-'+pane).parentNode).addClass('ccm-nav-active');
		jQuery('div.ccm-formBlockPane').each(function(num,el){ el.style.display='none'; });
		jQuery('#ccm-formBlockPane-'+pane).css('display','block');
	},
	refreshSurvey : function(){
			jQuery.ajax({ 
					url: this.serviceURL+'mode=refreshSurvey&qsID='+parseInt(this.qsID)+'&hide='+miniSurvey.hideQuestions.join(','),
					success: function(msg){ jQuery('#miniSurveyPreviewWrap').html(msg); }
				});
			jQuery.ajax({ 
					url: this.serviceURL+'mode=refreshSurvey&qsID='+parseInt(this.qsID)+'&showEdit=1&hide='+miniSurvey.hideQuestions.join(','),
					success: function(msg){	jQuery('#miniSurveyWrap').html(msg); }
				});			
		},
	optionsCheck : function(radioButton,mode){
			if(mode!='Edit') mode='';
			if( radioButton.value=='select' || radioButton.value=='radios' || radioButton.value=='checkboxlist'){
				 jQuery('#answerOptionsArea'+mode).css('display','block');
			}else jQuery('#answerOptionsArea'+mode).css('display','none');			
		},
	settingsCheck : function(radioButton,mode){
			if(mode!='Edit') mode='';
			if( radioButton.value=='text'){
				 jQuery('#answerSettings'+mode).css('display','block');
			}else jQuery('#answerSettings'+mode).css('display','none');			
		},
	addQuestion : function(mode){ 
			var msqID=0;
			if(mode!='Edit') mode='';
			else msqID=parseInt(jQuery('#msqID').val())
			var postStr='question='+encodeURIComponent(jQuery('#question'+mode).val())+'&options='+encodeURIComponent(jQuery('#answerOptions'+mode).val());
			postStr+='&width='+escape(jQuery('#width'+mode).val());
			postStr+='&height='+escape(jQuery('#height'+mode).val());
			var req=(jQuery('#required'+mode).get(0).checked)?1:0;
			postStr+='&required='+req;
			postStr+='&position='+escape(jQuery('#position'+mode).val());
			var form=document.getElementById('ccm-block-form'); 
			var opts=form['answerType'+mode];
			var answerType='';
			for(var i=0;i<opts.length;i++){
				if(opts[i].checked){
					answerType=opts[i].value;
					break;
				}
			} 
			postStr+='&inputType='+answerType;//jQuery('input[name=answerType'+mode+']:checked').val()
			postStr+='&msqID='+msqID+'&qsID='+parseInt(this.qsID);			
			jQuery.ajax({ 
					type: "POST",
					data: postStr,
					url: this.serviceURL+'mode=addQuestion&qsID='+parseInt(this.qsID),
					success: function(msg){ 
						eval('var jsonObj='+msg);
						if(!jsonObj){
						   alert(ccm_t('ajax-error'));
						}else if(jsonObj.noRequired){
						   alert(ccm_t('complete-required'));
						}else{
						   if(jsonObj.mode=='Edit'){
							   jQuery('#questionEditedMsg').slideDown('slow');
							   setTimeout("jQuery('#questionEditedMsg').slideUp('slow');",5000);
							   if(jsonObj.hideQID){
								   miniSurvey.hideQuestions.push( miniSurvey.edit_qID ); //jsonObj.hideQID); 
								   miniSurvey.edit_qID=0;
							   }
						   }else{
							   jQuery('#questionAddedMsg').slideDown('slow');
							   setTimeout("jQuery('#questionAddedMsg').slideUp('slow');",5000);
							   //miniSurvey.saveOrder();
						   }
						   jQuery('#editQuestionForm').css('display','none');
						   miniSurvey.qsID=jsonObj.qsID;
						   miniSurvey.ignoreQuestionId(jsonObj.msqID);
						   jQuery('#qsID').val(jsonObj.qsID);
						   miniSurvey.resetQuestion();
						   miniSurvey.refreshSurvey();						  
						   //miniSurvey.showPane('preview');
						}
					}
				});
	},
	//prevent duplication of these questions, for block question versioning
	ignoreQuestionId:function(msqID){
		var msqID, ignoreEl=jQuery('#ccm-ignoreQuestionIDs');
		if(ignoreEl.val()) msqIDs=ignoreEl.val().split(',');
		else msqIDs=[];
		msqIDs.push( parseInt(msqID) );
		ignoreEl.val( msqIDs.join(',') );
	},
	reloadQuestion : function(qID){
			
			jQuery.ajax({ 
				url: this.serviceURL+"mode=getQuestion&qsID="+parseInt(this.qsID)+'&qID='+parseInt(qID),
				success: function(msg){				
						eval('var jsonObj='+msg);
						jQuery('#editQuestionForm').css('display','block')
						jQuery('#questionEdit').val(jsonObj.question);
						jQuery('#answerOptionsEdit').val(jsonObj.optionVals.replace(/%%/g,"\r\n") );
						jQuery('#widthEdit').val(jsonObj.width);
						jQuery('#heightEdit').val(jsonObj.height); 
						jQuery('#positionEdit').val(jsonObj.position); 
						if( parseInt(jsonObj.required)==1 ) 
							 jQuery('#requiredEdit').get(0).checked=true;
						else jQuery('#requiredEdit').get(0).checked=false;
						jQuery('#msqID').val(jsonObj.msqID);    
						for(var i=0;i<miniSurvey.answerTypesEdit.length;i++){							
							if(miniSurvey.answerTypesEdit[i].value==jsonObj.inputType){
								miniSurvey.answerTypesEdit[i].checked=true; 
								miniSurvey.optionsCheck(miniSurvey.answerTypesEdit[i],'Edit');
								miniSurvey.settingsCheck(miniSurvey.answerTypesEdit[i],'Edit');
							}
						}
						if(parseInt(jsonObj.bID)>0) 
							miniSurvey.edit_qID = parseInt(qID) ;
						scroll(0,165);
					}
			});
	},	
	//prevent duplication of these questions, for block question versioning
	pendingDeleteQuestionId:function(msqID){
		var msqID, el=jQuery('#ccm-pendingDeleteIDs');
		if(el.val()) msqIDs=ignoreEl.val().split(',');
		else msqIDs=[];
		msqIDs.push( parseInt(msqID) );
		el.val( msqIDs.join(',') );
	},	
	hideQuestions : [], 
	deleteQuestion : function(el,msqID,qID){
			if(confirm(ccm_t('delete-question'))) { 
				jQuery.ajax({ 
					url: this.serviceURL+"mode=delQuestion&qsID="+parseInt(this.qsID)+'&msqID='+parseInt(msqID),
					success: function(msg){	miniSurvey.resetQuestion(); miniSurvey.refreshSurvey();  }			
				});
				
				miniSurvey.ignoreQuestionId(msqID);
				miniSurvey.hideQuestions.push(qID); 
				miniSurvey.pendingDeleteQuestionId(msqID)
			}
	},
	resetQuestion : function(){
			jQuery('#question').val('');
			jQuery('#answerOptions').val('');
			jQuery('#width').val('50');
			jQuery('#height').val('3');
			jQuery('#msqID').val('');
			for(var i=0;i<this.answerTypes.length;i++){
				this.answerTypes[i].checked=false;
			}
			jQuery('#answerOptionsArea').hide();
			jQuery('#answerSettings').hide();
			jQuery('#required').get(0).checked=0;
	},
	
	validate:function(){
			var failed=0;
			
			var n=jQuery('#ccmSurveyName');
			if( !n || parseInt(n.val().length)==0 ){
				alert(ccm_t('form-name'));
				this.showPane('options');
				n.focus();
				failed=1;
			}
			
			var Qs=jQuery('.miniSurveyQuestionRow'); 
			if( !Qs || parseInt(Qs.length)<1 ){
				alert(ccm_t('form-min-1'));
				failed=1;
			}
			
			if(failed){
				ccm_isBlockError=1;
				return false;
			}
			return true;
	},
	
	moveUp:function(el,thisQID){
		var qIDs=this.serialize();
		var previousQID=0;
		for(var i=0;i<qIDs.length;i++){
			if(qIDs[i]==thisQID){
				if(previousQID==0) break; 
				jQuery('#miniSurveyQuestionRow'+thisQID).after(jQuery('#miniSurveyQuestionRow'+previousQID));
				break;
			}
			previousQID=qIDs[i];
		}	
		this.saveOrder();
	},
	moveDown:function(el,thisQID){
		var qIDs=this.serialize();
		var thisQIDfound=0;
		for(var i=0;i<qIDs.length;i++){
			if(qIDs[i]==thisQID){
				thisQIDfound=1;
				continue;
			}
			if(thisQIDfound){
				jQuery('#miniSurveyQuestionRow'+qIDs[i]).after(jQuery('#miniSurveyQuestionRow'+thisQID));
				break;
			}
		}
		this.saveOrder();
	},
	serialize:function(){
		var t = document.getElementById("miniSurveyPreviewTable");
		var qIDs=[];
		for(var i=0;i<t.childNodes.length;i++){ 
			if( t.childNodes[i].className && t.childNodes[i].className.indexOf('miniSurveyQuestionRow')>=0 ){ 
				var qID=t.childNodes[i].id.substr('miniSurveyQuestionRow'.length);
				qIDs.push(qID);
			}
		}
		return qIDs;
	},
	saveOrder:function(){ 
		var postStr='qIDs='+this.serialize().join(',')+'&qsID='+parseInt(this.qsID);
		jQuery.ajax({ 
			type: "POST",
			data: postStr,
			url: this.serviceURL+"mode=reorderQuestions",			
			success: function(msg){	
				miniSurvey.refreshSurvey();
			}			
		});
	},
	showRecipient:function(cb){ 
		if(cb.checked) jQuery('#recipientEmailWrap').css('display','block');
		else jQuery('#recipientEmailWrap').css('display','none');
	}
}
ccmValidateBlockForm = function() { return miniSurvey.validate(); }
jQuery(document).ready(function(){
	//miniSurvey.init();
	jQuery('#ccm-form-redirect').change(function() {
		if(jQuery(this).is(':checked')) {
			jQuery('#ccm-form-redirect-page').show();
		} else {
			jQuery('#ccm-form-redirect-page').hide();
		}
	});
		
});
