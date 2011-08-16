<? defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php $fakeInstance = mt_rand(); ?>
<input type="hidden" name="persistantBID_<?=$fakeInstance?>" value="<?php if($persistantBID){ print $persistantBID; } else { $persistantBID = mt_rand(); print $persistantBID; }?>" />
<input type="hidden" name="bricksToolsURL_<?=$fakeInstance?>" value="<?=REL_DIR_FILES_TOOLS_BLOCKS?>/bricks/" />
<?php if($akCategoryHandle) { ?>
<input type="hidden" name="originalAKC_<?=$fakeInstance?>" value="<?=$akCategoryHandle?>" />
<?php } ?>
<?php if($columns) { ?>
<input type="hidden" name="columns_<?=$fakeInstance?>" value="<?=urlencode($columns)?>" />
<?php } ?>
<input type="hidden" name="bID_<?=$fakeInstance?>" value="<?=$bID?>" />

<input type="hidden" name="fakeInstance" value="<?=$fakeInstance?>" />
<?php $filters = unserialize($filters); ?>
<input type="hidden" name="keywords_<?=$fakeInstance?>" value="<?=$keywords?>" />
<input type="hidden" name="numResults_<?=$fakeInstance?>" value="<?=$numResults?>" />
<input type="hidden" name="akID_<?=$fakeInstance?>" value="<?=urlencode(serialize($akID))?>" />

<ul id="ccm-bricks-tabs" class="ccm-dialog-tabs">
	<li class="ccm-nav-active"><a id="ccm-bricks-tab-settings" href="javascript:void(0);">
		<?=t('Settings')?>
		</a></li>
	<li class=""><a id="ccm-bricks-tab-columns"  href="javascript:void(0);">
		<?=t('Columns')?>
		</a></li>
	<li class=""><a id="ccm-bricks-tab-admin"  href="javascript:void(0);">
		<?=t('Advanced')?>
		</a></li>
</ul>

<!-- Settings Pane -->
<div class="ccm-bricksPane" id="ccm-bricksPane-settings">
	<table width="100%" style="border-bottom:1px solid #DEDEDE">
		<tr>
			<td width="38%" style="padding-right:10px; padding-bottom:10px;">
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
			<td width="24%" align="center" style="border-left:1px solid #DEDEDE; border-right:1px solid #DEDEDE; padding-bottom:10px;">
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
			<td width="38%" style="padding-left:10px; padding-bottom:10px;">
				<h2>
					<?=t('Allow User Defined Columns')?>
				</h2>
				<?php 
					if($userDefinedColumnsDisabled === 0) $userDefinedColumnsDisabled = 1;
					print Loader::helper('form')->select(
						'userDefinedColumnsDisabled', 
						array(
							1=>t('Disabled'), 
							0=>t('Enabled')
						),
						$userDefinedColumnsDisabled
					);
				?>
			</td>
		</tr>
	</table>
	<h2><?=t('Default Search Parameters')?></h2>
	<table width="100%">
		<tr>
			<td valign="top" id="default-search-parameters" style="padding-right:10px; border-right:1px solid #DEDEDE">
				
			</td>
			<td width="25%" align="center" valign="top" style="padding-left:10px;">
				<?php echo $form->label('filterByCurrentUser', t('Filter by Current User'))?>
				<?php print Loader::helper('form')->select(
						'filterByCurrentUser', 
						array( 
							0=>t('No'),
							1=>t('Yes')
						),
						$filterByCurrentUser
					);
				?>
			</td>
		</tr>
	</table>
</div>

<!-- Columns Pane -->
<div class="ccm-bricksPane" id="ccm-bricksPane-columns" style="display: none">
	
</div>

<!-- Administrations Pane -->
<div class="ccm-bricksPane" id="ccm-bricksPane-admin" style="display: none">
	<h2>
		<?=t('Allow Administration')?>
	</h2>
	<?php 
		if($administrationDisabled === 0) $administrationDisabled = 1;
		print Loader::helper('form')->select(
			'administrationDisabled', 
			array(
				1=>t('No'), 
				0=>t('Yes')
			),
			$administrationDisabled
		);
	?>
</div>
