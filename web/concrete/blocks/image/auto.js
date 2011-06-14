ccmValidateBlockForm = function() {
	if (jQuery("#ccm-b-image-fm-value").val() == '' || jQuery("#ccm-b-image-fm-value").val() == 0) { 
		ccm_addError(ccm_t('image-required'));
	}
	return false;
}