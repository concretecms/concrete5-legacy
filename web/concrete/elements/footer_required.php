<?

$_trackingCodePosition = Config::get('SITE_TRACKING_CODE_POSITION');
if (
	empty($disableTrackingCode)
	&&
	(
		empty($_trackingCodePosition)
		||
		$_trackingCodePosition === 'bottom'
	)
	&&
	!$c->isSystemPage()
	&&
	!$c->isEditMode()
) {
	echo Config::get('SITE_TRACKING_CODE');
}

print $this->controller->outputFooterItems();

?>