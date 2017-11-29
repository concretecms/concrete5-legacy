<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$form = Loader::helper("form");
$u = new User();
$date = Loader::helper('form/date_time');
$us = Loader::helper('form/user_selector'); ?>

<div class="ccm-ui">
<div class="ccm-pane-options">
<div class="ccm-pane-options-permanent-search">
<form id="ccm-collection-preview-as-user-form" class="form-horizontal" method="get" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/pages/preview_as_user" target="ccm-collection-preview-as-user-frame">
	<input type="hidden" name="cID" value="<?php echo $c->getCollectionID()?>" />
	<div class="control-group">
	
	<label class="control-label"><?php echo t('Preview As')?></label>
	<div class="controls">
		<label class="radio inline"><input type="radio" value="guest" name="ccm-collection-preview-as" checked="checked" /> <?php echo t('Guest')?></label>
		<label class="radio inline"><input type="radio" value="registered" name="ccm-collection-preview-as" /> <?php echo t('Registered User')?>

		</label>
		
		&nbsp;&nbsp;
		<?php echo $us->quickSelect('customUser', $u->getUserName(), array('class' => 'span3', 'disabled' => 'disabled'))?>

	</div>
	</div>

	<div class="control-group">
	<?php echo $form->label('onDate_dt', t('On Date'))?>
	<div class="controls">
		<?php echo $date->datetime('onDate')?>
		<input type="submit" value="<?php echo t('Go')?>" class="btn" />
	</div>
	</div>		

</form>
</div>

<?php
$assignments = $cp->getAllTimedAssignmentsForPage();
if (count($assignments) > 0) { ?>
	<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup.php?cID=<?php echo $c->getCollectionID()?>&ctask=view_timed_permission_list" dialog-width="500" dialog-height="300" dialog-title="<?php echo t('View Timed Permission Assignments')?>" class="dialog-launch" onclick="" id="ccm-list-view-customize-top"><span class="ccm-menu-icon ccm-icon-clock"></span><?php 
		if (count($assignments) == 1) { ?><?php echo t('1 Timed Permission Found')?><?php } else { ?><?php echo t('%s Timed Permissions Found', count($assignments))?><?php } ?></a>
<?php } ?>
</div>
<br/>
<iframe width="100%" height="200" style="border: 0px" border="0" frameborder="0" id="ccm-collection-preview-as-user-frame" name="ccm-collection-preview-as-user-frame" src="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/pages/preview_as_user?cID=<?php echo $c->getCollectionID()?>"></iframe>

</div>


<script type="text/javascript">
$(function() {
	$('input[name=ccm-collection-preview-as]').change(function() {
		if ($(this).val() == 'registered') { 
			$('input[name=customUser]').prop('disabled', false);
		} else { 
			$('input[name=customUser]').prop('disabled', true);
		}
	});
	
	var h = $('#ccm-collection-preview-as-user-form').closest('.ui-dialog-content').height();
	h = h - 120;
	$('#ccm-collection-preview-as-user-frame').css('height', h + 'px');
});
</script>