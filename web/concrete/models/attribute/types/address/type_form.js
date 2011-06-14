jQuery(function() {
	jQuery("input[name=akHasCustomCountries]").click(function() {
		ccm_attributeTypeAddressCountries(jQuery(this));
	});
	
	ccm_attributeTypeAddressCountries();
});

ccm_attributeTypeAddressCountries = function(obj) {
	if (!obj) {
		var obj = jQuery("input[name=akHasCustomCountries][checked=checked]");
	}
	if (obj.attr('value') == 1) {
		jQuery("#akCustomCountries").attr('disabled' , false);
	} else {
		jQuery("#akCustomCountries").attr('disabled' , true);
		jQuery("#akCustomCountries option").attr('selected', true);
	}
}
