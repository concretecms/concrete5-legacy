<? defined('C5_EXECUTE') or die("Access Denied.");
$fakeInstance = mt_rand(); ?>
<input type="hidden" name="bricksToolsURL_<?=$fakeInstance?>" value="<?=REL_DIR_FILES_TOOLS_BLOCKS?>/bricks/" />
<?php if($akCategoryHandle) { ?>
<input type="hidden" name="originalAKC_<?=$fakeInstance?>" value="<?=$akCategoryHandle?>" />
<?php } ?>
<input type="hidden" name="defaults_<?=$fakeInstance?>" value="<?=urlencode($defaults)?>" />
<input type="hidden" name="fakeInstance" value="<?=$fakeInstance?>" />

<!-- Settings Pane -->
<div class="ccm-bricksPane" id="ccm-bricksPane-settings">
	<table width="100%" style="border-bottom:1px solid #DEDEDE;">
		<tr>
			<td width="50%" valign="top" style="padding-right:10px;padding-bottom:10px;border-right:1px solid #DEDEDE;">
				<h2>
					<?=t('Select an Attribute Key Category')?>
				</h2>
				<?php 
					print Loader::helper('form/attribute_key_category_selector')->select(
						$akCategoryHandle, 
						'akCategoryHandle'
					); 
				?>
			</td>
			<td width="50%" valign="top" style="padding-left:10px;padding-bottom:10px;">
				<h2>
					<?=t('Search Form')?>
				</h2>
				<?php 
					print Loader::helper('form')->select(
						'isSearchable', 
						array(
							0=>t('Hidden'), 
							1=>t('Visible')
						),
						$isSearchable
					);
				?>
			</td>
		</tr>
	</table>
	<div class="ccm-bricksPane" id="ccm-bricksPane-columns">
	
	</div>
</div>

<!-- Columns Pane -->

