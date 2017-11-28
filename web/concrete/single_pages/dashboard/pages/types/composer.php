<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<?php 
$form = Loader::helper('form');
$html = Loader::helper('html');
$ih = Loader::helper('concrete/interface');
$cap = Loader::helper('concrete/dashboard');
$ctArray = CollectionType::getList();
?>

<!-- START Composer Settings pane -->

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Composer Settings'), false, false, false);?>

<?php 
if ($cap->canAccessComposer()) { ?>

	<form class="form-vertical" method="post" action="<?php echo $this->action('save')?>">

	<div class="ccm-pane-body">
	<?php echo $form->hidden('ctID', $ct->getCollectionTypeID()); ?>
    
        <h3><?php echo t("Page type").': '.$ct->getCollectionTypeName()?></h3>
        <table class="table" cellspacing="0" cellpadding="0" border="0">
            <thead>
                <tr>
                    <th class="header"><?php echo t('Included in Composer?')?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>                    
                    	<label class="checkbox inline">
                        	<?php echo $form->checkbox('ctIncludeInComposer', 1, $ct->isCollectionTypeIncludedInComposer() == 1)?>
                            <span><?php echo t('Yes, include this page type in Composer.')?></span>
                        </label>                        
                    </td>
                </tr>
			</tbody>
		</table>
        
        <table cellspacing="0" cellpadding="0" border="0" class="table">
            <thead>
                <tr>
                    <th class="header"><?php echo t('Composer Publishing Settings')?></th>
                </tr>
			</thead>
			<tbody>
                <tr class="row-composer inputs-list">
                    <td>
                    
                        <label>
                        	<?php echo $form->radio('ctComposerPublishPageMethod', 'CHOOSE', $ct->getCollectionTypeComposerPublishMethod() == 'CHOOSE' || $ct->getCollectionTypeComposerPublishMethod == null)?>
                            <span><?php echo t('Choose from all pages when publishing.')?></span>
                        </label>
                        
                        <label>
                        	<?php echo $form->radio('ctComposerPublishPageMethod', 'PAGE_TYPE', $ct->getCollectionTypeComposerPublishMethod() == 'PAGE_TYPE')?>
                            <span><?php echo t('Choose from pages of a certain type when publishing.')?></span>
                        </label>
            
                        <div style="display: none; padding: 10px" id="ccm-composer-choose-parent-page-type">
                            <?php
                            $types = array();
                            foreach($ctArray as $cta) {
                                $types[$cta->getCollectionTypeID()] = $cta->getCollectionTypeName();
                            }
                            ?>
                            <?php echo $form->select('ctComposerPublishPageTypeID', $types, $ct->getCollectionTypeComposerPublishPageTypeID())?>
                        </div>
                        
                        <label>
                        	<?php echo $form->radio('ctComposerPublishPageMethod', 'PARENT', $ct->getCollectionTypeComposerPublishMethod() == 'PARENT')?>
                            <span><?php echo t('Always publish below a certain page.')?></span>
                        </label>
                        
                        <div style="display: none; padding: 10px" id="ccm-composer-choose-parent">
							<?php 
                            $pf = Loader::helper('form/page_selector');
                            print $pf->selectPage('ctComposerPublishPageParentID', $ct->getCollectionTypeComposerPublishPageParentID());
                            ?>
                        </div>
                        
                    </td>
                </tr>
			</tbody>
		</table>
                
		<table class="table" cellspacing="0" cellpadding="0" border="0">
            <thead>
                <tr class="row-composer">
                    <th colspan="3" class="subheader"><?php echo t('Attributes to Display in Composer')?></th>
                </tr>
			</thead>
			<tbody>
                <?php
                    $selectedAttributes = array();
                    $cpattribs = $ct->getComposerAttributeKeys();
                    foreach($cpattribs as $cpa) {
                        $selectedAttributes[] = $cpa->getAttributeKeyID();
                    }
                    
                    $attribs = CollectionAttributeKey::getList();
                    $i = 0;
                    foreach($attribs as $ak) { 
                    if ($i == 0) { ?>
                        <tr class="row-composer inputs-list">
                    <?php } ?>
                    
                    	<td width="33%">
                            <label>
                                <?php echo $form->checkbox('composerAKID[]', $ak->getAttributeKeyID(), in_array($ak->getAttributeKeyID(), $selectedAttributes))?>
                                <span><?php echo $ak->getAttributeKeyDisplayName()?></span>
                            </label>
                        </td>
                    
                    <?php $i++;
                    
                    if ($i == 3) { ?>
                    </tr>
                    <?php 
                    $i = 0;
                    }
                    
                }
                
                if ($i < 3 && $i > 0) {
                    for ($j = $i; $j < 3; $j++) { ?>
                        <td>&nbsp;</td>
                    <?php }
                ?></tr>
                <?php } ?>
			</tbody>
		</table>
                
		<table class="table" cellspacing="0" cellpadding="0" border="0">
            <thead>
				<tr>
                    <th class="header"><?php echo t('Composer Content Order')?></th>
                </tr>
			</thead>
            <tbody>
                <tr>
                    <td>
                        <div class="ccm-composer-content-item-list">
                        
                        <?php
                        $cur = Loader::helper('concrete/urls');
                                    
                        foreach($contentitems as $ci) { 
                            if ($ci instanceof AttributeKey) {
                                $ak = $ci;
                            ?>
                        
                        <div class="ccm-composer-content-item" id="item_akID<?php echo $ak->getAttributeKeyID()?>">
                            <img class="ccm-composer-content-item-icon" src="<?php echo $ak->getAttributeKeyIconSRC()?>" width="16" height="16" /><?php echo $ak->getAttributeKeyDisplayName()?>
                        </div>
                
                            <?php } else if ($ci instanceof Block) { 
                                $b = $ci; ?>
            
                            
                        <div class="ccm-composer-content-item" id="item_bID<?php echo $b->getBlockID()?>">
                            <img class="ccm-composer-content-item-icon" src="<?php echo $cur->getBlockTypeIconURL($b)?>" width="16" height="16" /><?php
                                if ($b->getBlockName()) {
                                    print $b->getBlockName();
                                } else {
                                    print t($b->getBlockTypeName());
                                }
                            ?>
                        </div>
                            <?php } ?>
            
                        <?php } ?>
                        
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        
	</div>
    
    <div class="ccm-pane-footer">
        <?php print $ih->submit(t('Save'), 'update', 'right', 'primary'); ?>
        <?php print $ih->button(t('Back to Page Types'), $this->url('/dashboard/pages/types'), 'left'); ?>
    </div>
    
	</form>
    
    <script type="text/javascript">
	ccm_setupComposerFields = function() {
		
		if ($("input[name=ctIncludeInComposer]").prop('checked')) {
			$(".row-composer input, .row-composer select").attr('disabled', false);
		} else {
			$(".row-composer input, .row-composer select").attr('disabled', 'true');
		}
		var val = $('input[name=ctComposerPublishPageMethod]:checked').val();
		switch(val) {
			case 'PAGE_TYPE':
				$("#ccm-composer-choose-parent-page-type").show();
				$("#ccm-composer-choose-parent").hide();
				break;
			case 'PARENT':
				$("#ccm-composer-choose-parent-page-type").hide();
				$("#ccm-composer-choose-parent").show();
				break;
			default:
				$("#ccm-composer-choose-parent-page-type").hide();
				$("#ccm-composer-choose-parent").hide();
			break;
		}
		
		$("div.ccm-composer-content-item-list").sortable({
			handle: 'img.ccm-composer-content-item-icon',
			cursor: 'move',
			opacity: 0.5,
			stop: function() {
				var ualist = $(this).sortable('serialize');
				$.post('<?php echo $this->action("save_content_items", $ct->getCollectionTypeID())?>', ualist, function(r) {
	
				});
			}
		
		});
	}
	
	$(function() {
		$("input[name=ctIncludeInComposer], input[name=ctComposerPublishPageMethod]").click(function() {
			ccm_setupComposerFields();
		});
		ccm_setupComposerFields();
	});
	
	</script>
    
<?php } else { ?>

	<div class="ccm-pane-body">
    	<p><?php echo t('Unable to access composer settings.'); ?></p>
	</div>
    
    <div class="ccm-pane-footer">
        <?php print $ih->button(t('Back to Page Types'), $this->url('/dashboard/pages/types'), 'left'); ?>
    </div>
    
<?php } ?>

	<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>

	<!-- END Composer Settings pane -->
