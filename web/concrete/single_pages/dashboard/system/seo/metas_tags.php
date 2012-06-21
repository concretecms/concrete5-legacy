<?php   defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<?php  echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Metas Tags'), false, false, false, array(), $settings); ?>
<div class="ccm-pane-body">	
		<div class="clearfix">
			<h3><?php   echo t('Export Metas Tags')?></h3>
			<a class="btn info" href="<?php  echo $this->action('export')?>"><?php  echo t('Export CSV file')?></a>
		</div>
		<br/><br/>
		<form method="post" id="upload-metas-tags" enctype="multipart/form-data" action="<?php   echo $this->action('import')?>">
		<?php   echo $validation_token->output('upload_Metas_Tags'); ?>
		<h3><?php   echo t('Import Metas Tags')?></h3>
		<div class="clearfix">
			<?php echo $form->label('theFile',t('Select File'), array('style'=>'width:auto;')); ?>
			<div class="input" style="margin-left:100px;"><input type="file" name="theFile" style="width: 100%" /></div>
		</div>
		<div class="clearfix">
            <div class="input" style="margin-left:0;">
	            <div class="help-block">
				<?php  
				echo t("The CSV file should contain the following columns (in the same order & in a separated comma \",\" file format):");
				echo "<br />";
				echo "<strong>".implode(", ",$csvKeys)."</strong>";
				?>
				</div>
            </div>	
			<br/>
		<?php   echo $concrete_interface->submit(t('Import CSV file'),'upload-metas-tags','left');?>
		</div>
		</form>
</div>
			<?php  echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>