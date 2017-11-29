<?php
defined('C5_EXECUTE') or die("Access Denied.");
$section = 'groups';

function checkExpirationOptions($g) {
	if ($_POST['gUserExpirationIsEnabled']) {
		$date = Loader::helper('form/date_time');
		switch($_POST['gUserExpirationMethod']) {
			case 'SET_TIME':
				$g->setGroupExpirationByDateTime($date->translate('gUserExpirationSetDateTime'), $_POST['gUserExpirationAction']);
				break;
			case 'INTERVAL':
				$g->setGroupExpirationByInterval($_POST['gUserExpirationIntervalDays'], $_POST['gUserExpirationIntervalHours'], $_POST['gUserExpirationIntervalMinutes'], $_POST['gUserExpirationAction']);
				break;
		}
	} else {
		$g->removeGroupExpiration();
	}
}

if ($_REQUEST['task'] == 'edit') {
	$g = Group::getByID(intval($_REQUEST['gID']));
	if (is_object($g)) { 		
		if ($_POST['update']) {
		
			$gName = $_POST['gName'];
			$gDescription = $_POST['gDescription'];
			
		} else {
			
			$gName = $g->getGroupName();
			$gDescription = $g->getGroupDescription();
		
		}
		
		$editMode = true;
	}
}

$txt = Loader::helper('text');
$ih = Loader::helper('concrete/interface');
$valt = Loader::helper('validation/token');

if (!$editMode) {

Loader::model('search/group');
$gl = new GroupSearch();
if (isset($_GET['gKeywords'])) {
	$gl->filterByKeywords($_GET['gKeywords']);
}

$gResults = $gl->getPage();

?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Groups'), false, 'span10 offset1', false)?>
<?php
$tp = new TaskPermission();
if ($tp->canAccessGroupSearch()) { ?>

<div class="ccm-pane-options">
<form method="get" class="form-horizontal" action="<?php echo $this->url('/dashboard/users/groups')?>">
<div class="ccm-pane-options-permanent-search">
<div class="span8">
<?php $form = Loader::helper('form'); ?>
<?php echo $form->label('gKeywords', t('Keywords'))?>
<div class="controls">
	<input type="text" name="gKeywords" value="<?php echo Loader::helper('text')->entities($_REQUEST['gKeywords'])?>"  />
	<input class="btn" type="submit" value="<?php echo t('Search')?>" />
</div>
<input type="hidden" name="group_submit_search" value="1" />
</div>
</div>
</form>
</div>
<div class="ccm-pane-body <?php if (!$gl->requiresPaging()) { ?> ccm-pane-body-footer <?php } ?>">

	<a href="<?php echo View::url('/dashboard/users/add_group')?>" style="float: right; position:relative;top:-5px"  class="btn primary"><?php echo t("Add Group")?></a>

<?php if (count($gResults) > 0) { 
	$gl->displaySummary();
$gp = new Permissions();
$canEditGroups = $gp->canEditGroups();
?>

	<style type="text/css">
	div.ccm-paging-top {padding-bottom:10px;}
	</style>

<?php
	
foreach ($gResults as $g) { ?>
	
	<div class="ccm-group">
		<<?php if ($canEditGroups) { ?>a<?php } else {?>span<?php } ?> class="ccm-group-inner" <?php if ($canEditGroups) { ?>href="<?php echo $this->url('/dashboard/users/groups?task=edit&gID=' . $g['gID'])?>"<?php } ?> style="background-image: url(<?php echo ASSETS_URL_IMAGES?>/icons/group.png)"><?php echo h(tc('GroupName', $g['gName']))?><?php if ($canEditGroups) { ?></a><?php } else {?></span><?php } ?>
		<?php if ($g['gDescription']) { ?>
			<div class="ccm-group-description"><?php echo h(tc('GroupDescription', $g['gDescription']))?></div>
		<?php } ?>
	</div>


<?php }

} else { ?>

	<p><?php echo t('No groups found.')?></p>
	
<?php } ?>
</div>
<?php if ($gl->requiresPaging()) { ?>
<div class="ccm-pane-footer">
	<?php echo $gl->displayPagingV2();?>
</div>
<?php } ?>

<?php } else { ?>
<div class="ccm-pane-body ccm-pane-body-footer">
	<p><?php echo t('You do not have access to group search. This setting may be changed in the access section of the dashboard settings page.')?></p>
</div>
<?php } ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>

<?php } else { ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Group'), false, false, false)?>
<form method="post"  class="form-horizontal" id="update-group-form" action="<?php echo $this->url('/dashboard/users/groups/', 'update_group')?>">
<?php echo $valt->output('add_or_update_group')?>
<div class="ccm-pane-body">
	<?php
	$form = Loader::helper('form');
	$date = Loader::helper('form/date_time');
	$u=new User();

	$delConfirmJS = t('Are you sure you want to permanently remove this group?');
	if($u->isSuperUser() == false){ ?>
		<?php echo t('You must be logged in as %s to remove groups.', USER_SUPER)?>			
	<?php }else{ ?>   

		<script type="text/javascript">
		deleteGroup = function() {
			if (confirm('<?php echo $delConfirmJS?>')) { 
				location.href = "<?php echo $this->url('/dashboard/users/groups', 'delete', intval($_REQUEST['gID']), $valt->generate('delete_group_' . intval($_REQUEST['gID']) ))?>";				
			}
		}
		</script>

	<?php } ?>

	<fieldset>
	<div class="control-group">
	<?php echo $form->label('gName', t('Name'))?>
	<div class="controls">
		<input type="text" name="gName" class="span6" value="<?php echo Loader::helper('text')->entities($gName)?>" />
	</div>
	</div>
	
	<div class="control-group">
	<?php echo $form->label('gDescription', t('Description'))?>
	<div class="controls">
		<textarea name="gDescription" rows="6" class="span6"><?php echo Loader::helper("text")->entities($gDescription)?></textarea>
	</div>
	</div>
	</fieldset>
	<fieldset>
	<legend><?php echo t("Group Expiration Options")?></legend>
	<div class="control-group">
	<div class="controls">

		<label class="checkbox">
		<?php echo $form->checkbox('gUserExpirationIsEnabled', 1, $g->isGroupExpirationEnabled())?>
		<span><?php echo t('Automatically remove users from this group')?></span></label>
		
	</div>
	
	<div class="controls" style="padding-left: 18px">
		<?php echo $form->select("gUserExpirationMethod", array(
			'SET_TIME' => t('at a specific date and time'),
				'INTERVAL' => t('once a certain amount of time has passed')
			
		), $g->getGroupExpirationMethod(), array('disabled' => true));?>	
	</div>	
	</div>
	
	
	<div id="gUserExpirationSetTimeOptions" style="display: none">
	<div class="control-group">
	<?php echo $form->label('gUserExpirationSetDateTime', t('Expiration Date'))?>
	<div class="controls">
	<?php echo $date->datetime('gUserExpirationSetDateTime', $g->getGroupExpirationDateTime())?>
	</div>
	</div>
	</div>
	<div id="gUserExpirationIntervalOptions" style="display: none">
	<div class="control-group">
	<label><?php echo t('Accounts expire after')?></label>
	<div class="controls">
	<table class="table table-condensed" style="width: auto">
	<tr>
	<?php
	$days = $g->getGroupExpirationIntervalDays();
	$hours = $g->getGroupExpirationIntervalHours();
	$minutes = $g->getGroupExpirationIntervalMinutes();
	$style = 'width: 60px';
	?>
	<td valign="top"><strong><?php echo t('Days')?></strong><br/>
	<?php echo $form->text('gUserExpirationIntervalDays', $days, array('style' => $style, 'class' => 'span1'))?>
	</td>
	<td valign="top"><strong><?php echo t('Hours')?></strong><br/>
	<?php echo $form->text('gUserExpirationIntervalHours', $hours, array('style' => $style, 'class' => 'span1'))?>
	</td>
	<td valign="top"><strong><?php echo t('Minutes')?></strong><br/>
	<?php echo $form->text('gUserExpirationIntervalMinutes', $minutes, array('style' => $style, 'class' => 'span1'))?>
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
	), $g->getGroupExpirationAction());?>	
	</div>
	</div>
	</div>
	<input type="hidden" name="gID" value="<?php echo intval($_REQUEST['gID'])?>" />
	<input type="hidden" name="task" value="edit" />
	</fieldset>
</div>
<div class="ccm-pane-footer">
	<?php echo $ih->submit(t('Update'), 'update-group-form', 'right', 'primary')?>
	<?php print $ih->button_js(t('Delete'), "deleteGroup()", 'right', 'error');?>
	<?php echo $ih->button(t('Cancel'), $this->url('/dashboard/users/groups'), 'left')?>
</div>
</form>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>
<?php } ?>

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
	/*
	$("div#gUserExpirationIntervalOptions input").focus(function() {
		if ($('input[name=gUserExpirationIntervalDays]').val() == '<?php echo t("Days")?>' &&
			$('input[name=gUserExpirationIntervalHours]').val() == '<?php echo t("Hours")?>' &&
			$('input[name=gUserExpirationIntervalMinutes]').val() == '<?php echo t("Minutes")?>') {
			$("div#gUserExpirationIntervalOptions input").val("");
			$("div#gUserExpirationIntervalOptions input").css('color', '#000');
		}
	});
	$("div#gUserExpirationIntervalOptions input").blur(function() {
		if ($('input[name=gUserExpirationIntervalDays]').val() == '' &&
			$('input[name=gUserExpirationIntervalHours]').val() == '' &&
			$('input[name=gUserExpirationIntervalMinutes]').val() == '') {
			$('input[name=gUserExpirationIntervalDays]').val('<?php echo t("Days")?>');
			$('input[name=gUserExpirationIntervalHours]').val('<?php echo t("Hours")?>');
			$('input[name=gUserExpirationIntervalMinutes]').val('<?php echo t("Minutes")?>');
			$("div#gUserExpirationIntervalOptions input").css('color', '#aaa');
		}
	});
	*/
});
</script>

