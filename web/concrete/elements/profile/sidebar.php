<?php
$av = Loader::helper('concrete/avatar');

/** @type ValidationTokenHelper $token */
$token = Loader::helper('validation/token');
?>
<div id="ccm-profile-sidebar">
	<div class="ccm-profile-header">
		<a href="<?php echo View::url('/profile',$profile->getUserID())?>"><?php echo $av->outputUserAvatar($profile)?></a><br />
		<a href="<?php echo View::url('/profile',$profile->getUserID())?>"><?php echo $profile->getUsername()?></a>
	</div>
	<div style="margin-top:16px; padding-bottom:4px; margin-bottom:0px; font-weight:bold"><?php echo t('Member Since')?></div>
	<?php echo Loader::helper('date')->formatDate($profile->getUserDateAdded(), true)?>

	<?php
	$u = new User();
	if ($u->isRegistered() && $u->getUserID() != $profile->getUserID()) { ?>
	<div style="margin-top:16px;">
		<div>
		<?php if( !UsersFriends::isFriend( $profile->getUserID(), $u->uID ) ){ ?>
			<a href="<?php echo View::url('/profile/friends','add_friend', $profile->getUserID(), $token->generate('profile.add_friend.' . $profile->getUserID()))?>">
				<?php echo t('Add to My Friends') ?>
			</a>
		<?php }else{ ?>
			<a href="<?php echo View::url('/profile/friends','remove_friend', $profile->getUserID(), $token->generate('profile.remove_friend.' . $profile->getUserID()))?>">
				<?php echo t('Remove from My Friends') ?>
			</a>
		<?php } ?>

		</div>
		<?php if ($profile->getUserProfilePrivateMessagesEnabled() == 1) { ?>
			<a href="<?php echo $this->url('/profile/messages', 'write', $profile->getUserID())?>"><?php echo t('Send Private Message')?></a>
		<?php } ?>

	</div>
	<?php } ?>


	<div>
	<?php
	if($u->getUserID() == $profile->getUserID()) {
		$nc = Page::getByPath('/profile');
		$pl = new PageList();
		$pl->filterByParentID($nc->getCollectionID());
		$pages = $pl->get(0);
		if (is_array($pages) && !empty($pages)) {
			$nh = Loader::helper('navigation');
			?>
			<ul class="nav">
			<?php foreach ($pages as $page) { ?>
				<li><a href="<?php echo $nh->getLinkToCollection($page) ?>"><?php echo t($page->getCollectionName())?></a></li>
			<?php } ?>
			</ul>
		<?php
		}
	}
	?>
	</div>

		<form method="get" action="<?php echo $this->url('/members')?>">
		<h4><?php echo t('Search Members')?></h4>
		<?php
		$form = Loader::helper('form');
		print $form->text('keywords', array('style' => 'width: 80px'));
		print '&nbsp;&nbsp;';
		print $form->submit('submit', t('Search'));
		?>

		</form>

</div>
