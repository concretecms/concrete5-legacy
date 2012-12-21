<?
defined('C5_EXECUTE') or die("Access Denied.");

$ui = UserInfo::getByID($_REQUEST['uID']);
$u = User::getByUserID($_REQUEST['uID']);
$uName = $ui->getUserName();
$uEmail = $ui->getUserEmail();

$attributeList = UserAttributeKey::getList();
$userGroup = $u->getUserGroups();
?>
	
<div class="ccm-ui">

		<h3><?=t('Basic Details')?></h3>
		<br>

		<div class="row">
			<div class="span3">
				<p><strong><?=t(Username)?></strong></p>
			</div>

			<div class="span2">
				<p><?=$uName?></p>
			</div>
		</div>

		<div class="row">
			<div class="span3">
				<p><strong><?=t(Email)?></strong></p>
			</div>

			<div class="span2">
				<p><a href="mailto:<?=$uEmail?>"><?=$uEmail?></a></p>
			</div>
		</div>

		<!-- user group starts -->
		<? 	if(count($userGroups) > 0) { ?>
			<h3><?=t('Groups')?></h3>
			<br>
		<? } ?>
		<!-- user group ends -->

		<!-- user attribut starts -->
		<? 	if(count($attributeList) > 0) { ?>
		<h3><?=t('User Attributes')?></h3>
		<br>
		<?	foreach ($attributeList as $key => $value) {
			if ($value->atHandle == "boolean") {
				$attributeValue = ($ui->getAttribute($value->akHandle) == 1)?"Yes":"No";
			} else if ($value->atHandle == "number") {
				$attributeValue = $ui->getAttribute($value->akHandle);
			}
		?>
		<div class="row">
			<div class="span3">
				<p><strong><?=$value->akName?></strong></p>
			</div>

			<div class="span2">
				<p><?=$attributeValue?></p>
			</div>
		</div>
		<? }
		   }?>
		<!-- // user attribut end -->
	
		<div class="dialog-buttons">
		<? $ih = Loader::helper('concrete/interface')?>
		<?=$ih->button_js(t('Cancel'), 'jQuery.fn.dialog.closeTop()', 'right', 'btn')?>
		</div>
</div><!-- // div ccm-ui end -->
