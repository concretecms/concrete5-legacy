<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 

?>



<?php 
if (count($attribs) > 0) { ?>

<div class="ccm-akci-attr-form ccm-akc-<?php echo $akCategoryHandle ?>">

<?php
	//Display attributes with no set first
	$unsetattribs = $category->getUnassignedAttributeKeys();
        if (count($unsetattribs) > 0) { ?>
        <fieldset class="ccm-akci-attr-form-attr-set ccm-akcas-none">
        	<!--legend><?php echo t('Other')?></legend-->
        <?php 
            foreach($unsetattribs as $ak) {
                if($ak->isAttributeKeyEditable()) {
                	$cssClass = 'ak_'.$ak->getKeyHandle();
                	if($error->has('ak_'.$ak->getKeyID())){
                		$cssClass .= ' ccm-error';							
                	}
    	?>
            <div class="<?php echo $cssClass ?> ccm-akci-attr-form-attr">
                <h3><?php echo $ak->render('label');?></h3>
                <?php
                        if(is_object($akci)) {
                            $aValue = $akci->getAttributeValueObject($ak);
                        }
                        echo $ak->render('form', $aValue);
                    }?>
            </div>
        <?php  } ?>
        </fieldset>
        <?php } ?>
		
		

<?php    if (count($sets) > 0) { 
        foreach($sets as $as) { ?>
        <fieldset class="ccm-akci-attr-form-attr-set ccm-akcas-<?php echo $as->getAttributeSetHandle() ?>">
        	<legend><?php echo $as->getAttributeSetName()?></legend>
        <?php 	
            $setattribs = $as->getAttributeKeys();
            if (count($setattribs) == 0) {
                echo t('No attributes defined.')?>
        </fieldset>
        <?php  } else { ?>
            <?php 
                foreach($setattribs as $ak) {
                    if($ak->isAttributeKeyEditable()) {
                    	$cssClass = 'ak_'.$ak->getKeyHandle();
                    	if($error->has('ak_'.$ak->getKeyID())){
                    		$cssClass .= ' ccm-error';							
                    	}
                    	?>
            <div class="<?php echo $cssClass ?> ccm-akci-attr-form-attr">
                <h3><?php echo $ak->render('label');?></h3>
                <?php
                        if(is_object($akci)) {
                            $aValue = $akci->getAttributeValueObject($ak);
                        }
                        echo $ak->render('form', $aValue);
                    }?>
            </div>
            <?php  } ?>
        </fieldset>
        <?php  } ?>
        <?php  } 
        
    } ?>
</div>
        
<?php  } else { ?>
        <br/>
        <strong><?php echo t('No attributes defined.'); ?></strong> <br/>
        <br/>
<?php  } ?>


<!-- VERY BADLY NEED TO MOVE THIS TO STYLESHEET -->
<style type="text/css">
	.ccm-akci-attr-form {}
	fieldset.ccm-akci-attr-form-attr-set {border:1px solid #CCC;margin:0 0 20px 0;padding:10px;}
	fieldset.ccm-akci-attr-form-attr-set > legend {font-size:16px; font-weight:bold; color:#457DA5;}
	.ccm-akci-attr-form-attr {margin:0 0 20px 0;}
	
	fieldset.ccm-akci-attr-form-attr-set.ccm-akcas-none {border:none;padding:0;}
</style>