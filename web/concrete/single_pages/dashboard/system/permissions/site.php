<?php defined('C5_EXECUTE') or die("Access Denied.");
/* @var $h ConcreteDashboardHelper */
$h = Loader::helper('concrete/dashboard');
/* @var $ih ConcreteInterfaceHelper */
$ih = Loader::helper('concrete/interface');
/* @var $form FormHelper */
$form = Loader::helper('form');
?>
<?php echo $h->getDashboardPaneHeaderWrapper(t('Site Access'), false, false, false);?>
<form id="site-permissions-form" action="<?php echo $this->action('')?>" method="post">
	<?php echo $this->controller->token->output('site_permissions_code')?>

<?php if(PERMISSIONS_MODEL != 'simple'):?>
<div class="ccm-pane-body ccm-pane-body-footer">
<p>
<?php echo t('Your concrete5 site does not use the simple permissions model. You must change your permissions for each specific page and content area.')?>
</p>
</div>
<?php else:?>
<div class="ccm-pane-body">
	<div class="clearfix">
		<?php echo $form->label('view', t('Viewing Permissions'))?>
		<div class="input">
			<ul class="inputs-list">
				<li>
					<label>
						<?php echo $form->radio('view', 'ANYONE', $guestCanRead)?>
						<span><?php echo t('Public')?> - <?php echo t('Anyone may view the website.')?></span>
					</label>
				</li>
				<li>
					<label>
						<?php echo $form->radio('view', 'USERS', $registeredCanRead)?>
						<span><?php echo t('Members')?> - <?php echo t('Only registered users may view the website.')?></span>
					</label>
				</li>
				<li>
					<label>
						<?php echo $form->radio('view', 'PRIVATE', !$guestCanRead && !$registeredCanRead)?>
						<span><?php echo t('Private')?> - <?php echo t('Only the administrative group may view the website.')?></span>
					</label>
				</li>
			</ul>
		</div>
	</div>
	<div class="clearfix">
		<?php echo $form->label('gID', t('Edit Access'))?>
		<div class="input">
			<ul class="inputs-list">
				<?php foreach($gArray as $g):?>
				<li>
					<label>
						<?php echo $form->checkbox('gID[]', $g->getGroupID(), in_array($g->getGroupID(), $editAccess))?>
						<span><?php echo $g->getGroupDisplayName()?></span>
					</label>
				</li>
				<?php endforeach?>
			</ul>
			<span class="help-block"><?php echo t('Choose which users and groups may edit your site. Note: These settings can be overridden on specific pages.')?></span>
		</div>
	</div>
</div>
<div class="ccm-pane-footer">
<?php
	$submit = $ih->submit( t('Save'), 'site-permissions-form', 'right', 'primary');
	print $submit;
?>
</div>

<?php endif?>
</form>
<?php echo $h->getDashboardPaneFooterWrapper(false);?>
