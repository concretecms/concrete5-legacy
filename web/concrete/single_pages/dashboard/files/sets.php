<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php $ih = Loader::helper('concrete/interface'); ?>
<?php if ($this->controller->getTask() == 'view_detail') { ?>


	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('File Set'), false, 'span10 offset1', false)?>
	<form method="post" class="form-horizontal" id="file_sets_edit" action="<?php echo $this->url('/dashboard/files/sets', 'file_sets_edit')?>" onsubmit="return ccm_saveFileSetDisplayOrder()">
		<?php echo $form->hidden('fsDisplayOrder', '')?>
		<?php echo $validation_token->output('file_sets_edit');?>

	<div class="ccm-pane-body">
	
	<div class="clearfix">
	<ul class="tabs">
		<li class="active"><a href="javascript:void(0)" onclick="$('.tabs').find('li.active').removeClass('active');$(this).parent().addClass('active');$('.ccm-tab').hide();$('#ccm-tab-details').show()" ><?php echo t('Details')?></a></li>
		<li><a href="javascript:void(0)" onclick="$('.tabs').find('li.active').removeClass('active');$(this).parent().addClass('active');$('.ccm-tab').hide();$('#ccm-tab-files').show()"><?php echo t("Files in Set")?></a></li>
	</ul>
	</div>

	<div id="ccm-tab-details" class="ccm-tab">

		<?php
		$u=new User();

		$delConfirmJS = t('Are you sure you want to permanently remove this file set?');
		?>
		
		<script type="text/javascript">
		deleteFileSet = function() {
			if (confirm('<?php echo $delConfirmJS?>')) { 
				location.href = "<?php echo $this->url('/dashboard/files/sets', 'delete', $fs->getFileSetID(), Loader::helper('validation/token')->generate('delete_file_set'))?>";				
			}
		}
		</script>

		<div class="control-group">
		<?php echo $form->label('file_set_name', t('Name'))?>
		<div class="controls">
			<?php echo $form->text('file_set_name',$fs->fsName, array('class' => 'span5'));?>	
		</div>
		</div>

		<?php 
		$fsp = new Permissions($fs);

		if (PERMISSIONS_MODEL != 'simple') { 
		
		if ($fsp->canEditFileSetPermissions()) {

		?>
		
		<div class="control-group">
		<?php echo $form->label('fsOverrideGlobalPermissions', t('Custom Permissions'))?>
		<div class="controls">
			<label class="checkbox"><?php echo $form->checkbox('fsOverrideGlobalPermissions', 1, $fs->overrideGlobalPermissions())?> <span><?php echo t('Enable custom permissions for this file set.')?></span></label>
		</div>
		</div>
		
		

		<div id="ccm-permission-list-form" <?php if (!$fs->overrideGlobalPermissions()) { ?> style="display: none" <?php } ?>>

		<?php Loader::element('permission/lists/file_set', array("fs" => $fs)); ?>
		
		</div>
		<?php } 
		
		}
		?>
		

		<?php
			echo $form->hidden('fsID',$fs->getFileSetID());
		?>
		
		</div>

	<div style="display: none" class="ccm-tab" id="ccm-tab-files">
		<?php
		Loader::model("file_list");
		$fl = new FileList();
		$fl->filterBySet($fs);
		$fl->sortByFileSetDisplayOrder();
		$files = $fl->get();
		if (count($files) > 0) { ?>
		
		
		<p><?php echo t('Click and drag to reorder the files in this set. New files added to this set will automatically be appended to the end.')?></p>
		<div class="ccm-spacer">&nbsp;</div>
		
		<ul class="ccm-file-set-file-list">
		
		<?php

		foreach($files as $f) { ?>
			
		<li id="fID_<?php echo $f->getFileID()?>">
			<div>
				<?php echo $f->getThumbnail(1)?>				
				<span style="word-wrap: break-word"><?php echo $f->getTitle()?></span>
			</div>
		</li>
			
		<?php } ?>

			
		</ul>
		<?php } else { ?>
			<p><?php echo t('There are no files in this set.')?></p>
		<?php } ?>
	</div>
	</div>
	<div class="ccm-pane-footer">
		<input type="submit" value="<?php echo t('Save')?>" class="btn primary ccm-button-v2-right" />
		<?php if ($fsp->canDeleteFileSet()) { ?>
			<?php print $ih->button_js(t('Delete'), "deleteFileSet()", 'right','error');?>
		<?php } ?>
	</div>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

	</form>
	
	
	<script type="text/javascript">
	
	ccm_saveFileSetDisplayOrder = function() {
		var fslist = $('.ccm-file-set-file-list').sortable('serialize');
		$('input[name=fsDisplayOrder]').val(fslist);
		return true;
	}
	
	$(function() {
		$(".ccm-file-set-file-list").sortable({
			cursor: 'move',
			opacity: 0.5
		});
		
	});
	
	</script>
	
	<style type="text/css">
	.ccm-file-set-file-list:hover {cursor: move}
	</style>

<?php } else { ?>


	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('File Sets'), false, 'span10 offset1', false)?>
	<div class="ccm-pane-options">
		
		<form id="ccm-file-set-search" method="get" action="<?php echo $this->url('/dashboard/files/sets')?>" class="form-horizontal">
		<div class="ccm-pane-options-permanent-search">

		<div class="span4">
		<?php echo $form->label('fsKeywords', t('Keywords'))?>
		<div class="controls">
		<input type="text" id="fsKeywords" name="fsKeywords" value="<?php echo Loader::helper('text')->entities($_REQUEST['fsKeywords'])?>" class="span3" />
		</div>
		</div>

		<div class="span4">
		<?php echo $form->label('fsType', t('Type'))?>
		<div class="controls">
		<select id="fsType" name="fsType" style="width: 130px">
		<option value="<?php echo FileSet::TYPE_PUBLIC?>" <?php if ($fsType != FileSet::TYPE_PRIVATE) { ?> selected <?php } ?>><?php echo t('Public Sets')?></option>
		<option value="<?php echo FileSet::TYPE_PRIVATE?>" <?php if ($fsType == FileSet::TYPE_PRIVATE) { ?> selected <?php } ?>><?php echo t('My Sets')?></option>
		</select>
		<input type="submit" class="btn" value="<?php echo t('Search')?>" />
		</div>
		</div>
				
		<input type="hidden" name="group_submit_search" value="1" />

	</div>
		</form>
	</div>
	<div class="ccm-pane-body <?php if (!$fsl->requiresPaging()) { ?> ccm-pane-body-footer <?php } ?> ">

		<a href="<?php echo View::url('/dashboard/files/add_set')?>" style="float: right; z-index: 5; position:relative;top:-5px" class="btn primary"><?php echo t("Add File Set")?></a>

		<?php echo $fsl->displaySummary()?>
	
		<?php if (count($fileSets) > 0) { ?>
			
			<style type="text/css">
				div.ccm-paging-top {padding-bottom:10px;}
			</style>
		
		<?php foreach ($fileSets as $fs) { ?>
		
			<div class="ccm-group">
				<a class="ccm-group-inner" href="<?php echo $this->url('/dashboard/files/sets/', 'view_detail', $fs->getFileSetID())?>" style="background-image: url(<?php echo ASSETS_URL_IMAGES?>/icons/group.png)"><?php echo $fs->getFileSetName()?></a>
			</div>
		
		
		<?php }
		
		
		} else { ?>
		
			<p><?php echo t('No file sets found.')?></p>
		
		<?php } ?>
	
	</div>
	<?php if ($fsl->requiresPaging()) { ?>
		<div class="ccm-pane-footer">
		<?php $fsl->displayPagingV2(); ?>
		</div>
	<?php } ?>
	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper($fsl->requiresPaging())?>
	
	<script type="text/javascript">
		var editFileSet = function(fsID){	
			//set id
			$('#fsID').attr('value',fsID);		
			$('#file-sets-edit-or-delete-action').attr('value','edit-form');
			//submit form
			$("#file-sets-edit-or-delete").get(0).submit();		
		}
		
		var deleteFileSet = function(fsID){
			//set id
			$('#fsID').attr('value',fsID);		
			$('#file-sets-edit-or-delete-action').attr('value','delete');		
			if(confirm("<?php echo t('Are you sure you want to delete this file set?')?>")){
				$("#file-sets-edit-or-delete").get(0).submit();
			}
		}
		
		
	</script>
<?php } ?>	