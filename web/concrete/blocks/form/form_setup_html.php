<?php 
defined('C5_EXECUTE') or die("Access Denied.");
/* @var $uh ConcreteUrlsHelper */ 
$uh = Loader::helper('concrete/urls');
/* @var $form FormHelper */
$form = Loader::helper('form');
/* @var $ih ConcreteInterfaceHelper */
$ih = Loader::helper('concrete/interface');
?>
<div class="ccm-ui">
	<ul class="tabs" id="ccm-formblock-tabs">
		<li class="<?php echo (intval($miniSurveyInfo['bID'])==0)?'active':''?>"><a href="javascript:void(0)" id="ccm-formblock-tab-add"><?php echo t('Add')?></a></li>
		<li class="<?php echo (intval($miniSurveyInfo['bID'])>0)?'active':''?>"><a href="javascript:void(0)" id="ccm-formblock-tab-edit"><?php echo t('Edit')?></a></li>
		<li><a href="javascript:void(0)" id="ccm-formblock-tab-preview"><?php echo t('Preview')?></a></li>
		<li><a href="javascript:void(0)" id="ccm-formblock-tab-options"><?php echo t('Options')?></a></li>
	</ul>
	<div class="spacer"></div>
	
	<input type="hidden" name="miniSurveyServices" value="<?php echo $uh->getBlockTypeToolsURL($bt)?>/services.php" />
	
	<?php /* these question ids have been deleted, or edited, and so shouldn't be duplicated for block versioning */ ?>
	<input type="hidden" id="ccm-ignoreQuestionIDs" name="ignoreQuestionIDs" value="" />
	<input type="hidden" id="ccm-pendingDeleteIDs" name="pendingDeleteIDs" value="" />
	
	<div id="ccm-formBlockPane-options" class="ccm-formBlockPane">
		<?php 
		$c = Page::getCurrentPage();
		if(strlen($miniSurveyInfo['surveyName'])==0)
			$miniSurveyInfo['surveyName']=$c->getCollectionName();
		?>
		<fieldset>
			<legend><?php echo t('Options')?></legend>
			<div class="clearfix">
				<?php echo $form->label('surveyName', t('Form Name'))?>
				<div class="input">
					<?php echo $form->text('surveyName', $miniSurveyInfo['surveyName'])?>
				</div>
			</div>
			<div class="clearfix">
				<?php echo $form->label('thankyouMsg', t('Message to display when completed'))?>
				<div class="input">
					<?php echo $form->textarea('thankyouMsg', $this->controller->thankyouMsg, array('rows' => 3))?>
				</div>
			</div>
			<div class="clearfix">
				<?php echo $form->label('recipientEmail', t('Notify me by email when people submit this form'))?>
				<div class="input">
					<div class="input-prepend">
						<label>
						<span class="add-on" style="z-index: 2000">
							<?php echo $form->checkbox('notifyMeOnSubmission', 1, $miniSurveyInfo['notifyMeOnSubmission'] == 1, array('onclick' => "$('input[name=recipientEmail]').focus()"))?>
						</span><?php echo $form->text('recipientEmail', $miniSurveyInfo['recipientEmail'], array('style' => 'z-index:2000;' ))?>
						</label>
					</div>

					<span class="help-block"><?php echo t('(Seperate multiple emails with a comma)')?></span>
				</div>
			</div>
			<div class="clearfix">
				<label><?php echo t('Solving a <a href="%s" target="_blank">CAPTCHA</a> Required to Post?', 'http://en.wikipedia.org/wiki/Captcha')?></label>
				<div class="input">
					<ul class="inputs-list" id="displayCaptcha">
						<li>
							<label>
								<?php echo $form->radio('displayCaptcha', 1, (int) $miniSurveyInfo['displayCaptcha'])?>
								<span><?php echo t('Yes')?></span>
							</label>
						</li>
						<li>
							<label>
								<?php echo $form->radio('displayCaptcha', 0, (int) $miniSurveyInfo['displayCaptcha'])?>
								<span><?php echo t('No')?></span>
							</label>
						</li>
					</ul>
				</div>
			</div>
			<div class="clearfix">
				<label for="ccm-form-redirect"><?php echo t('Redirect to another page after form submission?')?></label>
				<div class="input">
					<div id="ccm-form-redirect-page">
						<?php
							$page_selector = Loader::helper('form/page_selector');
							if ($miniSurveyInfo['redirectCID']) {
								print $page_selector->selectPage('redirectCID', $miniSurveyInfo['redirectCID']);
							} else {
								print $page_selector->selectPage('redirectCID');
							}
						?>
					</div>
				</div>
			</div>
			<div class="clearfix">
				<label for="ccm-form-fileset"><?php echo t('Add uploaded files to a set?')?></label>
				<div class="input">
					<div id="ccm-form-fileset">
						<?php
							Loader::model('file_set');
							$fs = new FileSet();
							$fileSets = $fs->getMySets();
							$sets = array(0 => t('None'));
							foreach($fileSets as $fileSet) {
								$sets[$fileSet->fsID] = $fileSet->fsName;
							}
							print $form->select('addFilesToSet', $sets, $miniSurveyInfo['addFilesToSet']);
						?>
					</div>
				</div>
			</div>
		</fieldset>
	</div> 
	
	<input type="hidden" id="qsID" name="qsID" type="text" value="<?php echo intval($miniSurveyInfo['questionSetId'])?>" />
	<input type="hidden" id="oldQsID" name="oldQsID" type="text" value="<?php echo intval($miniSurveyInfo['questionSetId'])?>" />
	<input type="hidden" id="msqID" name="msqID" type="text" value="<?php echo intval($msqID)?>" />
	
	<div id="ccm-formBlockPane-add" class="ccm-formBlockPane" style=" <?php echo (intval($miniSurveyInfo['bID'])==0)?'display:block':''?> ">
		
	
		<fieldset id="newQuestionBox">
			<legend id="addNewQuestionTitle"><?php echo t('Add a New Question')?></legend>		
			
			<div id="questionAddedMsg" class="alert-message" style="display:none">
				<?php echo t('Your question has been added. To view it click the preview tab.')?>
			</div>

			<div class="clearfix">
				<?php echo $form->label('question', t('Question'))?>
				<div class="input">
					<?php echo $form->text('question')?>
				</div>
			</div>
			<div class="clearfix">
				<label><?php echo t('Answer Type')?></label>
				<div class="input">
					<select class="inputs-list span4" name="answerType" id="answerType">
						<option value="field"><?php echo t('Text Field')?></option>
						<option value="text"><?php echo t('Text Area')?></option>
						<option value="radios"><?php echo t('Radio Buttons')?></option>
						<option value="select"><?php echo t('Select Box')?></option>
						<option value="checkboxlist"><?php echo t('Checkbox List')?></option>
						<option value="fileupload"><?php echo t('File Upload')?></option>
						<option value="email"><?php echo t('Email Address')?></option>
						<option value="telephone"><?php echo t('Telephone')?></option>
						<option value="url"><?php echo t('Web Address')?></option>
						<option value="date"><?php echo t('Date Field')?></option>
						<option value="datetime"><?php echo t('DateTime Field')?></option>
					</select>
				</div>
			</div>
			
			<div id="answerOptionsArea">
				<div class="clearfix">
					<?php echo $form->label('answerOptions', t('Answer Options'))?>
					<div class="input">
						<?php echo $form->textarea('answerOptions', array('rows' => 3))?>
						<span class="help-block"><?php echo t('Put each answer options on a new line')?></span>
					</div>
				</div>
			</div>

			<div id="answerSettings">
				<div class="clearfix">
					<?php echo $form->label('width', t('Text Area Width'))?>
					<div class="input">
						<?php echo $form->text('width', 50)?>
					</div>
				</div>
				<div class="clearfix">
					<?php echo $form->label('height', t('Text Area Height'))?>
					<div class="input">
						<?php echo $form->text('height', 3)?>
					</div>
				</div>
			</div>

			<div class="clearfix">
				<label><?php echo t('Required')?></label>
				<div class="input">
					<ul class="inputs-list" id="required">
						<li><label>
							<?php echo $form->radio('required', 1)?>
							<span><?php echo t('Yes')?></span>
						</label></li>
						<li><label>
							<?php echo $form->radio('required', 0)?>
							<span><?php echo t('No')?></span>
						</label></li>
					</ul>
				</div>
			</div>

			<div class="clearfix">
				<div id="emailSettings">
					<?php print $form->label('send_notification_from', t('Reply to this email address'));?>
					<div class="input send_notification_from">
						<?php print $form->checkbox('send_notification_from', 1); ?>
					</div>
				</div>
			</div>

			<div class="clearfix">
			<label></label>
			<div class="input">
				<?php echo $ih->button(t('Refresh'), '#', 'left', '', array('style' => 'display:none', 'id' => 'refreshButton'))?>
				<?php echo $ih->button(t('Add Question'), '#', '', '', array('id' => 'addQuestion'))?>
			</div>
			</div>
			
		</fieldset> 
	</div> 
		
	<div id="ccm-formBlockPane-edit" class="ccm-formBlockPane" style=" <?php echo (intval($miniSurveyInfo['bID'])>0)?'display:block':''?> ">
		
		<div id="questionEditedMsg" class="alert-message" style="display:none">
			<?php echo t('Your question has been edited.')?>
		</div>
		
		<div id="editQuestionForm" style="display:none">
			<fieldset>
				<legend id="editQuestionTitle"><?php echo t('Edit Question')?></legend>
				<div class="clearfix">
					<?php echo $form->label('question', t('Question'))?>
					<div class="input">
						<?php echo $form->text('questionEdit')?>
					</div>
				</div>

				<div class="clearfix">
					<label><?php echo t('Answer Type')?></label>
					<div class="input">
						<select class="inputs-list span4" name="answerTypeEdit" id="answerTypeEdit">
							<option value="field"><?php echo t('Text Field')?></option>
							<option value="text"><?php echo t('Text Area')?></option>
							<option value="radios"><?php echo t('Radio Buttons')?></option>
							<option value="select"><?php echo t('Select Box')?></option>
							<option value="checkboxlist"><?php echo t('Checkbox List')?></option>
							<option value="fileupload"><?php echo t('File Upload')?></option>
							<option value="email"><?php echo t('Email Address')?></option>
							<option value="telephone"><?php echo t('Telephone')?></option>
							<option value="url"><?php echo t('Web Address')?></option>
							<option value="date"><?php echo t('Date Field')?></option>
							<option value="datetime"><?php echo t('DateTime Field')?></option>
						</select>
					</div>
				</div>

				<div id="answerOptionsAreaEdit">
					<div class="clearfix">
						<?php echo $form->label('answerOptionsEdit', t('Answer Options'))?>
						<div class="input">
							<?php echo $form->textarea('answerOptionsEdit', array('rows' => 3))?>
							<span class="help-block"><?php echo t('Put each answer options on a new line')?></span>
						</div>
					</div>
				</div>
				
				<div id="answerSettingsEdit">
					<div class="clearfix">
						<?php echo $form->label('widthEdit', t('Text Area Width'))?>
						<div class="input">
							<?php echo $form->text('widthEdit', 50)?>
						</div>
					</div>
					<div class="clearfix">
						<?php echo $form->label('heightEdit', t('Text Area Height'))?>
						<div class="input">
							<?php echo $form->text('heightEdit', 3)?>
						</div>
					</div>
				</div>

				<div class="clearfix">
					<label><?php echo t('Required')?> </label>
					<div class="input">
						<ul class="inputs-list" id="requiredEdit">
							<li>
								<label>
									<?php echo $form->radio('requiredEdit', 1)?>
									<span><?php echo t('Yes')?></span>
								</label>
							</li>
							<li>
								<label>
									<?php echo $form->radio('requiredEdit', 0)?>
									<span><?php echo t('No')?> </span>
								</label>
							</li>
						</ul>
					</div>
				</div>

				<div id="emailSettingsEdit">
					<?php print $form->label('send_notification_from_edit', t('Reply to this email address'));?>
					<div class="input send_notification_from">
						<?php print $form->checkbox('send_notification_from_edit', 1); ?>
					</div>
				</div>
			</fieldset>
			
			<input type="hidden" id="positionEdit" name="position" type="text" value="1000" />
			
			<div>
				<?php echo $ih->button(t('Cancel'), 'javascript:void(0)', 'left', '', array('id' => 'cancelEditQuestion'))?>
				<?php echo $ih->button(t('Save Changes'), 'javascript:void(0)', 'right', 'primary', array('id' => 'editQuestion'))?>
			</div>
		</div>
	
		<div id="miniSurvey">
			<div style="margin-bottom:16px"><strong><?php echo t('Edit')?>:</strong>	</div>
			<div id="miniSurveyWrap"></div>
		</div>
	</div>	
		
	<div id="ccm-formBlockPane-preview" class="ccm-formBlockPane">
		<div id="miniSurvey">
			<div style="margin-bottom:16px"><strong><?php echo t('Preview')?>:</strong></div>	
			<div id="miniSurveyPreviewWrap"></div>
		</div>
	</div>
</div>
<script>
//safari was loading the auto.js too late. This ensures it's initialized
function initFormBlockWhenReady(){
	if(miniSurvey && typeof(miniSurvey.init)=='function'){
		miniSurvey.cID=parseInt(<?php echo $c->getCollectionID()?>);
		miniSurvey.arHandle="<?php echo $a->getAreaHandle()?>";
		miniSurvey.bID=thisbID;
		miniSurvey.btID=thisbtID;
		miniSurvey.qsID=parseInt(<?php echo $miniSurveyInfo['questionSetId']?>);	
		miniSurvey.init();
		miniSurvey.refreshSurvey();
	}else setTimeout('initFormBlockWhenReady()',100);
}
initFormBlockWhenReady();
</script>
