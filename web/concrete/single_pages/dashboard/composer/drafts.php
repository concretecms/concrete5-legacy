<? defined('C5_EXECUTE') or die("Access Denied."); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Composer Drafts'))?>

<?
$dh = Loader::helper('date');
/* @var $dh DateHelper */

$today = $dh->getLocalDateTime('now', 'Y-m-d');
if (count($drafts) > 0) { ?>

<table class="table table-striped">
<tr>
	<th width="60%"><?=t('Page Name')?></th>
	<th width="20%"><?=t('Page Type')?></th>
	<th width="20%"><?=t('Last Modified')?></th>
</tr>
<? foreach($drafts as $dr) { ?>
<tr>
	<td><a href="<?=$this->url('/dashboard/composer/write', 'edit', $dr->getCollectionID())?>"><? if (!$dr->getCollectionName()) {
		print t('(Untitled Page)');
	} else {
		print $dr->getCollectionName();
	} ?></a></td>
	<td><?=$dr->getCollectionTypeName()?></td>
	<td><?
		if ($today == $dr->getCollectionDateLastModified("Y-m-d")) {
			print $dh->formatTime($dr->getCollectionDateLastModified(), false);
		}
		else {
			print $dh->formatDateTime($dr->getCollectionDateLastModified(), false, false);
		}
	?></td>
<? } ?>
</table>

<? } else { ?>
	
	<p><?=t('You have not created any drafts. <a href="%s">Visit Composer &gt;</a>', $this->url('/dashboard/composer/write'))?></p>

<? } ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>