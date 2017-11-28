<?php defined('C5_EXECUTE') or die("Access Denied.");?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Multilingual Setup'), false, 'span8 offset2', false)?>
<?php 

if (count($interfacelocales) <= 1) { ?>
<div class="ccm-pane-body ccm-pane-body-footer">
	<?php echo t("You don't have any interface languages installed. You must run concrete5 in English.");?>
</div>
<?php } else { ?>

<form method="post" class="form-horizontal" action="<?php echo $this->action('save_interface_language')?>">
<div class="ccm-pane-body">
	
	<div class="control-group">
	<?php echo $form->label('LANGUAGE_CHOOSE_ON_LOGIN', t('Login'))?>
	<div class="controls">
		<label class="checkbox"><?php echo $form->checkbox('LANGUAGE_CHOOSE_ON_LOGIN', 1, $LANGUAGE_CHOOSE_ON_LOGIN)?> <span><?php echo t('Offer choice of language on login.')?></span></label>
	</div>
	</div>
	
	<?php
	$args = array();
	if (defined("LOCALE")) {
		$args['disabled'] = 'disabled';
	}
	?>
	
	<div class="control-group">
	<?php echo $form->label('SITE_LOCALE', t('Default Language'))?>
	<div class="controls">
	<?php echo $form->select('SITE_LOCALE', $interfacelocales, SITE_LOCALE, $args);?>
	</div>
	</div>
	
	<br/>
	<?php echo Loader::helper('validation/token')->output('save_interface_language')?>
</div>
<div class="ccm-pane-footer">
	<?php echo Loader::helper('concrete/interface')->submit(t('Save'), 'save', 'left', 'primary')?>
</div>
</form>
	
<?php } ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>