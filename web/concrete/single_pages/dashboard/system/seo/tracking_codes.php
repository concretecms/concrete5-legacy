<?php defined('C5_EXECUTE') or die("Access Denied.");
/* @var $h ConcreteDashboardHelper */
$h = Loader::helper('concrete/dashboard');
/* @var $ih ConcreteInterfaceHelper */
$ih = Loader::helper('concrete/interface');
/* @var $form FormHelper */
$form = Loader::helper('form');
?>
<?php echo $h->getDashboardPaneHeaderWrapper(t('Tracking Codes'), false, 'span8 offset2', false);?>
<form id="tracking-code-form" action="<?php echo $this->action('')?>" method="post">
<div class="ccm-pane-body">
	<?php echo $this->controller->token->output('update_tracking_code')?>
	<div class="clearfix">
		<?php echo $form->label('tracking_code', t('Tracking Codes'))?>
		<div class="input">
			<?php echo $form->textarea('tracking_code', $tracking_code, array('class' => 'xxlarge', 'rows' => 4, 'cols' => 50))?>
			<span class="help-block"><?php echo t('Any HTML you paste here will be inserted at either the bottom or top of every page in your website automatically.')?></span>
		</div>
	</div>
	<div class="clearfix">
		<?php echo $form->label('tracking_code_position', t('Position'))?>
		<div class="input">
			<ul class="inputs-list">
				<li>
					<label>
						<?php echo $form->radio('tracking_code_position', 'top', $tracking_code_position)?>
						<span><?php echo t('Header of the page')?></span>
					</label>
				</li>
				<li>
					<label>
						<?php echo $form->radio('tracking_code_position', 'bottom', $tracking_code_position)?>
						<span><?php echo t('Footer of the page')?></span>
					</label>
				</li>
			</ul>
		</div>
	</div>	
	
</div>
<div class="ccm-pane-footer">
<?php
	$submit = $ih->submit( t('Save'), 'tracking-code-form', 'right', 'primary');
	print $submit;
?>
</div>
</form>
<?php echo $h->getDashboardPaneFooterWrapper(false);?>