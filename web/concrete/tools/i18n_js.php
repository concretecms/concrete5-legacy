<?php defined('C5_EXECUTE') or die('Access Denied.');

$jh = Loader::helper('json');
/* @var $jh JsonHelper */

header('Content-type: text/javascript'); ?>

var ccmi18n = {

	error: <?php echo $jh->encode(t('Error')); ?>,
	deleteBlock: <?php echo $jh->encode(t('Delete')); ?>,
	deleteBlockMsg: <?php echo $jh->encode(t('The block has been removed successfully.')); ?>,
	addBlock: <?php echo $jh->encode(t('Add Block')); ?>,
	addBlockNew: <?php echo $jh->encode(t('Add Block')); ?>,
	addBlockStack: <?php echo $jh->encode(t('Add Stack')); ?>,
	addBlockPaste: <?php echo $jh->encode(t('Paste from Clipboard')); ?>,
	changeAreaCSS: <?php echo $jh->encode(t('Design')); ?>,
	editAreaLayout: <?php echo $jh->encode(t('Edit Layout')); ?>,
	addAreaLayout: <?php echo $jh->encode(t('Add Layout')); ?>,
	moveLayoutUp: <?php echo $jh->encode(t('Move Up')); ?>,
	moveLayoutDown: <?php echo $jh->encode(t('Move Down')); ?>,
	moveLayoutAtBoundary: <?php echo $jh->encode(t('This layout section can not be moved further in this direction.')); ?>,
	lockAreaLayout: <?php echo $jh->encode(t('Lock Layout')); ?>,
	unlockAreaLayout: <?php echo $jh->encode(t('Unlock Layout')); ?>,
	deleteLayout: <?php echo $jh->encode(t('Delete')); ?>,
	deleteLayoutOptsTitle: <?php echo $jh->encode(t('Delete Layout')); ?>,
	confirmLayoutPresetDelete: <?php echo $jh->encode(t('Are you sure you want to delete this layout preset?')); ?>,
	setAreaPermissions: <?php echo $jh->encode(t('Set Permissions')); ?>,
	addBlockMsg: <?php echo $jh->encode(t('The block has been added successfully.')); ?>,
	updateBlock: <?php echo $jh->encode(t('Update Block')); ?>,
	updateBlockMsg: <?php echo $jh->encode(t('The block has been saved successfully.')); ?>,
	copyBlockToScrapbookMsg: <?php echo $jh->encode(t('The block has been added to your clipboard.')); ?>,
	closeWindow: <?php echo $jh->encode(t('Close')); ?>,
	editBlock: <?php echo $jh->encode(t('Edit')); ?>,
	editBlockWithName: <?php echo $jh->encode(tc('%s is a block type name', 'Edit %s')); ?>,
	setPermissionsDeferredMsg: <?php echo $jh->encode(t('Permission setting saved. You must complete the workflow before this change is active.')); ?>,
	editStackContents: <?php echo $jh->encode(t('Manage Stack Contents')); ?>,
	compareVersions: <?php echo $jh->encode(t('Compare Versions')); ?>,
	blockAreaMenu: <?php echo $jh->encode(t("Add Block")); ?>,
	arrangeBlock: <?php echo $jh->encode(t('Move')); ?>,
	arrangeBlockMsg: <?php echo $jh->encode(t('Blocks arranged successfully.')); ?>,
	copyBlockToScrapbook: <?php echo $jh->encode(t('Copy to Clipboard')); ?>,
	changeBlockTemplate: <?php echo $jh->encode(t('Custom Template')); ?>,
	changeBlockCSS: <?php echo $jh->encode(t("Design")); ?>,
	errorCustomStylePresetNoName: <?php echo $jh->encode(t('You must give your custom style preset a name.')); ?>,
	changeBlockBaseStyle: <?php echo $jh->encode(t("Set Block Styles")); ?>,
	confirmCssReset: <?php echo $jh->encode(t("Are you sure you want to remove all of these custom styles?")); ?>,
	confirmCssPresetDelete: <?php echo $jh->encode(t("Are you sure you want to delete this custom style preset?")); ?>,
	setBlockPermissions: <?php echo $jh->encode(t('Set Permissions')); ?>,
	setBlockAlias: <?php echo $jh->encode(t('Setup on Child Pages')); ?>,
	setBlockComposerSettings: <?php echo $jh->encode(t("Composer Settings")); ?>,
	themeBrowserTitle: <?php echo $jh->encode(t('Get More Themes')); ?>,
	themeBrowserLoading: <?php echo $jh->encode(t('Retrieving theme data from concrete5.org marketplace.')); ?>,
	addonBrowserLoading: <?php echo $jh->encode(t('Retrieving add-on data from concrete5.org marketplace.')); ?>,
	clear: <?php echo $jh->encode(t('Clear')); ?>,
	requestTimeout: <?php echo $jh->encode(t('This request took too long.')); ?>,
	generalRequestError: <?php echo $jh->encode(t('An unexpected error occurred.')); ?>,
	helpPopup: <?php echo $jh->encode(t('Help')); ?>,
	community: <?php echo $jh->encode(t('concrete5 Community')); ?>,
	communityCheckout: <?php echo $jh->encode(t('concrete5 Community - Purchase &amp; Checkout')); ?>,
	noIE6: <?php echo $jh->encode(t('concrete5 does not support Internet Explorer 6 in edit mode.')); ?>,
	helpPopupLoginMsg: <?php echo $jh->encode(t('Get more help on your question by posting it to the concrete5 help center on concrete5.org')); ?>,
	marketplaceErrorMsg: <?php echo $jh->encode(t('<p>You package could not be installed.  An unknown error occured.</p>')); ?>,
	marketplaceInstallMsg: <?php echo $jh->encode(t('<p>Your package will now be downloaded and installed.</p>')); ?>,
	marketplaceLoadingMsg: <?php echo $jh->encode(t('<p>Retrieving information from the concrete5 Marketplace.</p>')); ?>,
	marketplaceLoginMsg: <?php echo $jh->encode(t('<p>You must be logged into the concrete5 Marketplace to install add-ons and themes.  Please log in.</p>')); ?>,
	marketplaceLoginSuccessMsg: <?php echo $jh->encode(t('<p>You have successfully logged into the concrete5 Marketplace.</p>')); ?>,
	marketplaceLogoutSuccessMsg: <?php echo $jh->encode(t('<p>You are now logged out of concrete5 Marketplace.</p>')); ?>,
	deleteAttributeValue: <?php echo $jh->encode(t('Are you sure you want to remove this value?')); ?>,
	customizeSearch: <?php echo $jh->encode(t('Customize Search')); ?>,
	properties: <?php echo $jh->encode(t('Properties')); ?>,
	savePropertiesMsg: <?php echo $jh->encode(t('Page Properties saved.')); ?>,
	saveSpeedSettingsMsg: <?php echo $jh->encode(t("Full page caching settings saved.")); ?>,
	saveUserSettingsMsg: <?php echo $jh->encode(t("User Settings saved.")); ?>,
	ok: <?php echo $jh->encode(t('Ok')); ?>,
	scheduleGuestAccess: <?php echo $jh->encode(t('Schedule Guest Access')); ?>,
	scheduleGuestAccessSuccess: <?php echo $jh->encode(t('Timed Access for Guest Users Updated Successfully.')); ?>,
	newsflowLoading: <?php echo $jh->encode(t("Checking for updates.")); ?>,
	authoredBy: <?php echo $jh->encode(t('by')); ?>,
	x: <?php echo $jh->encode(t('x')); ?>,
	user_activate: <?php echo $jh->encode(t('Activate Users')); ?>,
	user_deactivate: <?php echo $jh->encode(t('Deactivate Users')); ?>,
	user_delete: <?php echo $jh->encode(t('Delete')); ?>,
	user_group_remove: <?php echo $jh->encode(t('Remove From Group')); ?>,
	user_group_add: <?php echo $jh->encode(t('Add to Group')); ?>
};

var ccmi18n_sitemap = {

	visitExternalLink: <?php echo $jh->encode(t('Visit')); ?>,
	editExternalLink: <?php echo $jh->encode(t('Edit External Link')); ?>,
	deleteExternalLink: <?php echo $jh->encode(t('Delete')); ?>,
	copyProgressTitle: <?php echo $jh->encode(t('Copy Progress')); ?>,
	addExternalLink: <?php echo $jh->encode(t('Add External Link')); ?>,
	sendToTop: <?php echo $jh->encode(t('Send To Top')); ?>,
	sendToBottom: <?php echo $jh->encode(t('Send To Bottom')); ?>,
	emptyTrash: <?php echo $jh->encode(t('Empty Trash')); ?>,
	restorePage: <?php echo $jh->encode(t('Restore Page')); ?>,
	deletePageForever: <?php echo $jh->encode(t('Delete Forever')); ?>,
	previewPage: <?php echo $jh->encode(t('Preview')); ?>,
	visitPage: <?php echo $jh->encode(t('Visit')); ?>,
	pageProperties: <?php echo $jh->encode(t('Properties')); ?>,
	speedSettings: <?php echo $jh->encode(t('Full Page Caching')); ?>,
	speedSettingsTitle: <?php echo $jh->encode(t('Full Page Caching')); ?>,
	pagePropertiesTitle: <?php echo $jh->encode(t('Page Properties')); ?>,
	pagePermissionsTitle: <?php echo $jh->encode(t('Page Permissions')); ?>,
	setPagePermissions: <?php echo $jh->encode(t('Set Permissions')); ?>,
	setPagePermissionsMsg: <?php echo $jh->encode(t('Page permissions updated successfully.')); ?>,
	pageDesignMsg: <?php echo $jh->encode(t('Theme and page type updated successfully.')); ?>,
	pageDesign: <?php echo $jh->encode(t('Design')); ?>,
	pageVersions: <?php echo $jh->encode(t('Versions')); ?>,
	deletePage: <?php echo $jh->encode(t('Delete')); ?>,
	deletePages: <?php echo $jh->encode(t('Delete Pages')); ?>,
	deletePageSuccessMsg: <?php echo $jh->encode(t('The page has been removed successfully.')); ?>,
	deletePageSuccessDeferredMsg: <?php echo $jh->encode(t('Delete request saved. You must complete the workflow before the page is fully removed.')); ?>,
	addPage: <?php echo $jh->encode(t('Add Page')); ?>,
	moveCopyPage: <?php echo $jh->encode(t('Move/Copy')); ?>,
	reorderPage: <?php echo $jh->encode(t('Change Page Order')); ?>,
	reorderPageMessage: <?php echo $jh->encode(t('Move or reorder pages by dragging their icons.')); ?>,
	moveCopyPageMessage: <?php echo $jh->encode(t('Choose a new parent page from the sitemap.')); ?>,
	editInComposer: <?php echo $jh->encode(t('Edit in Composer')); ?>,

	searchPages: <?php echo $jh->encode(t('Search Pages')); ?>,
	explorePages: <?php echo $jh->encode(t('Flat View')); ?>,
	backToSitemap: <?php echo $jh->encode(t('Back to Sitemap')); ?>,
	searchResults: <?php echo $jh->encode(t('Search Results')); ?>,
	createdBy: <?php echo $jh->encode(t('Created By')); ?>,
	choosePage: <?php echo $jh->encode(t('Choose a Page')); ?>,
	viewing: <?php echo $jh->encode(t('Viewing')); ?>,
	results: <?php echo $jh->encode(t('Result(s)')); ?>,
	max: <?php echo $jh->encode(t('max')); ?>,
	noResults: <?php echo $jh->encode(t('No results found.')); ?>,
	areYouSure: <?php echo $jh->encode(t('Are you sure?')); ?>,
	loadError: <?php echo $jh->encode(t('Unable to load sitemap data. Response received: ')); ?>,
	loadErrorTitle: <?php echo $jh->encode(t('Unable to load sitemap data.')); ?>,
	on: <?php echo $jh->encode(t('on')); ?>

};

var ccmi18n_spellchecker = {

	resumeEditing: <?php echo $jh->encode(t('Resume Editing')); ?>,
	noSuggestions: <?php echo $jh->encode(t('No Suggestions')); ?>

};

var ccmi18n_filemanager = {

	view: <?php echo $jh->encode(t('View')); ?>,
	download: <?php echo $jh->encode(t('Download')); ?>,
	select: <?php echo $jh->encode(t('Choose')); ?>,
	duplicateFile: <?php echo $jh->encode(t('Copy File')); ?>,
	clear: <?php echo $jh->encode(t('Clear')); ?>,
	edit: <?php echo $jh->encode(t('Edit')); ?>,
	replace: <?php echo $jh->encode(t('Replace')); ?>,
	duplicate: <?php echo $jh->encode(t('Copy')); ?>,
	chooseNew: <?php echo $jh->encode(t('Choose New File')); ?>,
	sets: <?php echo $jh->encode(t('Sets')); ?>,
	permissions: <?php echo $jh->encode(t('Access & Permissions')); ?>,
	properties: <?php echo $jh->encode(t('Properties')); ?>,
	deleteFile: <?php echo $jh->encode(t('Delete')); ?>,
	title: <?php echo $jh->encode(t('File Manager')); ?>,
	uploadErrorChooseFile: <?php echo $jh->encode(t('You must choose a file.')); ?>,
	rescan: <?php echo $jh->encode(t('Rescan')); ?>,
	pending: <?php echo $jh->encode(t('Pending')); ?>,
	uploadComplete: <?php echo $jh->encode(t('Upload Complete')); ?>,

	PTYPE_CUSTOM: <?php echo '""'; //$jh->encode(FilePermissions::PTYPE_CUSTOM); ?>,
	PTYPE_NONE: <?php echo '""'; //$jh->encode(FilePermissions::PTYPE_NONE); ?>,
	PTYPE_ALL: <?php echo '""'; //$jh->encode(FilePermissions::PTYPE_ALL); ?>,

	FTYPE_IMAGE: <?php echo $jh->encode(FileType::T_IMAGE); ?>,
	FTYPE_VIDEO: <?php echo $jh->encode(FileType::T_VIDEO); ?>,
	FTYPE_TEXT: <?php echo $jh->encode(FileType::T_TEXT); ?>,
	FTYPE_AUDIO: <?php echo $jh->encode(FileType::T_AUDIO); ?>,
	FTYPE_DOCUMENT: <?php echo $jh->encode(FileType::T_DOCUMENT); ?>,
	FTYPE_APPLICATION: <?php echo $jh->encode(FileType::T_APPLICATION); ?>

};

var ccmi18n_chosen = {

	placeholder_text_multiple: <?php echo $jh->encode(t('Select Some Options')); ?>,
	placeholder_text_single: <?php echo $jh->encode(t('Select an Option')); ?>,
	no_results_text: <?php echo $jh->encode(t(/*i18n After this text we have a search criteria: for instance 'No results match "Criteria"'*/'No results match')); ?>

};
