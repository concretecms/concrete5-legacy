<?php
defined('C5_EXECUTE') or die("Access Denied.");
$section = 'groups';

$txt = Loader::helper('text');
$ih = Loader::helper('concrete/interface');
$valt = Loader::helper('validation/token');

$date = Loader::helper('form/date_time');
$form = Loader::helper('form');

?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Add Group'), false, false, false)?>
<form class="form-horizontal" method="post" id="add-group-form" action="<?php echo $this->url('/dashboard/users/add_group/', 'do_add')?>">
<div class="ccm-pane-body">
<?php echo $valt->output('add_or_update_group')?>
<fieldset>
<div class="control-group">
<?php echo $form->label('gName', t('Name'))?>
<div class="controls">
	<input type="text" name="gName" class="span6" value="<?php echo Loader::helper('text')->entities($_POST['gName'])?>" />
</div>
</div>

<div class="control-group">
<?php echo $form->label('gDescription', t('Description'))?>
<div class="controls">
	<?php echo $form->textarea('gDescription', array('rows' => 6, 'class' =>'span6'))?>
</div>
</div>
</fieldset>
<fieldset>
<legend><?php echo t("Group Expiration Options")?></legend>

<div class="control-group">
	<div class="controls">
	<label class="checkbox">
	<?php echo $form->checkbox('gUserExpirationIsEnabled', 1, false)?>
	<span><?php echo t('Automatically remove users from this group')?></span></label>
	</div>
	
	<div class="controls" style="padding-left:18px">
	<?php echo $form->select("gUserExpirationMethod", array(
		'SET_TIME' => t('at a specific date and time'),
			'INTERVAL' => t('once a certain amount of time has passed')
		
	), array('disabled' => true));?>	
	</div>	
</div>

<div id="gUserExpirationSetTimeOptions" style="display: none">
<div class="control-group">
<?php echo $form->label('gUserExpirationSetDateTime', t('Expiration Date'))?>
<div class="controls">
<?php echo $date->datetime('gUserExpirationSetDateTime')?>
</div>
</div>
</div>
<div id="gUserExpirationIntervalOptions" style="display: none">
<div class="control-group">
<label><?php echo t('Accounts expire after')?></label>
<div class="controls">
<table class="table table-condensed" style="width: auto">
<tr>
<td valign="top"><strong><?php echo t('Days')?></strong><br/>
<?php echo $form->text('gUserExpirationIntervalDays', array('style' => $style, 'class' => 'span1'))?>
</td>
<td valign="top"><strong><?php echo t('Hours')?></strong><br/>
<?php echo $form->text('gUserExpirationIntervalHours', array('style' => $style, 'class' => 'span1'))?>
</td>
<td valign="top"><strong><?php echo t('Minutes')?></strong><br/>
<?php echo $form->text('gUserExpirationIntervalMinutes', array('style' => $style, 'class' => 'span1'))?>
</td>
</tr>
</table>
</div>
</div>
</div>

<div id="gUserExpirationAction" style="display: none">
<div class="clearfix">
<?php echo $form->label('gUserExpirationAction', t('Expiration Action'))?>
<div class="input">
<?php echo $form->select("gUserExpirationAction", array(
'REMOVE' => t('Remove the user from this group'),
	'DEACTIVATE' => t('Deactivate the user account'),
	'REMOVE_DEACTIVATE' => t('Remove the user from the group and deactivate the account')

));?>	
</div>
</div>
</div>
</fieldset>
</div>

<div class="ccm-pane-footer">
<input type="hidden" name="add" value="1" /><input type="submit" name="submit" value="<?php echo t('Add')?>" class="btn ccm-button-right primary" />
</div>

</form>	
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>

<script type="text/javascript">
ccm_checkGroupExpirationOptions = function() {
	var sel = $("select[name=gUserExpirationMethod]");
	var cb = $("input[name=gUserExpirationIsEnabled]");
	if (cb.prop('checked')) {
		sel.attr('disabled', false);
		switch(sel.val()) {
			case 'SET_TIME':
				$("#gUserExpirationSetTimeOptions").show();
				$("#gUserExpirationIntervalOptions").hide();
				break;
			case 'INTERVAL': 
				$("#gUserExpirationSetTimeOptions").hide();
				$("#gUserExpirationIntervalOptions").show();
				break;				
		}
		$("#gUserExpirationAction").show();
	} else {
		sel.attr('disabled', true);	
		$("#gUserExpirationSetTimeOptions").hide();
		$("#gUserExpirationIntervalOptions").hide();
		$("#gUserExpirationAction").hide();
	}
}

$(function() {
	$("input[name=gUserExpirationIsEnabled]").click(ccm_checkGroupExpirationOptions);
	$("select[name=gUserExpirationMethod]").change(ccm_checkGroupExpirationOptions);
	ccm_checkGroupExpirationOptions();
});
</script>