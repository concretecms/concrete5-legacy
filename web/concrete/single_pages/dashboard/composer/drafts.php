<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Composer Drafts'))?>

<?php
$dh = Loader::helper('date');
/* @var $dh DateHelper */

$today = $dh->getLocalDateTime('now', 'Y-m-d');
if (count($drafts) > 0) { ?>

<table class="table table-striped">
<tr>
	<th width="60%"><?php echo t('Page Name')?></th>
	<th width="20%"><?php echo t('Page Type')?></th>
	<th width="20%"><?php echo t('Last Modified')?></th>
</tr>
<?php foreach($drafts as $dr) { ?>
<tr>
	<td><a href="<?php echo $this->url('/dashboard/composer/write', 'edit', $dr->getCollectionID())?>"><?php if (!$dr->getCollectionName()) {
		print t('(Untitled Page)');
	} else {
		print $dr->getCollectionName();
	} ?></a></td>
	<td><?php echo $dr->getCollectionTypeName()?></td>
	<td><?php
		if ($today == $dr->getCollectionDateLastModified("Y-m-d")) {
			print $dh->formatTime($dr->getCollectionDateLastModified(), false);
		}
		else {
			print $dh->formatDateTime($dr->getCollectionDateLastModified(), false, false);
		}
	?></td>
<?php } ?>
</table>

<?php } else { ?>
	
	<p><?php echo t('You have not created any drafts. <a href="%s">Visit Composer &gt;</a>', $this->url('/dashboard/composer/write'))?></p>

<?php } ?>

<?php echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>