<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<? if (isset($key)) { ?>

<form method="post" action="<?=$this->action('edit')?>" id="ccm-attribute-key-form">

<? Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type, 'key' => $key)); ?>

</form>


<? } else if ($this->controller->getTask() == 'select_type' || $this->controller->getTask() == 'add' || $this->controller->getTask() == 'edit') { ?>

	<? if (isset($type)) { ?>
		<form method="post" action="<?=$this->action('add')?>" id="ccm-attribute-key-form">
	
		<? Loader::element("attribute/type_form_required", array('category' => $category, 'type' => $type)); ?>
	
		</form>	
	<? } ?>


<? } else { ?>

	<?
	$attribs = FileAttributeKey::getList();
	Loader::element('dashboard/attributes_table', array('category' => $category, 'attribs'=> $attribs, 'editURL' => '/dashboard/files/attributes')); ?>
<div class="ccm-pane-body ccm-pane-body-footer">
	<form method="get" action="<?=$this->action('select_type')?>" id="ccm-attribute-type-form" class="form-inline">
		<fieldset>
			<legend><?=t('Add Attribute')?></legend>
			<div class="form-group">
				<?=$form->label('atID', t('Add Attribute'), array("class" => "sr-only"))?>
				<?=$form->select('atID', $types, array("style" => "width: 400px; margin-right: 10px;"))?>
			</div>
			<div class="form-group">
				<?=$form->submit('submit', t('Add'))?>
			</div>
		</fieldset>
	</form>
</div>
<? } ?>