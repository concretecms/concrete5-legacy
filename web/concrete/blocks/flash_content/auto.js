ccmValidateBlockForm = function() {
	if (jQuery("#ccm-b-file-fm-value").val() == '' || jQuery("#ccm-b-file-fm-value").val() == 0) { 
		ccm_addError(ccm_t('file-required'));
	}
	return false;
}