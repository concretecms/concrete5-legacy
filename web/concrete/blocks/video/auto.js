ccmValidateBlockForm = function() {
	if (jQuery("#ccm-b-flv-file-value").val() == '' || jQuery("#ccm-b-flv-file-value").val() == 0) { 
		ccm_addError(ccm_t('flv-required'));
	}
	return false;
}

ccm_chooseAsset = function(obj) {
	ccm_triggerSelectFile(obj.fID);
	if (obj.width) {
		jQuery("#ccm-block-video-width").val(obj.width);
	}
	if (obj.height) {
		jQuery("#ccm-block-video-height").val(obj.width);
	}
}