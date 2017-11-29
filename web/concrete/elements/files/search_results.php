<?php defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?php
if (isset($_REQUEST['searchInstance'])) {
	$searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);
}
?>
<script type="text/javascript">
	var CCM_STAR_STATES = {
		'unstarred':'star_grey.png',
		'starred':'star_yellow.png'
	};
	var CCM_STAR_ACTION    = 'files/star.php';
</script>

<div id="ccm-<?php echo $searchInstance?>-search-results" class="ccm-file-list">

<?php if ($searchType == 'DASHBOARD') { ?>

<div class="ccm-pane-body">

<?php } ?>

<div id="ccm-list-wrapper"><a name="ccm-<?php echo $searchInstance?>-list-wrapper-anchor"></a>
		<?php $form = Loader::helper('form'); ?>
	<div style="float: left; width: 200px; margin-bottom: 10px">
		<select id="ccm-<?php echo $searchInstance?>-list-multiple-operations" class="" disabled>
			<option value="">** <?php echo t('With Selected')?></option>
			<option value="download"><?php echo t('Download')?></option>
			<option value="sets"><?php echo t('Sets')?></option>
			<option value="properties"><?php echo t('Properties')?></option>
			<option value="rescan"><?php echo t('Rescan')?></option>
			<option value="duplicate"><?php echo t('Copy')?></option>
			<option value="delete"><?php echo t('Delete')?></option>
		</select>	
		
	</div>
	<div style="float: right">
		<?php Loader::element('files/upload_single', array('searchInstance' => $searchInstance, 'ocID' => $ocID)); ?>
	</div>
	<div class="clearfix" style="height: 1px"></div>

<?php
	$txt = Loader::helper('text');
	$keywords = $searchRequest['fKeywords'];
	$soargs = array();
	$soargs['searchType'] = $searchType;
	$soargs['searchInstance'] = $searchInstance;
	$bu = REL_DIR_FILES_TOOLS_REQUIRED . '/files/search_results';
	
	if (count($files) > 0) { ?>	
		<table border="0" cellspacing="0" cellpadding="0" id="ccm-<?php echo $searchInstance?>-list" class="ccm-results-list">
		<tr>
			<th><input id="ccm-<?php echo $searchInstance?>-list-cb-all" type="checkbox" /></th>
			<th class="ccm-file-list-thumbnail-wrapper"><?php echo t('Thumbnail')?></th>

			<th class="ccm-file-list-starred">&nbsp;</th>
			<?php foreach($columns->getColumns() as $col) { ?>
				<?php if ($col->isColumnSortable()) { ?>
					<th class="<?php echo $fileList->getSearchResultsClass($col->getColumnKey())?>"><a href="<?php echo $fileList->getSortByURL($col->getColumnKey(), $col->getColumnDefaultSortDirection(), $bu, $soargs)?>"><?php echo $col->getColumnName()?></a></th>
				<?php } else { ?>
					<th><?php echo $col->getColumnName()?></th>
				<?php } ?>
			<?php } ?>
		</tr>
	<?php
		foreach($files as $f) {
			$pf = new Permissions($f);
			if (!isset($striped) || $striped == 'ccm-list-record-alt') {
				$striped = '';
			} else if ($striped == '') { 
				$striped = 'ccm-list-record-alt';
			}
			$star_icon = ($f->isStarred() == 1) ? 'star_yellow.png' : 'star_grey.png';
			$fv = $f->getApprovedVersion(); 
			$canViewInline = $fv->canView() ? 1 : 0;
			$canEdit = ($fv->canEdit() && $pf->canEditFileContents()) ? 1 : 0;
			$pfg = FilePermissions::getGlobal();
			?>
			<tr class="ccm-list-record <?php echo $striped?>" ccm-file-manager-instance="<?php echo $searchInstance?>" ccm-file-manager-can-admin="<?php echo ($pf->canEditFilePermissions())?>" ccm-file-manager-can-duplicate="<?php echo $pf->canCopyFile()?>" ccm-file-manager-can-delete="<?php echo $pf->canDeleteFile()?>" ccm-file-manager-can-view="<?php echo $canViewInline?>" ccm-file-manager-can-replace="<?php echo $pf->canEditFileContents()?>" ccm-file-manager-can-edit="<?php echo $canEdit?>" fID="<?php echo $f->getFileID()?>" id="fID<?php echo $f->getFileID()?>">
			<td class="ccm-file-list-cb" style="vertical-align: middle !important"><input type="checkbox" value="<?php echo $f->getFileID()?>" /></td>
			<td class="ccm-file-list-thumbnail-wrapper"><ul class="thumbnails"><li class="ccm-file-list-thumbnail" fID="<?php echo $f->getFileID()?>"><a href="javascript:void(0)" class="thumbnail"><?php echo $fv->getThumbnail(1)?></a></li></ul>
			
			<?php if ($fv->hasThumbnail(2)) { ?>
				<div class="ccm-file-list-thumbnail-hover" id="fID<?php echo $f->getFileID()?>hoverThumbnail"><div><?php echo $fv->getThumbnail(2)?></div></div>
			<?php } ?>

				</td>
			<td class="ccm-file-list-starred"><img src="<?php echo ASSETS_URL_IMAGES?>/icons/<?php echo $star_icon?>" height="16" width="16" border="0" class="ccm-star" /></td>
			<?php foreach($columns->getColumns() as $col) { ?>
				<?php // special one for keywords ?>				
				<?php if ($col->getColumnKey() == 'fvTitle') { ?>
					<td class="ccm-file-list-filename"><?php echo $txt->highlightSearch($fv->getTitle(), $keywords)?></td>		
				<?php } else { ?>
					<td><?php echo $col->getColumnValue($f)?></td>
				<?php } ?>
			<?php } ?>
						
			</tr>
			<?php
		}

	?>
	
	</table>
	
	

	<?php } else { ?>
		
		<div class="ccm-results-list-none"><?php echo t('No files found.')?></div>
		
	
	<?php } ?>
	
<?php
	$fileList->displaySummary();
?>
</div>

<?php if ($searchType == 'DASHBOARD') { ?>
</div>
<div class="ccm-pane-footer">
	<?php 	$fileList->displayPagingV2($bu, false, $soargs); ?>
</div>

<?php } else { ?>
	<div class="ccm-pane-dialog-pagination">
		<?php 	$fileList->displayPagingV2($bu, false, $soargs); ?>
	</div>
<?php } ?>

</div>
