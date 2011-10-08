<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); 

?>



<?php 
if (count($attribs) > 0) { 
    if (count($sets) > 0) { 
        foreach($sets as $as) { ?>
        <fieldset style="border-color:#CCCCCC"><legend style="font-size:16px; font-weight:bold; color:#457DA5;"><?php echo $as->getAttributeSetName()?></legend>
        <?php 	
            $setattribs = $as->getAttributeKeys();
            if (count($setattribs) == 0) {
                echo t('No attributes defined.')?>
        </fieldset>
        <?php  } else { ?>
            <?php 
                foreach($setattribs as $ak) {
                    if($ak->isAttributeKeyEditable()) {?>
            <div style="margin-bottom:20px;">
                <h3><?php echo $ak->render('label');?></h3>
                <?php
                        if(is_object($newObject)) {
                            $aValue = $newObject->getAttributeValueObject($ak);
                        }
                        echo $ak->render('form', $aValue);
                    }?>
            </div>
            <?php  } ?>
        </fieldset>
        <?php  } ?>
        <?php  } 
        $unsetattribs = $category->getUnassignedAttributeKeys();
        if (count($unsetattribs) > 0) { ?>
        <fieldset style="border-color:#CCCCCC"><legend style="font-size:16px; font-weight:bold; color:#457DA5;"><?php echo t('Other')?></legend>
        <?php 
            foreach($unsetattribs as $ak) {
                if($ak->isAttributeKeyEditable()) {?>
            <div style="margin-bottom:20px;">
                <h3><?php echo $ak->render('label');?></h3>
                <?php
                        if(is_object($newObject)) {
                            $aValue = $newObject->getAttributeValueObject($ak);
                        }
                        echo $ak->render('form', $aValue);
                    }?>
            </div>
        <?php  } ?>
        </fieldset>
        <?php }
    } else { ?>
        <div class="ccm-attributes-list">
            <?php 
        foreach($attribs as $ak) { 
            if($ak->isAttributeKeyEditable()) {?>
            <div style="margin-bottom:20px;">
                <h3><?php echo $ak->render('label');?></h3>
                <?php
                        if(is_object($newObject)) {
                            $aValue = $newObject->getAttributeValueObject($ak);
                        }
                        echo $ak->render('form', $aValue);
                    }?>
            </div>
            <?php  } ?>
        </div>
        <?php  } ?>
        <?php  } else { ?>
        <br/>
        <strong><?php echo t('No attributes defined.'); ?></strong> <br/>
        <br/>
<?php  } ?>
