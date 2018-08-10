<?php defined('C5_EXECUTE') or die("Access Denied.");  ?>
<div class="ccm-tags-display">
<?php if(strlen($title)) {
	?><h4><?php echo $title ?></h4><?php
}
if($options instanceof SelectAttributeTypeOptionList && $options->count() > 0) {
	?><ul class="ccm-tag-list">
		<?php foreach($options as $opt) {
			$qs = $akc->field('atSelectOptionID') . '[]=' . $opt->getSelectAttributeOptionID();
			?><li <?php echo ($selectedOptionID == $opt->getSelectAttributeOptionID()?'class="ccm-tag-selected"':'')?>><?php if ($target instanceof Page) { ?>
				<a href="<?php echo $navigation->getLinkToCollection($target)?>?<?php echo $qs?>"><?php echo $opt ?></a><?php }  else { echo $opt; }?></li><?php 
		}?>	
	</ul>
<?php } ?>
	<div style="clear: both"></div>
</div>