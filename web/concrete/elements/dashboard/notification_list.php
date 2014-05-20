<?
function getNotificationClassName($n) {
	switch($n->getSystemNotificationTypeID()) {
		case SystemNotification::SN_TYPE_CORE_MESSAGE_HELP:
			return 'ccm-dashboard-notification-core-message-help';
			break;
		case SystemNotification::SN_TYPE_CORE_MESSAGE_NEWS:
			return 'ccm-dashboard-notification-core-message-news';
			break;
		case SystemNotification::SN_TYPE_ADDON_UPDATE:
			return 'ccm-dashboard-notification-addon-update';
			break;
		case SystemNotification::SN_TYPE_CORE_UPDATE_CRITICAL:
		case SystemNotification::SN_TYPE_ADDON_UPDATE_CRITICAL:
			return 'ccm-dashboard-notification-critical';
			break;
		case SystemNotification::SN_TYPE_ADDON_MESSAGE:
			return 'ccm-dashboard-notification-addon-message';
			break;
		case SystemNotification::SN_TYPE_CORE_UPDATE:
			return 'ccm-dashboard-notification-core-update';
			break;
		case SystemNotification::SN_TYPE_CORE_MESSAGE_OTHER:
		default:
			return 'ccm-dashboard-notification-generic';
			break;
	}
}

?>


<ul id="ccm-dashboard-notification-list">
<? 
$lastDate = false;
$txt = Loader::helper('text');
$dh = Loader::helper('date');
/* @var $dh DateHelper */
foreach($notifications as $n) { 
	$date = $dh->formatPrettyDate($n->getSystemNotificationDateTime(), true);
	$time = $dh->formatTime($n->getSystemNotificationDateTime(), false);
	
	if ($date != $lastDate) {
		?><li class="ccm-dashboard-notification-list-date"><h2><?=$date?></h2></li><?
		$lastDate = $date;
	} ?>
	
	<li class="<?=getNotificationClassName($n)?>">
	
	<h3><?=$n->getSystemNotificationTitle()?> <span class="ccm-dashboard-notification-time"><?=$time?></span></h3>
	<p><?=$n->getSystemNotificationDescription()?></p>
	
	<? 
	if ($n->getSystemNotificationTypeID() == SystemNotification::SN_TYPE_CORE_UPDATE || $n->getSystemNotificationTypeID() == SystemNotification::SN_TYPE_CORE_UPDATE_CRITICAL) {
		$bodyReadMore = t('Full Release Notes');
		$readMore = t('Update concrete5');
	} else if ($n->getSystemNotificationTypeID() == SystemNotification::SN_TYPE_ADDON_UPDATE || $n->getSystemNotificationTypeID() == SystemNotification::SN_TYPE_ADDON_UPDATE_CRITICAL) {
		$bodyReadMore = t('Full Release Notes');
		$readMore = t('Update Addon');
	} else {
		$bodyReadMore = t('Read More');
		$readMore = t('Read Full Post');
	}

	if ($n->getSystemNotificationBody() != '' && $n->getSystemNotificationBody() != $n->getSystemNotificationDescription()) { ?>		
		<div id="ccmSystenNotificationBody<?=$n->getSystemNotificationID()?>" style="display: none"><?=$n->getSystemNotificationBody()?></div>
		<a href="javascript:void(0)" class="ccm-dashboard-notification-read-more" onclick="ccmDisplayNotificationBody('<?=$n->getSystemNotificationID()?>')"><?=$bodyReadMore?></a> | 
	<? } ?>
	
	
	<a class="ccm-dashboard-notification-read-more" href="<?=$n->getSystemNotificationURL()?>"><?=$readMore?></a>	
	
	<? if ($n->getSystemNotificationAlternateURL() != '') { ?>|
		<a class="ccm-dashboard-notification-read-more" href="<?=$n->getSystemNotificationAlternateURL()?>"><?=$bodyReadMore?></a>	
	<? } ?>
	
	</li>

	<? 
	if ($n->isSystemNotificationNew()) {
		$n->markSystemNotificationAsRead();
	}
} ?>
</ul>

<script type="text/javascript">
ccmDisplayNotificationBody = function(snID) {
	jQuery.fn.dialog.open({modal: false, title: "<?=t('More Information')?>", width: 500, height: 400, element: $('#ccmSystenNotificationBody' + snID)});
}
</script>