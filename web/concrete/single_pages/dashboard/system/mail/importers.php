<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php $ih = Loader::helper('concrete/interface'); ?>
<?php
$enabledVals = array('0' => t('No'), '1' => t('Yes'));
$secureVals = array('' => t('None'), 'SSL' => 'SSL', 'TLS' => 'TLS');
$form = Loader::helper('form');
?>


<?php if ($this->controller->getTask() == 'edit_importer') { ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Importer'), false, 'span8 offset2', false)?>
<form method="post" id="mail-importer-form" class="form-horizontal" action="<?php echo $this->url('/dashboard/system/mail/importers', 'save_importer')?>">
<div class="ccm-pane-body">

		<?php Loader::helper('validation/token')->output('save_importer') ?>
		<?php echo $form->hidden('miID', $mi->getMailImporterID())?>
		<fieldset>
			<legend><?php echo t($mi->getMailImporterName())?> <?php echo t('Settings');?></legend>
		
			<div class="control-group">
				<?php echo $form->label('miEmail',t('Email Address to Route Emails To'));?>
				<div class="controls">
					<?php echo $form->text('miEmail', $mi->getMailImporterEmail())?>
				</div>
			</div>
			
			<div class="control-group">
				<?php echo $form->label('miIsEnabled',t('Enabled'));?>
				<div class="controls">
					<?php echo $form->select('miIsEnabled', $enabledVals, $mi->isMailImporterEnabled())?>
				</div>
			</div>	
		</fieldset>
		<fieldset>
			<legend><?php echo t('POP Mail Server Authentication Settings')?></legend>
			<div class="control-group">
				<?php echo $form->label('miServer',t('Mail Server'));?>
				<div class="controls">
					<?php echo $form->text('miServer', $mi->getMailImporterServer())?>
				</div>
			</div>
			<div class="control-group">
				<?php echo $form->label('miUsername',t('Username'));?>
				<div class="controls">
					<?php echo $form->text('miUsername', $mi->getMailImporterUsername())?>
				</div>
			</div>
			<div class="control-group">
				<?php echo $form->label('miPassword',t('Password'));?>
				<div class="controls">
					<?php echo $form->text('miPassword', $mi->getMailImporterPassword())?>
				</div>
			</div>
			
			<div class="control-group">
				<?php echo $form->label('miEncryption',t('Encryption'));?>
				<div class="controls">
					<?php echo $form->select('miEncryption', $secureVals, $mi->getMailImporterEncryption())?>
				</div>
			</div>
			<?php $port = $mi->getMailImporterPort() == 0 ? '' : $mi->getMailImporterPort(); ?>
		
			<div class="control-group">
				<?php echo $form->label('miPort',t('Port (Leave blank for default)'));?>
				<div class="controls">
					<?php echo $form->text('miPort', $port)?>
				</div>
			</div>

			<div class="control-group">
				<?php echo $form->label('miConnectionMethod', t('Connection Method'));?>
				<div class="controls">
					<?php echo $form->select('miConnectionMethod', array('POP' => 'POP', 'IMAP' => 'IMAP'), $mi->getMailImporterConnectionMethod())?>
				</div>
			</div>

	</fieldset>	
</div>
<div class="ccm-pane-footer">
<?php echo $ih->submit(t('Save'), 'mail-importer-form','right', 'primary')?>
</div>
</form>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>	

<?php } else { ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Mail Importers'), false, 'span8 offset2')?>	
	<div class="ccm-pane-body">
	<?php if (count($importers) == 0) { ?>
		<p><?php echo t('There are no mail importers. Mail importers poll email accounts for new messages and run actions on those messages.')?></p>
	<?php } else { ?>
	
	<table class="table table-striped" border="0" cellspacing="1" cellpadding="0">
	<tr>
		<td class="header"><?php echo t('Name')?></td>
		<td class="header"><?php echo t('Server')?></td>
		<td class="header"><?php echo t('Email Address')?></td>
		<td class="header"><?php echo t('Enabled')?></td>
		<td class="header">&nbsp;</td>
	</tr>
	<?php foreach($importers as $mi) { ?>
		<tr>
			<td><?php echo $mi->getMailImporterName()?></td>
			<td><?php echo $mi->getMailImporterServer()?></td>
			<td><?php echo $mi->getMailImporterEmail()?></td>
			<td><?php echo $mi->isMailImporterEnabled() ? t('Yes') : t('No')?></td>
			<td width="60"><?php
				print $ih->button(t('Edit'), $this->url('/dashboard/system/mail/importers', 'edit_importer', $mi->getMailImporterID()), 'left');		
			?>
		</tr>
	<?php } ?>
	</table>
	<?php } ?>
</div>
<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
<?php } ?>
