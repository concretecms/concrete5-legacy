<?php defined('C5_EXECUTE') or die("Access Denied.");
Loader::library('authentication/open_id');
$form = Loader::helper('form');
?>
<script type="text/javascript">
$(function() {
	$("input[name=uName]").focus();
});
</script>

<?php if (isset($intro_msg)) { ?>
	<div class="alert-message block-message success"><p><?php echo $intro_msg; ?></p></div>
<?php } ?>

<div class="row">
	<div class="span10 offset1">
		<div class="page-header">
			<h1><?php echo t('Sign in to %s', SITE); ?></h1>
		</div>
	</div>
</div>

<?php if ($passwordChanged) { ?>
	<div class="block-message info alert-message"><p><?php echo t('Password changed.  Please login to continue. '); ?></p></div>
<?php } ?>

<?php if ($changePasswordForm) { ?>
	<form method="post" action="<?php echo $this->url( '/login', 'change_password', $uHash ); ?>" class="form-horizontal ccm-change-password-form">
		<div class="row">
			<div class="span10 offset1">
				<fieldset>
					<legend><?php echo t('Change Password'); ?></legend>
					<p><?php echo t('Enter your new password below.'); ?></p>
					<div class="control-group">
						<label for="uPassword" class="control-label"><?php echo t('New Password'); ?></label>
						<div class="controls">
							<input type="password" name="uPassword" id="uPassword" class="ccm-input-text" autofocus>
						</div>
					</div>
					<div class="control-group">
						<label for="uPasswordConfirm" class="control-label"><?php echo t('Confirm Password'); ?></label>
						<div class="controls">
							<input type="password" name="uPasswordConfirm" id="uPasswordConfirm" class="ccm-input-text">
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="row">
			<div class="span10 offset1">
				<div class="actions">
					<?php echo $form->submit('submit', t('Sign In') . ' &gt;', array('class' => 'primary')); ?>
				</div>
			</div>
		</div>
	</form>
	
<?php } elseif ($validated) { ?>
	<div class="row">
		<div class="span10 offset1">
			<fieldset>
				<legend><?php echo t('Email Address Verified'); ?></legend>
				<div class="success alert-message block-message">
					<p><?php echo t('The email address <b>%s</b> has been verified and you are now a fully validated member of this website.', $uEmail); ?></p>
				</div>
			</fieldset>
		</div>
	</div>
	<div class="row">
		<div class="span10 offset1">
			<div class="actions">
				<a class="btn primary" href="<?php echo $this->url('/'); ?>"><?php echo t('Continue to Site'); ?></a>
			</div>
		</div>
	</div>

<?php } elseif (isset($_SESSION['uOpenIDError']) && isset($_SESSION['uOpenIDRequested'])) {
		switch($_SESSION['uOpenIDError']) {
			case OpenIDAuth::E_REGISTRATION_EMAIL_INCOMPLETE:
				?>
				<form method="post" action="<?php echo $this->url('/login', 'complete_openid_email'); ?>" class="form-horizontal ccm-openid-login-form">
					<div class="row">
						<div class="span10 offset1">
							<fieldset>
								<legend><?php echo t('Specify your OpenID email address'); ?></legend>
								<p><?php echo t('To complete the signup process, you must provide a valid email address.'); ?></p>
								<div class="control-group">
									<label for="uEmail" class="control-label"><?php echo t('Email Address'); ?></label>
									<div class="controls">
										<?php echo $form->email('uEmail', array('autofocus' => 'autofocus')); ?>
									</div>
								</div>
							</fieldset>
						</div>
					</div>
					<div class="row">
						<div class="span10 offset1">
							<div class="actions">
								<?php echo $form->submit('submit', t('Sign In') . ' &gt;', array('class' => 'primary')); ?>
							</div>
						</div>
					</div>
				</form>
				<?php
				break;
			case OpenIDAuth::E_REGISTRATION_EMAIL_EXISTS:
				$ui = UserInfo::getByID($_SESSION['uOpenIDExistingUser']);
				?>
				<form method="post" action="<?php echo $this->url('/login', 'do_login'); ?>" class="form-horizontal ccm-openid-merge-form">
					<div class="row">
						<div class="span10 offset1">
							<fieldset>
								<legend><?php echo t('Merge local account with OpenID'); ?></legend>
								<p><?php echo t(/*i18n: %s is an email address */ 'The OpenID account returned an email address already registered on this site (%s). To join this OpenID to the existing user account, login below:', '<strong>' . $ui->getUserEmail() . '</strong>'); ?></p>
								<div class="control-group">
									<label for="uName" class="control-label"><?php
										if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) {
											echo t('Email Address');
										} else {
											echo t('Username');
										}
									?></label>
									<div class="controls">
										<input type="text" name="uName" id="uName" <?php echo isset($uName) ? ('value="'.h($uName).'"') : ''; ?> class="ccm-input-text" autofocus>
									</div>
								</div>
								<div class="control-group">
									<label for="uPassword" class="control-label"><?php echo t('Password'); ?></label>
									<div class="controls">
										<input type="password" name="uPassword" id="uPassword" class="ccm-input-text">
									</div>
								</div>
							</fieldset>
						</div>
					</div>
					<div class="row">
						<div class="span10 offset1">
							<div class="actions">
								<?php echo $form->submit('submit', t('Sign In') . ' &gt;', array('class' => 'primary')); ?>
							</div>
						</div>
					</div>
				</form>
				<?php
				break;
		}

} elseif ($invalidRegistrationFields == true) { ?>
	<form method="post" action="<?php echo $this->url('/login', 'do_login'); ?>" class="form-horizontal ccm-missing-login-fields-form">
		<div class="row">
			<div class="span10 offset1">
				<fieldset>
					<legend><?php echo t('Fill in missing fields'); ?></legend>
					<p><?php echo t('You must provide the following information before you may login.'); ?></p>
					<?php 
					$attribs = UserAttributeKey::getRegistrationList();
					$af = Loader::helper('form/attribute');
					$i = 0;
					foreach($unfilledAttributes as $ak) { 
						if ($i > 0) { 
						}
						echo $af->display($ak, $ak->isAttributeKeyRequiredOnRegister());	
						$i++;
					}
					echo $form->hidden('uName', Loader::helper('text')->entities($_POST['uName']));
					echo $form->hidden('uPassword', Loader::helper('text')->entities($_POST['uPassword']));
					echo $form->hidden('uOpenID', $uOpenID);
					echo $form->hidden('completePartialProfile', true);
					?>
				</fieldset>
			</div>
		</div>
		<div class="row">
			<div class="span10 offset1">
				<div class="actions">
					<?php echo $form->submit('submit', t('Sign In'), array('class' => 'primary')); ?>
					<?php echo $form->hidden('rcID', $rcID); ?>
				</div>
			</div>
		</div>
	</form>

<?php } else { ?>
	<form method="post" action="<?php echo $this->url('/login', 'do_login'); ?>" class="form-horizontal ccm-login-form">
		<div class="row">
			<div class="span5 offset1">
				<fieldset>
					<legend><?php echo t('User Account'); ?></legend>
					<div class="control-group">
						<label for="uName" class="control-label"><?php
							if (USER_REGISTRATION_WITH_EMAIL_ADDRESS == true) {
								echo t('Email Address');
							} else {
								echo t('Username');
							}
						?></label>
						<div class="controls">
							<input type="text" name="uName" id="uName" <?php echo isset($uName) ? ('value="'.$uName.'"') : ''; ?> class="ccm-input-text" autofocus>
						</div>
					</div>
					<div class="control-group">
						<label for="uPassword" class="control-label"><?php echo t('Password'); ?></label>
						<div class="controls">
							<input type="password" name="uPassword" id="uPassword" class="ccm-input-text" />
						</div>
					</div>
				</fieldset>
				<?php if (OpenIDAuth::isEnabled()) { ?>
					<fieldset>
						<legend><?php echo t('OpenID'); ?></legend>
						<div class="control-group">
							<label for="uOpenID" class="control-label"><?php echo t('Login with OpenID'); ?>:</label>
							<div class="controls">
								<input type="text" name="uOpenID" id="uOpenID" <?php echo isset($uOpenID) ? ('value="'.$uOpenID.'"') : ''; ?> class="ccm-input-openid">
							</div>
						</div>
					</fieldset>
				<?php } ?>
			</div>
			<div class="span4 offset1">
				<fieldset>
					<legend><?php echo t('Options'); ?></legend>
					<?php if (isset($locales) && is_array($locales) && count($locales) > 0) { ?>
						<div class="control-group">
							<label for="USER_LOCALE" class="control-label"><?php echo t('Language'); ?></label>
							<div class="controls"><?php echo $form->select('USER_LOCALE', $locales); ?></div>
						</div>
					<?php } ?>
					<div class="control-group">
						<label class="checkbox"><?php echo $form->checkbox('uMaintainLogin', 1); ?> <span><?php echo t('Remain logged in to website.'); ?></span></label>
					</div>
					<?php $rcID = isset($_REQUEST['rcID']) ? Loader::helper('text')->entities($_REQUEST['rcID']) : $rcID; ?>
					<input type="hidden" name="rcID" value="<?php echo $rcID; ?>" />
				</fieldset>
			</div>
		</div>
		<div class="row">
			<div class="span10 offset1">
				<div class="actions">
					<?php echo $form->submit('submit', t('Sign In') . ' &gt;', array('class' => 'primary')); ?>
				</div>
			</div>
		</div>
	</form>

	<a name="forgot_password"></a>
	<form method="post" action="<?php echo $this->url('/login', 'forgot_password'); ?>" class="form-horizontal ccm-forgot-password-form">
		<div class="row">
			<div class="span10 offset1">
				<fieldset>
					<legend><?php echo t('Forgot Your Password?'); ?></legend>
					<p><?php echo t("Enter your email address below. We will send you instructions to reset your password."); ?></p>
					<input type="hidden" name="rcID" value="<?php echo $rcID; ?>" />
					<div class="control-group">
						<label for="uEmail" class="control-label"><?php echo t('Email Address'); ?></label>
						<div class="controls">
							<input type="text" name="uEmail" value="" class="ccm-input-text" >
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<div class="row">
			<div class="span10 offset1">
				<div class="actions">
					<?php echo $form->submit('submit', t('Reset and Email Password') . ' &gt;'); ?>
				</div>
			</div>
		</div>
	</form>

	<?php if (ENABLE_REGISTRATION == 1) { ?>
		<div class="row">
			<div class="span10 offset1">
				<div class="control-group">
					<fieldset>
						<legend><?php echo t('Not a Member'); ?></legend>
						<p><?php echo t('Create a user account for use on this website.'); ?></p>
					</fieldset>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span10 offset1">
				<div class="actions">
					<a class="btn" href="<?php echo $this->url('/register'); ?>"><?php echo t('Register here!'); ?></a>
				</div>
			</div>
		</div>
	<?php } ?>

<?php }
