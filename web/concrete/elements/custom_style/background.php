<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<div id="ccm-styleEditPane-background" class="ccm-styleEditPane" style="display:none">
	<div>
		<h3><?php echo t('Background')?></h3> 
		<table border="0" cellspacing="0" cellpadding="0" class="table ccm-style-property-table">
			<tr>
				<td><?php echo $fh->output( 'background_color', t('Background Color'), $cssData['background_color']) ?></td>
				<?php 
				$bf = false;
				if ($cssData['background_image'] > 0) {
					$bf = File::getByID($cssData['background_image']);
				} ?>
			</tr>
			<tr>
				<td><?php echo $ah->image('background_image', 'background_image', t('Background Image'), $bf)?></td>
			</tr>
			<tr>
				<td>
					<?php echo t('Repeat')?>:<br/>
					<input type="radio" value="no-repeat" name="background_repeat" <?php if ($cssData['background_repeat'] == 'no-repeat' || !$cssData['backgroundImageRepeat']) { ?> checked <?php } ?> /> <?php echo t('None')?>
					<input type="radio" value="repeat-x" name="background_repeat" <?php if ($cssData['background_repeat'] == 'repeat-x') { ?> checked <?php } ?> /> <?php echo t('Horizontal')?>
					<input type="radio" value="repeat-y" name="background_repeat" <?php if ($cssData['background_repeat'] == 'repeat-y') { ?> checked <?php } ?>/> <?php echo t('Vertical')?>
					<input type="radio" value="repeat" name="background_repeat" <?php if ($cssData['background_repeat'] == 'repeat') { ?> checked <?php } ?>/> <?php echo t('All')?>
				</td>
			</tr>
		</table>
		<div class="ccm-spacer"></div>
	</div>	
</div>