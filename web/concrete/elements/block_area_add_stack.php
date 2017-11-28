<?php 
defined('C5_EXECUTE') or die("Access Denied.");
Loader::model('stack/list');
$sl = new StackList();
$sl->filterByUserAdded();
$stacks = $sl->get();

//$btl = $a->getAddBlockTypes($c, $ap );
$ci = Loader::helper('concrete/urls');
$ch = Loader::helper('concrete/interface');
$form = Loader::helper('form');
?>

<script type="text/javascript">

$('input[name=ccmStackSearch]').focus(function() {
	if ($(this).val() == '<?php echo t("Search")?>') {
		$(this).val('');
	}
	$(this).css('color', '#000');

	if (!ccmLiveSearchActive) {
		$('#ccmStackSearch').liveUpdate('ccm-stack-list', 'stacks');
		ccmLiveSearchActive = true;
//		$("#ccm-block-type-clear-search").show();
	}
});

ccmStackSearchFormCheckResults = function() {
	return false;
}


var ccmLiveSearchActive = false;
ccmStackSearchResultsSelect = function(which, e) {

	e.preventDefault();
	e.stopPropagation();

	// find the currently selected item
	var obj = $("li.ccm-item-selected");
	var foundblock = false;
	if (obj.length == 0) {
		$($("#ccm-stack-list li.ccm-stack-available")[0]).addClass('ccm-item-selected');
	} else {
		if (which == 'next') {
			var nextObj = obj.nextAll('li.ccm-stack-available');
			if (nextObj.length > 0) {
				obj.removeClass('ccm-item-selected');
				$(nextObj[0]).addClass('ccm-item-selected');
			}
		} else if (which == 'previous') {
			var prevObj = obj.prevAll('li.ccm-stack-available');
			if (prevObj.length > 0) {
				obj.removeClass('ccm-item-selected');
				$(prevObj[0]).addClass('ccm-item-selected');
			}
		}
		
	}	

	var currObj = $("li.ccm-item-selected");
	// handle scrolling
	// this is buggy. needs fixing

	var currPos = currObj.position();
	var currDialog = currObj.parents('div.ui-dialog-content');
	var docViewTop = currDialog.scrollTop();
	var docViewBottom = docViewTop + currDialog.innerHeight();

	var elemTop = currObj.position().top;
	var elemBottom = elemTop + docViewTop + currObj.innerHeight();

	if ((docViewBottom - elemBottom) < 0) {
		currDialog.get(0).scrollTop += currDialog.get(0).scrollTop + currObj.height();
	} else if (elemTop < 0) {
		currDialog.get(0).scrollTop -= currDialog.get(0).scrollTop + currObj.height();
	}


	return true;
	
}

ccmStackSearchDoMapKeys = function(e) {

	if (e.keyCode == 40) {
		ccmStackSearchResultsSelect('next', e);
	} else if (e.keyCode == 38) {
		ccmStackSearchResultsSelect('previous', e);
	} else if (e.keyCode == 13) {
		var obj = $("li.ccm-item-selected");
		if (obj.length > 0) {
			obj.find('a').click();
		}
	}
}
ccmStackSearchMapKeys = function() {
	$(window).bind('keydown.stacks', ccmStackSearchDoMapKeys);
}
ccmStackSearchResetKeys = function() {
	$(window).unbind('keydown.stacks');
}

ccmStackAddToArea = function(stackID, arHandle) {
	ccmStackSearchResetKeys();
	jQuery.fn.dialog.showLoader();
	$.get('<?php echo DIR_REL?>/<?php echo DISPATCHER_FILENAME?>?atask=add_stack&stID=' + stackID + '&cID=<?php echo $c->getCollectionID()?>&arHandle=' + encodeURIComponent(arHandle) + '&<?php echo $token?>', 
		function(r) { ccm_parseBlockResponse(r, false, 'add'); 
	});
}

$(function() {
	$(window).css('overflow', 'hidden');
	$(window).unbind('keydown.stacks');
	ccmStackSearchMapKeys();
	$("#ccmStackSearch").get(0).focus();

});

</script>


<div id="ccm-add-tab" class="ccm-ui">
	<div class="ccm-pane-options">
		<div class="ccm-block-type-search-wrapper ccm-pane-options-permanent-search">

		<form onsubmit="return ccmStackSearchFormCheckResults()">
		<i class="icon-search"></i>
		<?php echo $form->text('ccmStackSearch', array('tabindex' => 1, 'autocomplete' => 'off', 'style' => 'margin-left: 8px; width: 168px'))?>
		</form>
		
		
		</div>		
	</div>
	
	<?php if (count($stacks) > 0) { ?>
		<ul id="ccm-stack-list" class="item-select-list item-select-list-groups">
		<?php foreach($stacks as $s) { 
			$as = Area::get($s, STACKS_AREA_NAME);
			$asp = new Permissions($as);
			if ($asp->canRead() && $ap->canAddStackToArea($s)) { 
			?>	
			<li class="ccm-stack-available">
				<a onclick="ccmStackSearchResetKeys()" dialog-on-destroy="ccmStackSearchMapKeys()" class="dialog-launch ccm-block-type-inner" dialog-on-close="ccm_blockWindowAfterClose()" dialog-append-buttons="true" dialog-modal="false" dialog-width="620" dialog-height="400" dialog-title="<?php echo $s->getCollectionName()?> <?php echo t('Contents')?>" href="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/edit_area_popup.php?atask=add_stack_contents&cID=<?php echo $c->getCollectionID()?>&stackID=<?php echo $s->getCollectionID()?>&arHandle=<?php echo Loader::helper('text')->entities($a->getAreaHandle())?>"><?php echo $s->getCollectionName()?></a>
			</li>
			
			<?php } ?>
			
		<?php } ?>
		</ul>
		<?php
	} else { ?>
		<br/>
		<p><?php echo t('No stacks can be added to this area.')?></p>
	<?php } ?>
</div>