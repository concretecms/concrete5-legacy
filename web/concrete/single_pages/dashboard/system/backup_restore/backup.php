<?php defined('C5_EXECUTE') or die('Access Denied.');

/* @var $dh DateHelper */
$dh = Loader::helper('date');

?>
<script type="text/javascript">
	dNum = 0;
	function confirmDelete(strFn) {
		//   var ele = $('#confirmDelete').clone().attr('id','confirmDelete'+dNum);
		//   $('body').append($('#confirmDelete'+dNum)); 
		$('#confirmDelete').clone().attr('id', 'confirmDelete'+dNum).appendTo('body');
		var alink = $('#confirmDelete' + dNum + ' input[name=backup_file]').val(strFn); 
		$('#confirmDelete' + dNum).dialog({width: 500, height: 200, title: "<?php echo t("Confirm Delete"); ?>", buttons:[{}], 'open': function() {
			$(this).parent().find('.ui-dialog-buttonpane').addClass("ccm-ui").html('');
			$(this).find('.dialog-buttons').appendTo($(this).parent().find('.ui-dialog-buttonpane'));
			$(this).find('.dialog-buttons').remove();
			}
		});
		dNum++;
	}

	rNum = 0;
	function confirmRestore(strFn) {
		//   var ele = $('#confirmDelete').clone().attr('id','confirmDelete'+rNum);
		//   $('body').append($('#confirmDelete'+rNum)); 
		$('#confirmRestore').clone().attr('id', 'confirmRestore'+rNum).appendTo('body');
		var alink = $('#confirmRestore' + rNum + ' input[name=backup_file]').val(strFn); 
		$('#confirmRestore' + rNum + ' .confirmActionBtn a').attr('href',alink); 
		$('#confirmRestore' + rNum).dialog({width:500, height: 200, title: "<?php echo t("Are you sure?"); ?>", buttons:[{}], 'open': function() {
			$(this).parent().find('.ui-dialog-buttonpane').addClass("ccm-ui").html('');
			$(this).find('.dialog-buttons').appendTo($(this).parent().find('.ui-dialog-buttonpane'));
			$(this).find('.dialog-buttons').remove();
			}
		});
		rNum++;
	}
	$(document).ready(function () {
		$('#executeBackup').click( function() { 
			if ($('#useEncryption').is(':checked')) {
				window.location.href = $(this).attr('href')+$('#useEncryption').val();
				return false;
			}
		});


		if ($.cookie('useEncryption') == "1" ) {
			$('#useEncryption').attr('checked','checked');
		}

		$('#useEncryption').change(function() {
			if ($('#useEncryption').is(':checked')) {
				$.cookie('useEncryption','1');
			} else {
				$.cookie('useEncryption','0');

			}
		}); 
	});

</script>

<!--Dialog -->
<div id="confirmDelete" style="display:none" class="ccm-ui">
		<p><?php echo t('This action <strong>cannot be undone</strong>. Are you sure?') ?></p>
		<div class="dialog-buttons">
			<form method="post" action="<?php echo $this->action('delete_backup') ?>" style="display: inline">
			<input type="hidden" name="backup_file" value="" />
			<?php echo $interface->submit(t('Delete Backup'), false, 'right', 'error'); ?>
	</form>
		</div> 
</div>

<!-- End of Dialog //-->

<!--Dialog -->
<div id="confirmRestore" style="display:none" class="ccm-ui">
		<p><?php echo t('This action <strong>cannot be undone</strong>. Are you sure?') ?></p>
		<div class="dialog-buttons">
			<form method="post" action="<?php echo $this->action('restore_backup') ?>" style="display: inline">	
			<input type="hidden" name="backup_file" value="" />
			<?php echo $interface->submit(t('Restore Backup'), false, 'right', 'primary'); ?>
		</form>
		</div> 
</div>

<!-- End of Dialog //-->


<?php
$tp = new TaskPermission();
if ($tp->canBackup()) {
	?>
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Backup Database'), false, 'span10 offset1') ?>
		<h3><?php echo t('Existing Backups')?></h3>
		<?php
		if (count($backups) > 0) {
			?>
			<table class="table table-striped" cellspacing="1" cellpadding="0" border="0">
				<thead>
					<tr>
						<th><?php echo t('Date') ?></th>
						<th><?php echo t('File') ?></th>
						<th colspan="3"></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($backups as $arr_bkupInf) { ?>
						<tr> 
							<td width="50%" style="white-space: nowrap"><?php echo $dh->formatDateTime($arr_bkupInf['date'], true, false) ?></td>
							<td width="50%"><?php echo $arr_bkupInf['file']; ?></td>
							<td style="white-space: nowrap">
								<?php echo $interface->button_js(t('Download'), 'window.location.href=\'' . $this->action('download', $arr_bkupInf['file']) . '\'', 'left', 'small'); ?>
								
								<?php print $interface->button_js(t("Restore"), "confirmRestore('" . $arr_bkupInf['file'] . "')", 'left','small'); ?>
								
								<?php print $interface->button_js(t("Delete"), "confirmDelete('" . $arr_bkupInf['file'] . "')",'left','small'); ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>


		<?php } else { ?>
			<p><?php echo t('You have no backups available.') ?></p>
		<?php } ?>

			<?php
				$crypt = Loader::helper('encryption');
			?>
			<h3><?php echo t('Create new Backup')?></h3>
				<form method="post" action="<?php echo $this->action('run_backup') ?>">
					<div class="ccm-buttons well">
						<?php echo $interface->submit(t("Run Backup"), false, "left") ?>
						<br/><br/>
						<div>
						<?php if ($crypt->isAvailable()) { ?>
						<label class="checkbox"><input type="checkbox" name="useEncryption" id="useEncryption" value="1" />
									<span><?php echo t('Use Encryption') ?></span></label>
								<?php } else { ?>
						<label class="checkbox"><input type="checkbox" value="0" disabled />
									<span><?php echo t('Use Encryption') ?></span></label>
								<?php } ?>
						</div>
					</div>
				</form>
		
				<h2><?php echo t('Important Information about Backup & Restore') ?></h2>
		
				<p><?php echo t('Running a backup will create a database export file and store it on your server. Encryption is only advised if you plan on storing the backup on the server indefinitely. This is <strong>not recommended</strong>. After running backup, download the file and make sure that the entire database was saved correctly. If any error messages appear during the backup process, do <b>not</b> attempt to restore from that backup.') ?></p>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(); ?>
	
<?php } else { ?>
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Backup'), false, 'span8 offset2') ?>
	<p><?php echo t('You do not have permission to create or administer backups.') ?></p>
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(); ?>
<?php } ?>
