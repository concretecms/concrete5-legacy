<?php
defined('C5_EXECUTE') or die("Access Denied.");

class Concrete5_Controller_Profile_Friends extends Controller {

	public $helpers = array('html', 'form');

	/** @type ValidationErrorHelper */
	public $error;

	public function on_start(){
		$this->error = Loader::helper('validation/error');
		$this->addHeaderItem(Loader::helper('html')->css('ccm.profile.css'));
	}

	public function view($userID = 0) {
		if(!ENABLE_USER_PROFILES) {
			$this->render("/page_not_found");
		}

		$html = Loader::helper('html');
		$canEdit = false;
		$u = new User();

		if ($userID > 0) {
			$profile = UserInfo::getByID($userID);
			if (!is_object($profile)) {
				throw new Exception('Invalid User ID.');
			}
		} else if ($u->isRegistered()) {
			$profile = UserInfo::getByID($u->getUserID());
			$canEdit = true;
		} else {
			$this->set('intro_msg', t('You must sign in order to access this page!'));
			$this->render('/login');
		}

		$this->set('profile', $profile);
		$this->set('av', Loader::helper('concrete/avatar'));
		$this->set('t', Loader::helper('text'));
		$this->set('canEdit',$canEdit);
	}

	public function add_friend($fuID=0, $token=""){
		/** @type ValidationTokenHelper $token_helper */
		$token_helper = Loader::helper('validation/token');
		$user_id = intval($fuID);
		if ($token_helper->validate("profile.add_friend.{$user_id}", $token)) {
			$user = User::getByUserID($user_id);

			if (!$user->isError()) {
				UsersFriends::addFriend($user->getUserID());
			} else {
				$this->error->add(t('Invalid User ID'));
			}
		} else {
			$this->error->add('Invalid Token.');
		}

		if ($this->error->has()) {
			$this->set('error', $this->error);
		}
		$this->view();
	}

	public function remove_friend($fuID=0, $token=""){
		/** @type ValidationTokenHelper $token_helper */
		$token_helper = Loader::helper('validation/token');
		$user_id = intval($fuID);
		if ($token_helper->validate("profile.remove_friend.{$user_id}", $token)) {
			$user = User::getByUserID($user_id);

			if (!$user->isError()) {
				UsersFriends::removeFriend($user->getUserID());
			} else {
				$this->error->add(t('Invalid User ID'));
			}
		} else {
			$this->error->add('Invalid Token.');
		}

		if ($this->error->has()) {
			$this->set('error', $this->error);
		}
		$this->view();
	}

}
