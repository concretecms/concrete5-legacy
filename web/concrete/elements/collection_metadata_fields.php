<script>
jQuery(function() {
	jQuery("#ccm-meta-custom-fields").change(function() {
		if (jQuery(this).val() != "" && typeof(jQuery(this).val()) != undefined) {
			var thisField = jQuery(this).val();
			jQuery("#ccm-field-ak" + jQuery(this).val()).show();
			this.options[this.selectedIndex] = null;
			this.selectedIndex = 0;
			
			jQuery("#ccm-meta-field-selected" + thisField).val(thisField);
		}
	});
	
	jQuery("a.ccm-meta-close").click(function() {
		var thisField = jQuery(this).attr('id').substring(19);
		var thisName = jQuery(this).attr('ccm-meta-name');
		jQuery("#ccm-meta-field-selected" + thisField).val(0);
		jQuery("#ccm-field-ak" + thisField).hide();
		
		// add it back to the select menu
		jQuery("#ccm-meta-custom-fields").each(function() {
			this.options[this.options.length] = new Option(thisName, thisField);
		});
				
	});

	jQuery("a.ccm-meta-path-add").click(function(ev) { ccmPathHelper.add(ev.target) });
	jQuery("a.ccm-meta-path-del").click(function(ev) { ccmPathHelper.del(ev.target) });
});

var ccmPathHelper={
	add:function(field){
		var parent = jQuery(field).parent();
		var clone = parent.clone();
		clone.children().each(function() {
			if (this.id != undefined  && (i = this.id.search("-add-")) != -1) {
				this.id = this.id.substr(0, i) + "-add-" + (parseInt(this.id.substr(i+5)) + 1);
			}
			if (this.name != undefined && (i = this.name.search("-add-")) != -1) {
				this.name = this.name.substr(0, i) + "-add-" + (parseInt(this.name.substr(i+5)) + 1);
			}
			if (this.type == "text") {
				this.value = "";
			}
		});
    	jQuery(field).replaceWith('<a href="javascript:void(0)" class="ccm-meta-path-del">Remove Path</a>');
		clone.appendTo(parent.parent());

		jQuery("a.ccm-meta-path-add,a.ccm-meta-path.del").unbind('click');
		jQuery("a.ccm-meta-path-add").click(function(ev) { ccmPathHelper.add(ev.target) });
		jQuery("a.ccm-meta-path-del").click(function(ev) { ccmPathHelper.del(ev.target) });
	},
	del:function(field){
		jQuery(field).parent().remove();
	}
}
</script>


<div id="ccm-metadata-fields">
<?
$requiredKeys = array();
$usedKeys = array();
if ($c->getCollectionTypeID() > 0) {
	$cto = CollectionType::getByID($c->getCollectionTypeID());
	$aks = $cto->getAvailableAttributeKeys();
	foreach($aks as $ak) {
		$requiredKeys[] = $ak->getAttributeKeyID();
	}
}
$setAttribs = $c->getSetCollectionAttributes();
foreach($setAttribs as $ak) {
	$usedKeys[] = $ak->getAttributeKeyID();
}
$usedKeysCombined = array_merge($requiredKeys, $usedKeys);
	
?>

	<h2>
		<?=t('Custom Attributes')?> 
		<select id="ccm-meta-custom-fields">
			<option value="">** <?=t('Add Attribute')?></option>
			<? $cAttributes = CollectionAttributeKey::getList(); 
			foreach($cAttributes as $ck) { 
				if (!in_array($ck->getAttributeKeyID(), $usedKeysCombined) || $c->getCollectionTypeID()==0) {?>
				<option value="<?=$ck->getAttributeKeyID()?>"><?=$ck->getAttributeKeyName()?></option>
			<? }
			
			}?>
		</select>
	</h2><br/>

	<? 
		ob_start();
		
		$al = Loader::helper('concrete/asset_library');


		foreach($cAttributes as $ak) {
			$caValue = $c->getAttributeValueObject($ak); ?>
		
			<div class="ccm-field-meta" id="ccm-field-ak<?=$ak->getAttributeKeyID()?>" <? if (!in_array($ak->getAttributeKeyID(), $usedKeysCombined)) { ?> style="display: none" <? } ?>>
			<input type="hidden" class="ccm-meta-field-selected" id="ccm-meta-field-selected<?=$ak->getAttributeKeyID()?>" name="selectedAKIDs[]" value="<? if (!in_array($ak->getAttributeKeyID(), $usedKeysCombined)) { ?>0<? } else { ?><?=$ak->getAttributeKeyID()?><? } ?>" />
			
			<a href="javascript:void(0)" class="ccm-meta-close" ccm-meta-name="<?=$ak->getAttributeKeyName()?>" id="ccm-remove-field-ak<?=$ak->getAttributeKeyID()?>"
			  style="display:<?=(!in_array($ak->getAttributeKeyID(), $requiredKeys))?'block':'none'?>" ><?=t('Remove Attribute')?></a>
	
			
			<label><?=$ak->getAttributeKeyName()?></label>
			<?=$ak->render('form', $caValue); ?>
	
	
				<div class="ccm-spacer">&nbsp;</div>
				
			</div>
		<? } 
		$contents = ob_get_contents();
		ob_end_clean(); ?>	
		
		<script type="text/javascript">
		<? 
		$v = View::getInstance();
		$headerItems = $v->getHeaderItems();
		foreach($headerItems as $item) {
			if ($item instanceof CSSOutputObject) {
				$type = 'CSS';
			} else {
				$type = 'JAVASCRIPT';
			} ?>
			 ccm_addHeaderItem("<?=$item->file?>", '<?=$type?>');
			<? 
		} 
		?>
		</script>
		
		<? print $contents; ?>

</div>

