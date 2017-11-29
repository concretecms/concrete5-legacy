
<?php 
//Used on both page and file attributes
$c = Page::getCurrentPage();

$sets = array();
if (is_object($category) && $category->allowAttributeSets()) {
	$sets = $category->getAttributeSets();
}
?>

<div class="ccm-pane-options">
<form class="form-horizontal">
<div class="ccm-pane-options-permanent-search">

	<?php $form = Loader::helper('form'); ?>

	<?php if (count($sets) > 0) { ?>
	<div class="span6">
	<?php echo $form->label('asGroupAttributes', t('View'))?>
	<div class="controls">
	<select class="span3" onchange="window.location.href='<?php echo Loader::helper('navigation')->getLinkToCollection($c)?>?asGroupAttributes=' + this.value" id="asGroupAttributes" name="asGroupAttributes">
		<option value="1" <?php if ($_REQUEST['asGroupAttributes'] !== '0') { ?> selected <?php } ?>><?php echo t('Grouped by set')?></option>
		<option value="0" <?php if ($_REQUEST['asGroupAttributes'] === '0') { ?> selected <?php } ?>><?php echo t('In one list')?></option>
	</select>
	</div>
	</div>
	
	<?php } ?>
	<a href="<?php echo $this->url('/dashboard/system/attributes/sets', 'category', $category->getAttributeKeyCategoryID())?>" id="ccm-list-view-customize-top"><span class="ccm-menu-icon ccm-icon-properties"></span><?php echo t('Manage Sets')?></a>
</div>
</form>
</div>

<div class="ccm-pane-body">

<?php
if (count($attribs) > 0) { ?>


	<?php
	$ih = Loader::helper('concrete/interface');
	$valt = Loader::helper('validation/token');

	
	if (count($sets) > 0 && ($_REQUEST['asGroupAttributes'] !== '0')) { ?>
	
	
		<?php
	
		foreach($sets as $as) { ?>
	
		
		<h3><?php echo $as->getAttributeSetDisplayName()?></h3>
	
		<?php
		
		$setattribs = $as->getAttributeKeys();
		if (count($setattribs) == 0) { ?>
		
			<div class="ccm-attribute-list-wrapper"><?php echo t('No attributes defined.')?></div>
		
		<?php } else { ?>
			
			<div class="ccm-attribute-sortable-set-list ccm-attribute-list-wrapper" attribute-set-id="<?php echo $as->getAttributeSetID()?>" id="asID_<?php echo $as->getAttributeSetID()?>">			
			
			<?php
			
			foreach($setattribs as $ak) { ?>
			
			<div class="ccm-attribute" id="akID_<?php echo $as->getAttributeSetID()?>_<?php echo $ak->getAttributeKeyID()?>">
				<img class="ccm-attribute-icon" src="<?php echo $ak->getAttributeKeyIconSRC()?>" width="16" height="16" /><a href="<?php echo $this->url($editURL, 'edit', $ak->getAttributeKeyID())?>"><?php echo $ak->getAttributeKeyDisplayName()?></a>
			</div>
	

			<?php } ?>
			
			</div>
			
			<?php } ?>
			
			
		<?php } 
		
		$unsetattribs = $category->getUnassignedAttributeKeys();
		if (count($unsetattribs) > 0) { ?>
		
			<h3><?php echo t('Other')?></h3>
			<div class="ccm-attribute-list-wrapper">
			<?php
			foreach($unsetattribs as $ak) { ?>
	
			<div class="ccm-attribute" id="akID_<?php echo $as->getAttributeSetID()?>_<?php echo $ak->getAttributeKeyID()?>">
				<img class="ccm-attribute-icon" src="<?php echo $ak->getAttributeKeyIconSRC()?>" width="16" height="16" /><a href="<?php echo $this->url($editURL, 'edit', $ak->getAttributeKeyID())?>"><?php echo $ak->getAttributeKeyDisplayName()?></a>
			</div>
	

			<?php } ?>
			</div>
		
		<?php
		
		}
	
	} else { ?>
		
		<div class="ccm-attributes-list">
		
		<?php
		foreach($attribs as $ak) { ?>
		<div class="ccm-attribute" id="akID_<?php echo $ak->getAttributeKeyID()?>">
			<img class="ccm-attribute-icon" src="<?php echo $ak->getAttributeKeyIconSRC()?>" width="16" height="16" /><a href="<?php echo $this->url($editURL, 'edit', $ak->getAttributeKeyID())?>"><?php echo $ak->getAttributeKeyDisplayName()?></a>
		</div>
		
		<?php } ?>
	
		</div>
	
	<?php } ?>
	
<?php } else { ?>
	
	<p>
		<?php
	 echo t('No attributes defined.');
		?>
	</p>
	
<?php } ?>

</div>

<script type="text/javascript">
$(function() {
	$("div.ccm-attribute-sortable-set-list").sortable({
		handle: 'img.ccm-attribute-icon',
		cursor: 'move',
		opacity: 0.5,
		stop: function() {
			var ualist = $(this).sortable('serialize');
			ualist += '&cID=<?php echo $c->getCollectionID()?>';
			ualist += '&asID=' + $(this).attr('attribute-set-id');
			$.post('<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/dashboard/attribute_sets_update', ualist, function(r) {

			});
		}
	});
});
</script>

<style type="text/css">
div.ccm-attribute-sortable-set-list img.ccm-attribute-icon:hover {cursor: move}
</style>
