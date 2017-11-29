<?php  
defined('C5_EXECUTE') or die("Access Denied.");
$al = Loader::helper('concrete/asset_library');
$bf = null;
$bfo = null;

if ($controller->getFileID() > 0) { 
	$bf = $controller->getFileObject();
}
if ($controller->getFileOnstateID() > 0) { 
	$bfo = $controller->getFileOnstateObject();

}
?>
<div class="ccm-block-field-group">
<h4><?php echo t('Image to Display')?></h4><br/>
<?php
$args = array();
if ($forceImageToMatchDimensions && $maxWidth && $maxHeight) {
	$args['maxWidth'] = $maxWidth;
	$args['maxHeight'] = $maxHeight;
	$args['minWidth'] = $maxWidth;
	$args['minHeight'] = $maxHeight;
}
?>

<div class="clearfix">
	<label><?php echo t('Image')?></label>
	<div class="input">	
		<?php echo $al->image('ccm-b-image', 'fID', t('Choose Image'), $bf, $args);?>
	</div>
</div>
<div class="clearfix">
	<label><?php echo t('Image On-State')?></label>
	<div class="input">	
		<?php echo $al->image('ccm-b-image-onstate', 'fOnstateID', t('Choose Image On-State'), $bfo, $args);?>
	</div>
</div>

</div>

<div class="ccm-block-field-group">
<h4><?php echo t('Link and Caption')?></h4><br/>

<div class="clearfix">
	<?php echo $form->label('linkType', t('Image Links to:'))?>
	<div class="input">	
		<select name="linkType" id="linkType">
			<option value="0" <?php echo (empty($externalLink) && empty($internalLinkCID) ? 'selected="selected"' : '')?>><?php echo t('Nothing')?></option>
			<option value="1" <?php echo (empty($externalLink) && !empty($internalLinkCID) ? 'selected="selected"' : '')?>><?php echo t('Another Page')?></option>
			<option value="2" <?php echo (!empty($externalLink) ? 'selected="selected"' : '')?>><?php echo t('External URL')?></option>
		</select>
	</div>
</div>

<div id="linkTypePage" style="display: none;" class="clearfix">
	<?php echo $form->label('internalLinkCID', t('Choose Page:'))?>
	<div class="input">
		<?php echo Loader::helper('form/page_selector')->selectPage('internalLinkCID', $internalLinkCID); ?>
	</div>
</div>
<div id="linkTypeExternal" style="display: none;" class="clearfix">
	<?php echo $form->label('externalLink', t('URL:'))?>
	<div class="input">
	<?php echo $form->text('externalLink', $externalLink, array('style' => 'width: 250px')); ?>
	</div>
</div>


<div class="clearfix">
	<?php echo $form->label('altText', t('Alt Text/Caption'))?>
	<div class="input">	
		<?php echo $form->text('altText', $altText, array('style' => 'width: 250px')); ?>
	</div>
</div>

</div>

<div>
<h4><?php echo t('Constrain Image Dimensions')?></h4><br/>
<?php if ($maxWidth == 0) { 
	$maxWidth = '';
} 
if ($maxHeight == 0) {
	$maxHeight = '';
}
?>

<div class="clearfix">
	<?php echo $form->label('maxWidth', t('Max Width'))?>
	<div class="input">	
		<?php echo $form->text('maxWidth', $maxWidth, array('style' => 'width: 60px')); ?>
	</div>
</div>

<div class="clearfix">
	<?php echo $form->label('maxHeight', t('Max Height'))?>
	<div class="input">	
		<?php echo $form->text('maxHeight', $maxHeight, array('style' => 'width: 60px')); ?>
	</div>
</div>


<div class="clearfix">
	<?php echo $form->label('forceImageToMatchDimensions', t('Scale Image'))?>
	<div class="input">	
		<select name="forceImageToMatchDimensions" id="forceImageToMatchDimensions">
			<option value="0" <?php if (!$forceImageToMatchDimensions) { ?> selected="selected" <?php } ?>><?php echo t('Automatically')?></option>
			<option value="1" <?php if ($forceImageToMatchDimensions == 1) { ?> selected="selected" <?php } ?>><?php echo t('Force Exact Image Match')?></option>
		</select>
	</div>
</div>


</div>