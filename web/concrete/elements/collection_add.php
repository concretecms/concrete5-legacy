<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php 

Loader::model('collection_attributes');
Loader::model('collection_types');
$dh = Loader::helper('date');

?>
	
<div class="ccm-ui">

<?php if ($_REQUEST['ctID']) { ?>

	<form method="post" action="<?php echo $c->getCollectionAction()?>" id="ccmAddPage" class="dialog-form">		
	<input type="hidden" name="rel" value="<?php echo $_REQUEST['rel']?>" />
	<input type="hidden" name="ctID" value="<?php echo $_REQUEST['ctID']?>" />
	<input type="hidden" name="mode" value="<?php echo $_REQUEST['mode']?>" />

	<div id="ccm-add-page-information">
		
		<h4><?php echo t('Standard Properties')?></h4>
		<?php $form = Loader::helper('form'); ?>

		<div class="clearfix">
			<?php echo $form->label('cName', t('Name'))?>
			<div class="input"><input type="text" name="cName" value="" class="text span6" onKeyUp="ccm_updateAddPageHandle()" ></div>
		</div>

		
		<div class="clearfix">
			<?php echo $form->label('cHandle', t('URL Slug'))?>
			<div class="input"><input type="text" name="cHandle" class="span3" value="" id="cHandle">
			<img src="<?php echo ASSETS_URL_IMAGES?>/loader_intelligent_search.gif" width="43" height="11" id="ccm-url-slug-loader" style="display: none" />
			</div>
		</div>
		
		<div class="clearfix">		
			<?php echo $form->label('cDatePublic', t('Public Date/Time'))?>
			<div class="input">
			<?php
			$dt = Loader::helper('form/date_time');
			echo $dt->datetime('cDatePublic' );
			?> 
			</div>
		</div>		
		
		<div class="clearfix">
			<?php echo $form->label('cDescription', t('Description'))?>
			<div class="input">
			<textarea name="cDescription" rows="4" class="span6"></textarea>
			</div>
		</div>	
		<?php
		$attribs = $ct->getAvailableAttributeKeys();
		$mc = $ct->getMasterTemplate();
		?>

	<?php if (count($attribs) > 0) { ?>
		<h4><?php echo t('Custom Attributes')?></h4>
		

	<?php	
	ob_start();

	foreach($attribs as $ak) { 
	
		if (is_object($mc)) { 
			$caValue = $mc->getAttributeValueObject($ak);
		}		
		?>
	
	
		<div class="clearfix">
			<label><?php echo $ak->getAttributeKeyDisplayName()?></label>
			<div class="input">
			<?php echo $ak->render('composer', $caValue); ?>
			</div>
		</div>
		
	<?php } 
	$contents = ob_get_contents();
	ob_end_clean(); ?>	
	
	<script type="text/javascript">
	<?php 
	$v = View::getInstance();
	$headerItems = $v->getHeaderItems();
	foreach($headerItems as $item) {
		if ($item instanceof CSSOutputObject) {
			$type = 'CSS';
		} else {
			$type = 'JAVASCRIPT';
		} ?>
		 ccm_addHeaderItem("<?php echo $item->file?>", '<?php echo $type?>');
		<?php 
	} 
	?>
	</script>
	
	<?php print $contents; ?>
		
		<?php } ?>
		
	</div>
	
	

	<div class="dialog-buttons">
		<a href="javascript:void(0)" onclick="jQuery.fn.dialog.closeTop();" class="ccm-button-left btn"><?php echo t('Cancel')?></a>
		<input type="submit" onclick="$('#ccmAddPage').submit()" class="btn primary ccm-button-right" value="<?php echo t('Add Page')?>" />
	</div>	
	
	<input type="hidden" name="add" value="1" />
	<input type="hidden" name="processCollection" value="1">
	
	</form>

<script type="text/javascript">
	
	$(function() {
		$('input[name=cName]').focus();
		$('#ccmAddPage input, #ccmAddPage select').bind('keypress.addpage', function(e) {
			if (e.keyCode == 13) {
				$('#ccmAddPage').submit();
			}
		});
		var height = $("#ccm-add-page-information").height();
		var dlog = $("#ccm-add-page-information").closest('.ui-dialog-content');
		if (height > 256) {
			height = height + 160;
			if ($(window).height() > 750) {
				if (height < 650) { 
					dlog.dialog('option', 'height', height);
				} else {
					dlog.dialog('option', 'height', '650');
				}
				dlog.dialog('option','position','center');
			}
		}

		$('#ccmAddPage').submit(function(e) {
			var proceed = true;
			if ($('#ccm-url-slug-loader').is(':visible')) {
				proceed = false;
			}
			if(proceed) {
				jQuery.fn.dialog.showLoader();
			}
			else {
				e.preventDefault();
			}
			return proceed;
		});

	});
	
	var addPageTimer = {};
	ccm_updateAddPageHandle = function() {
		if(addPageTimer.lastRequested === $.trim($('#ccmAddPage input[name=cName]').val())) {
			return;
		}
		if(addPageTimer.timer) {
			clearTimeout(addPageTimer.timer);
		}
		addPageTimer.timer = setTimeout(function() {
			var val = $.trim($('#ccmAddPage input[name=cName]').val());
			addPageTimer.lastRequested = val;
			delete addPageTimer.timer;
			$('#ccm-url-slug-loader').show();
			addPageTimer.xhr = $.ajax({
				type: 'POST',
				url: '<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/pages/url_slug',
				data: {
					'token': '<?php echo Loader::helper('validation/token')->generate('get_url_slug')?>',
					'name': val,
					'parentID' : '<?php echo $c->getCollectionId()  ?>'
				}
			})
			.done(function(r, textStatus, xhr) {
				if(addPageTimer && addPageTimer.xhr == xhr) {
					$('#ccmAddPage input[name=cHandle]').val(r);
					$('#ccm-url-slug-loader').hide();
				}
			})
			.fail(function(xhr) {
				if(addPageTimer && addPageTimer.xhr == xhr) {
					$('#ccm-url-slug-loader').hide();
				}
			});
		}, 150);
	
	}
</script>



<?php } else {


$ctArray = CollectionType::getList();
$cp = new Permissions($c);

$cnt = 0;
for ($i = 0; $i < count($ctArray); $i++) {
	$ct = $ctArray[$i];
	if ($cp->canAddSubpage($ct)) { 
		$cnt++;
	}
}

?>
		<div id="ccm-choose-pg-type">
			<h4 id="ccm-choose-pg-type-title"><?php echo t('Choose a Page Type')?></h4>
			<ul id="ccm-select-page-type">
				<?php 
				foreach($ctArray as $ct) { 
					if ($cp->canAddSubpage($ct)) { 
					$requiredKeys=array();
					$aks = $ct->getAvailableAttributeKeys();
					foreach($aks as $ak)
						$requiredKeys[] = intval($ak->getAttributeKeyID());
						
					$usedKeysCombined=array();
					$usedKeys=array();
					$setAttribs = $c->getSetCollectionAttributes();
					foreach($setAttribs as $ak) 
						$usedKeys[] = $ak->getAttributeKeyID(); 
					$usedKeysCombined = array_merge($requiredKeys, $usedKeys);
					?>
					
					<?php $class = ($ct->getCollectionTypeID() == $ctID) ? 'ccm-item-selected' : ''; ?>
			
					<li class="<?php echo $class?>"><a class="dialog-launch" dialog-width="600" dialog-title="<?php echo t('Add %s', Loader::helper('text')->entities($ct->getCollectionTypeName()))?>" dialog-height="310" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_collection_popup?cID=<?php echo $_REQUEST['cID']?>&ctask=add&rel=<?php echo $_REQUEST['rel']?>&mode=<?php echo $_REQUEST['mode']?>&ctID=<?php echo $ct->getCollectionTypeID()?>"><?php echo $ct->getCollectionTypeIconImage(); ?></a>
					<span id="pgTypeName<?php echo $ct->getCollectionTypeID()?>"><?php echo $ct->getCollectionTypeName()?></span>
					</li> 
				
				<?php } 
				
				}?>
			
			</ul>
	</div>
	
<?php } ?>

</div>