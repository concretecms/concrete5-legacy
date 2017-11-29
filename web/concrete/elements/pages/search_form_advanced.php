<?php defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?php

$searchFields = array(
	'' => '** ' . t('Fields'),
	'keywords' => t('Full Page Index'),
	'date_added' => t('Date Added'),
	'theme' => t('Theme'),
	'last_modified' => t('Last Modified'),
	'date_public' => t('Public Date'),
	'owner' => t('Page Owner'),
	'num_children' => t('# Children'),
	'version_status' => t('Approved Version')
);

if (PERMISSIONS_MODEL != 'simple') {
	$searchFields['permissions_inheritance'] = t('Permissions Inheritance');
}

if (!$searchDialog) {
	$searchFields['parent'] = t('Parent Page');
}

Loader::model('attribute/categories/collection');
$searchFieldAttributes = CollectionAttributeKey::getSearchableList();
foreach($searchFieldAttributes as $ak) {
	$searchFields[$ak->getAttributeKeyID()] = $ak->getAttributeKeyDisplayName();
}

?>

<?php $form = Loader::helper('form'); ?>
	
	<div id="ccm-<?php echo $searchInstance?>-search-field-base-elements" style="display: none">
	
		<span class="ccm-search-option"  search-field="keywords">
		<?php echo $form->text('keywords', $searchRequest['keywords'], array('style' => 'width: 120px'))?>
		</span>

		<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_public">
		<?php echo $form->text('date_public_from', array('style' => 'width: 86px'))?>
		<?php echo t('to')?>
		<?php echo $form->text('date_public_to', array('style' => 'width: 86px'))?>
		</span>

		<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_added">
		<?php echo $form->text('date_added_from', array('style' => 'width: 86px'))?>
		<?php echo t('to')?>
		<?php echo $form->text('date_added_to', array('style' => 'width: 86px'))?>
		</span>

		<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="last_modified">
		<?php echo $form->text('last_modified_from', array('style' => 'width: 86px'))?>
		<?php echo t('to')?>
		<?php echo $form->text('last_modified_to', array('style' => 'width: 86px'))?>
		</span>

		<span class="ccm-search-option"  search-field="owner">
		<?php echo $form->text('owner', array('class'=>'span5'))?>
		</span>

		<span class="ccm-search-option"  search-field="permissions_inheritance">
			<select name="cInheritPermissionsFrom">
				<option value="PARENT"<?php if ($req['cInheritPermissionsFrom'] == 'PARENT') { ?> selected <?php } ?>><?php echo t('Parent Page')?></option>
				<option value="TEMPLATE" <?php if ($req['cInheritPermissionsFrom'] == 'TEMPLATE') { ?> selected <?php } ?>><?php echo t('Page Type')?></option>
				<option value="OVERRIDE"<?php if ($req['cInheritPermissionsFrom'] == 'OVERRIDE') { ?> selected <?php } ?>><?php echo t('Itself (Override)')?></option>
			</select>
		</span>

		<span class="ccm-search-option"  search-field="version_status">
		<label class="checkbox"><?php echo $form->radio('cvIsApproved', 0, false)?> <span><?php echo t('Unapproved')?></span></label>
		<label class="checkbox"><?php echo $form->radio('cvIsApproved', 1, false)?> <span><?php echo t('Approved')?></span></label>
		</span>
			
		<?php if (!$searchDialog) { ?>
		<span class="ccm-search-option" search-field="parent">

		<?php $ps = Loader::helper("form/page_selector");
		print $ps->selectPage('cParentIDSearchField');
		?>
		
		<br/><strong><?php echo t('Search All Children?')?></strong><br/>
		<label class="checkbox"><?php echo $form->radio('cParentAll', 0, false)?> <span><?php echo t('No')?></span></label>
		<label class="checkbox"><?php echo $form->radio('cParentAll', 1, false)?> <span><?php echo t('Yes')?></span></label>
		</span>
		<?php } ?>
		<span class="ccm-search-option"  search-field="num_children">
			<select name="cChildrenSelect">
				<option value="gt"<?php if ($req['cChildrenSelect'] == 'gt') { ?> selected <?php } ?>><?php echo t('More Than')?></option>
				<option value="eq" <?php if ($req['cChildrenSelect'] == 'eq') { ?> selected <?php } ?>><?php echo t('Equal To')?></option>
				<option value="lt"<?php if ($req['cChildrenSelect'] == 'lt') { ?> selected <?php } ?>><?php echo t('Fewer Than')?></option>
			</select>
			<input type="text" name="cChildren" value="<?php echo $req['cChildren']?>" />
		</span>
		
		<span class="ccm-search-option"  search-field="theme">
			<select name="ptID">
			<?php $themes = PageTheme::getList(); ?>
			<?php foreach($themes as $pt) { ?>
				<option value="<?php echo $pt->getThemeID()?>"><?php echo $pt->getThemeDisplayName()?></option>			
			<?php } ?>
			</select>
		</span>		
		
		<?php foreach($searchFieldAttributes as $sfa) { 
			$sfa->render('search'); ?>
		<?php } ?>
		
	</div>

	<form method="get" id="ccm-<?php echo $searchInstance?>-advanced-search" action="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/pages/search_results" class="form-horizontal">

	<input type="hidden" name="searchInstance" value="<?php echo $searchInstance?>" />

	<div class="ccm-pane-options-permanent-search">
	
		<input type="hidden" name="submit_search" value="1" />
	<?php	
		print $form->hidden('ccm_order_dir', $searchRequest['ccm_order_dir']); 
		print $form->hidden('ccm_order_by', $searchRequest['ccm_order_by']); 
		if ($searchDialog) {
			print $form->hidden('searchDialog', true);
		}
		if ($sitemap_select_mode) {
			print $form->hidden('sitemap_select_mode', $sitemap_select_mode);
		}
		if ($sitemap_select_callback) {
			print $form->hidden('sitemap_select_callback', $sitemap_select_callback);
		}
		if ($sitemap_display_mode) {
			print $form->hidden('sitemap_display_mode', $sitemap_display_mode);
		}
	?>

		<div class="span3">
		<?php echo $form->label('cvName', t('Page Name'))?>
		<div class="controls">
			<?php echo $form->text('cvName', $searchRequest['cvName'], array('style'=> 'width: 120px')); ?>
		</div>
		</div>

		<div class="span3">
		<?php echo $form->label('ctID', t('Page Type'))?>
		<div class="controls">
			<?php 
			Loader::model('collection_types');
			$ctl = CollectionType::getList();
			$ctypes = array('' => t('** All'));
			foreach($ctl as $ct) {
				$ctypes[$ct->getCollectionTypeID()] = $ct->getCollectionTypeName();
			}
			
			print $form->select('ctID', $ctypes, $searchRequest['ctID'], array('style' => 'width:120px'))?>

		</div>
		</div>

		<div class="span3">
		<?php echo $form->label('numResults', t('# Per Page'))?>
		<div class="controls">
			<?php echo $form->select('numResults', array(
				'10' => '10',
				'25' => '25',
				'50' => '50',
				'100' => '100',
				'500' => '500'
			), $searchRequest['numResults'], array('style' => 'width:65px'))?>
		</div>
		<?php echo $form->submit('ccm-search-pages', t('Search'), array('style' => 'margin-left: 10px'))?>
		</div>

	</div>
	<a href="javascript:void(0)" onclick="ccm_paneToggleOptions(this)" class="ccm-icon-option-<?php if (is_array($searchRequest['selectedSearchField']) && count($searchRequest['selectedSearchField']) > 1) { ?>open<?php } else { ?>closed<?php } ?>"><?php echo t('Advanced Search')?></a>
	<div class="clearfix ccm-pane-options-content" <?php if (is_array($searchRequest['selectedSearchField']) && count($searchRequest['selectedSearchField']) > 1) { ?>style="display: block" <?php } ?>>
		<br/>
		<table class="table-striped table ccm-search-advanced-fields" id="ccm-<?php echo $searchInstance?>-search-advanced-fields">
		<tr>
			<th colspan="2" width="100%"><?php echo t('Additional Filters')?></th>
			<th style="text-align: right; white-space: nowrap"><a href="javascript:void(0)" id="ccm-<?php echo $searchInstance?>-search-add-option" class="ccm-advanced-search-add-field"><span class="ccm-menu-icon ccm-icon-view"></span><?php echo t('Add')?></a></th>
		</tr>
		<tr id="ccm-search-field-base">
			<td><?php echo $form->select('searchField', $searchFields);?></td>
			<td width="100%">
			<input type="hidden" value="" class="ccm-<?php echo $searchInstance?>-selected-field" name="selectedSearchField[]" />
			<div class="ccm-selected-field-content">
				<?php echo t('Select Search Field.')?>				
			</div></td>
			<td><a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a></td>
		</tr>
		<?php 
		$i = 1;
		if (is_array($searchRequest['selectedSearchField'])) { 
			foreach($searchRequest['selectedSearchField'] as $req) { 
				if ($req == '') {
					continue;
				}
				?>
				
				<tr class="ccm-search-field ccm-search-request-field-set" ccm-search-type="<?php echo $req?>" id="ccm-<?php echo $searchInstance?>-search-field-set<?php echo $i?>">
				<td><?php echo $form->select('searchField' . $i, $searchFields, $req); ?></td>
				<td width="100%"><input type="hidden" value="<?php echo $req?>" class="ccm-<?php echo $searchInstance?>-selected-field" name="selectedSearchField[]" />
					<div class="ccm-selected-field-content">
						<?php if ($req == 'date_public') { ?>
							<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_public">
							<?php echo $form->text('date_public_from', $searchRequest['date_public_from'], array('style' => 'width: 86px'))?>
							<?php echo t('to')?>
							<?php echo $form->text('date_public_to', $searchRequest['date_public_to'], array('style' => 'width: 86px'))?>
							</span>
						<?php } ?>

						<?php if ($req == 'keywords') { ?>
							<span class="ccm-search-option"  search-field="keywords">
							<?php echo $form->text('keywords', $searchRequest['keywords'], array('style' => 'width: 120px'))?>
							</span>
						<?php } ?>

						<?php if ($req == 'date_added') { ?>
							<span class="ccm-search-option ccm-search-option-type-date_time"  search-field="date_added">
							<?php echo $form->text('date_added_from', $searchRequest['date_added_from'], array('style' => 'width: 86px'))?>
							<?php echo t('to')?>
							<?php echo $form->text('date_added_to', $searchRequest['date_added_to'], array('style' => 'width: 86px'))?>
							</span>
						<?php } ?>

						<?php if ($req == 'owner') { ?>
							<span class="ccm-search-option"  search-field="owner">
							<?php echo $form->text('owner', $searchRequest['owner'], array('class' => 'span5'))?>
							</span>
						<?php } ?>

						<?php if ($req == 'permissions_inheritance') { ?>
							<span class="ccm-search-option"  search-field="permissions_inheritance">
							<select name="cInheritPermissionsFrom">
								<option value="PARENT"<?php if ($searchRequest['cInheritPermissionsFrom'] == 'PARENT') { ?> selected <?php } ?>><?php echo t('Parent Page')?></option>
								<option value="TEMPLATE" <?php if ($searchRequest['cInheritPermissionsFrom'] == 'TEMPLATE') { ?> selected <?php } ?>><?php echo t('Page Type')?></option>
								<option value="OVERRIDE"<?php if ($searchRequest['cInheritPermissionsFrom'] == 'OVERRIDE') { ?> selected <?php } ?>><?php echo t('Itself (Override)')?></option>
							</select>
							</span>
						<?php } ?>

						<?php if ($req == 'num_children') { ?>
							<span class="ccm-search-option"  search-field="num_children">
							<select name="cChildrenSelect">
								<option value="gt"<?php if ($searchRequest['cChildrenSelect'] == 'gt') { ?> selected <?php } ?>><?php echo t('More Than')?></option>
								<option value="eq" <?php if ($searchRequest['cChildrenSelect'] == 'eq') { ?> selected <?php } ?>><?php echo t('Equal To')?></option>
								<option value="lt"<?php if ($searchRequest['cChildrenSelect'] == 'lt') { ?> selected <?php } ?>><?php echo t('Fewer Than')?></option>
							</select>
							<input type=text name="cChildren" value="<?php echo $searchRequest['cChildren']?>">
							</span>
						<?php } ?>

						<?php if ($req == 'version_status') { ?>
							<span class="ccm-search-option"  search-field="version_status">
							<ul class="inputs-list">
							<li><label><?php echo $form->radio('_cvIsApproved', 0, $searchRequest['cvIsApproved'])?> <span><?php echo t('Unapproved')?></span></label></li>
							<li><label><?php echo $form->radio('_cvIsApproved', 1, $searchRequest['cvIsApproved'])?> <span><?php echo t('Approved')?></span></label></li>
							</ul>
							</span>
						<?php } ?>
						
						<?php if ((!$searchDialog) && $req == 'parent') { ?>
						<span class="ccm-search-option" search-field="parent">

						<?php $ps = Loader::helper("form/page_selector");
						print $ps->selectPage('cParentIDSearchField', $searchRequest['cParentIDSearchField']);
						?>
						
						<br/><strong><?php echo t('Search All Children?')?></strong><br/>

						<ul class="inputs-list">
						<li><label><?php echo $form->radio('_cParentAll', 0, $searchRequest['cParentAll'])?> <span><?php echo t('No')?></span></label></li>
						<li><label><?php echo $form->radio('_cParentAll', 1, $searchRequest['cParentAll'])?> <span><?php echo t('Yes')?></span></label></li>
						</ul>
						</span>
						<?php } ?>
						
						<?php foreach($searchFieldAttributes as $sfa) { 
							if ($sfa->getAttributeKeyID() == $req) {
								$at = $sfa->getAttributeType();
								$at->controller->setRequestArray($searchRequest);
								$at->render('search', $sfa);
							}
						} ?>					</div>
					</td>
					<td><a href="javascript:void(0)" class="ccm-search-remove-option"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/remove_minus.png" width="16" height="16" /></a></td>
					</tr>
				<?php 
					$i++;
				} 
				
				} ?>
		</table>
		<div id="ccm-search-fields-submit">
			<a href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/pages/customize_search_columns?searchInstance=<?php echo $searchInstance?>" id="ccm-list-view-customize"><span class="ccm-menu-icon ccm-icon-properties"></span><?php echo t('Customize Results')?></a>
		</div>
	</div>
</form>	
