<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<? if (isset($key)) { ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Edit Attribute'), false, false, false)?>
<form method="post" class="form-horizontal" action="<?=$this->action('edit')?>" id="ccm-attribute-key-form">



<? Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type, 'key' => $key)); ?>

</form>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>




<? } else if ($this->controller->getTask() == 'select_type' || $this->controller->getTask() == 'add' || $this->controller->getTask() == 'edit') { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('User Attributes'), false, false, false)?>

	<? if (isset($type)) { ?>
		<form method="post" class="form-horizontal" action="<?=$this->action('add')?>" id="ccm-attribute-key-form">
	
		<? Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type)); ?>
	
		</form>	
	<? } ?>
	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>



<? } else { ?>

	<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('User Attributes'), false, false, false)?>

	<?
	$attribs = UserAttributeKey::getList();
	Loader::element('dashboard/attributes_table', array('category' => $category, 'attribs'=> $attribs, 'editURL' => '/dashboard/users/attributes')); ?>

	<form method="get" class="form-inline" action="<?=$this->action('select_type')?>" id="ccm-attribute-type-form">
		<fieldset>
			<legend><?php echo t("Add Attribute");?></legend>	
			<div class="form-group">
				<?=$form->label('atID', t('Add Attribute'), array("class" => "sr-only"))?>
				<?=$form->select('atID', $types, array("style" => "width: 400px; margin-right: 10px;"))?>
			</div>
			<div class="form-group">
				<?=$form->submit('submit', t('Add'))?>
			</div>
		</fieldset>
	</form>
	
	<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>

<? } ?>

<script type="text/javascript">
$(function() {
	$("div.ccm-attributes-list").sortable({
		handle: 'img.ccm-attribute-icon',
		cursor: 'move',
		opacity: 0.5,
		stop: function() {
			var ualist = $(this).sortable('serialize');
			$.post('<?=REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/user_attributes_update.php', ualist, function(r) {

			});
		}
	});
});

</script>

<style type="text/css">
div.ccm-attributes-list img.ccm-attribute-icon:hover {cursor: move}
</style>