<?
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * An object representing a private message sent to a user
 *
 * @package Users
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2009 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */

	class UserPrivateMessage extends Object {

		protected $authorName = false;
		protected $mailbox;

		public function getMessageDelimiter() {
			return t('-------------------- Original Message --------------------');
		}
		/**
		* Gets a message given the message id
		* @param int $msgID
		* @param bool $mailbox
		* @return UserPrivateMessage object $upm
		*/
		public static function getByID($msgID, $mailbox = false) {
			$db = Loader::db();
			$row = $db->GetRow('select uAuthorID, msgDateCreated, msgID, msgSubject, msgBody, uToID from UserPrivateMessages where msgID = ?', array($msgID));
			if (!isset($row['msgID'])) {
				return false;
			}

			$upm = new UserPrivateMessage();
			$upm->setPropertiesFromArray($row);

			if ($mailbox) {
				// we add in some mailbox-specific attributes
				$row = $db->GetRow('select msgID, msgIsNew, msgIsUnread, msgMailboxID, msgIsReplied, uID from UserPrivateMessagesTo where msgID = ? and uID = ?', array($msgID, $mailbox->getMailboxUserID()));
				if (isset($row['msgID'])) {
					$upm->setPropertiesFromArray($row);	
				}
				$upm->mailbox = $mailbox;
			}

			return $upm;
		}
		/**
		* Gets the messages status- whether its read or not, replied to, sent, etc.
		* @return string
		*/
		public function getMessageStatus() {
			if (is_object($this->mailbox)) {
				if (!$this->msgIsUnread) {
					return t('Read');
				}
				if ($this->mailbox->getMailboxID() == UserPrivateMessageMailbox::MBTYPE_SENT) {
					return t("Sent");
				}
			}

			if ($this->msgIsNew) {
				return t('New');
			}
			if ($this->msgIsUnread) {
				return t('Unread');
			}
			if ($this->msgIsReplied) {
				return t('Replied');
			}

			return t("Read");		
		}
		/**
		* Marks the current message as read
		*/
		public function markAsRead() {
			if (!$this->uID) {
				return false;
			}

			$db = Loader::db();
			if ($this->uID != $this->uAuthorID) {
				Events::fire('on_private_message_marked_as_read', $this);
				$db->Execute('update UserPrivateMessagesTo set msgIsUnread = 0 where msgID = ?', array($this->msgID, $this->msgMailboxID, $this->uID));
			}
		}

		public function getMessageAuthorID() {return $this->uAuthorID;}
		public function getMessageID() {return $this->msgID;}
		public function getMessageUserID() {return $this->uID;}
		public function getMessageAuthorObject() {return UserInfo::getByID($this->uAuthorID);}
		public function getMessageUserToID() {return $this->uToID;}
		public function getMessageRelevantUserID() {
			if (is_object($this->mailbox)) {
				if ($this->mailbox->getMailboxID() == UserPrivateMessageMailbox::MBTYPE_SENT) {
					return $this->uToID;
				}
			}

			return $this->uAuthorID;
		}

		/** 
		 * Responsible for converting line breaks to br tags, perhaps running bbcode, as well as making the older replied-to messages gray
		 * @return string $msgBody
		 */		
		public function getFormattedMessageBody() {
			$msgBody = $this->getMessageBody();
			$txt = Loader::helper('text');

			$repliedPos = strpos($msgBody, $this->getMessageDelimiter());
			if ($repliedPos > -1) {
				$repliedText = substr($msgBody, $repliedPos);
				$messageText = substr($msgBody, 0, $repliedPos);
				$msgBody = $messageText . '<div class="ccm-profile-message-replied">' . nl2br($txt->entities($repliedText)) . '</div>';
				$msgBody = str_replace($this->getMessageDelimiter(), '<hr />', $msgBody);
			} else {
			    $msgBody = nl2br($txt->entities($msgBody));		
			}

			return $msgBody;
		}

		/**
		* Deletes the current message from a users inbox
		*/	
		public function delete() {
			$db = Loader::db();
			if (!$this->uID) {
				return false;
			}
			$ret = Events::fire('on_private_message_delete', $this);
			if($ret < 0) {
				return;
			}
			$db->Execute('delete from UserPrivateMessagesTo where uID = ? and msgID = ?', array($this->uID, $this->msgID));
		}
		/**
		* Gets the user object that was sent a message
		* @return UserInfo object $ui
		*/
		public function getMessageRelevantUserObject() {
			$ui = UserInfo::getByID($this->getMessageRelevantUserID());
			return $ui;
		}
		/**
		* Gets the user name that was sent a message or author name if it isn't sent
		* @return string username
		*/
		public function getMessageRelevantUserName() {
			$ui = UserInfo::getByID($this->getMessageRelevantUserID());
			if (is_object($ui)) {
				return $ui->getUserName();
			}
		}
		/**
		* Gets the messages Author Name
		* @return string author name 
		*/
		public function getMessageAuthorName() {
			if ($this->authorName == false) {
				$author = $this->getMessageAuthorObject();
				if (is_object($author)) { 
					$this->authorName = $author->getUserName();
				} else {
					$this->authorName = t('Unknown User');
				}
			}

			return $this->authorName;
		}
		/**
		* Get when the message was made
		* @param string $type is how it was made
		* returns date in the format of $mask
		*/
		public function getMessageDateAdded($type = 'system', $mask = false) {
			if($type == 'user') {
				$dh = Loader::helper('date');
				return $dh->getLocalDateTime($this->msgDateCreated, $mask);
			} else {
				return $this->msgDateCreated;
			}
		}
		public function getMessageSubject() {return $this->msgSubject;}
		public function getFormattedMessageSubject() {
			$txt = Loader::helper('text');
			return $txt->entities($this->msgSubject);
		}
		public function getMessageBody() {return $this->msgBody;}
	}

	class UserPrivateMessageMailbox extends Object {

		const MBTYPE_INBOX = -1;
		const MBTYPE_SENT = -2;

		public function getMailboxID() {return $this->msgMailboxID;}
		public function getMailboxUserID() {return $this->uID;}
		/** 
		* Gets information about a users mailbox
		* @param user object $uer
		* @param int $msgMailboxID
		* @return object $mb
		*/
		public static function get($user, $msgMailboxID) {
			$db = Loader::db();
			$mb = new UserPrivateMessageMailbox();
			$mb->msgMailboxID = $msgMailboxID;
			$mb->uID = $user->getUserID();
			$mb->totalMessages = $db->GetOne("select count(msgID) from UserPrivateMessagesTo where msgMailboxID = ? and uID = ?", array($msgMailboxID, $user->getUserID()));
			$mb->lastMessageID = $db->GetOne("select UserPrivateMessages.msgID from UserPrivateMessages inner join UserPrivateMessagesTo on UserPrivateMessages.msgID = UserPrivateMessagesTo.msgID where msgMailboxID = ? and UserPrivateMessagesTo.uID = ? order by msgDateCreated desc", array($msgMailboxID, $user->getUserID()));

			return $mb;
		}
		/**
		* Sets all messages for a user to not be new
		*/
		public function removeNewStatus() {
			$db = Loader::db();
			$user = UserInfo::getByID($this->uID);
			Events::fire('on_private_message_marked_not_new', $this);
			$db->Execute('update UserPrivateMessagesTo set msgIsNew = 0 where msgMailboxID = ? and uID = ?', array($this->msgMailboxID, $user->getUserID()));
		}

		public function getTotalMessages() {return $this->totalMessages;}
		public function getLastMessageID() {return $this->lastMessageID;}
		public function getLastMessageObject() {
			if ($this->lastMessageID > 0) {
				return UserPrivateMessage::getByID($this->lastMessageID, $this);
			}
		}

		public function getMessageList() {
			$pml = new UserPrivateMessageList();
			$pml->filterByMailbox($this);
			return $pml;
		}

	}

	class UserPrivateMessageList extends DatabaseItemList {

		protected $itemsPerPage = 10;
		protected $mailbox;

		public function filterByMailbox($mailbox) {
			$this->filter('msgMailboxID', $mailbox->getMailboxID());
			$this->filter('uID', $mailbox->getMailboxUserID());
			$this->mailbox = $mailbox;
		}

		function __construct() {
			$this->setQuery("select UserPrivateMessagesTo.msgID from UserPrivateMessagesTo inner join UserPrivateMessages on UserPrivateMessagesTo.msgID = UserPrivateMessages.msgID");
			$this->sortBy('msgDateCreated', 'desc');
		}

		public function get($itemsToGet = 0, $offset = 0) {
			$r = parent::get($itemsToGet, $offset);
			foreach($r as $row) {
				$messages[] = UserPrivateMessage::getByID($row['msgID'], $this->mailbox);
			}
			return $messages;
		}

	}


	class UserPrivateMessageLimit {
		/**
		 * checks to see if a user has exceeded their limit for sending private messages
		 * @param int $uID
		 * @return boolean
		*/
		public function isOverLimit($uID){
			if(USER_PRIVATE_MESSAGE_MAX == 0) { return false; }
			if(USER_PRIVATE_MESSAGE_MAX_TIME_SPAN == 0) { return false; }
			$db = Loader::db();
			$dt = new DateTime();
			$dt->modify('-'.USER_PRIVATE_MESSAGE_MAX_TIME_SPAN.' minutes');
			$v = array($uID, $dt->format('Y-m-d H:i:s'));
			$q = "SELECT COUNT(msgID) as sent_count FROM UserPrivateMessages WHERE uAuthorID = ? AND msgDateCreated >= ?";
			$count = $db->getOne($q,$v);

			if($count > USER_PRIVATE_MESSAGE_MAX) {
				self::notifyAdmin($uID);
				return true;
			} else {
				return false;
			}
		}
		/**
		* Alters the user if isOverLimit occurs
		* @return string $ve
		*/
		public function getErrorObject() {
			$ve = Loader::helper('validation/error');
			$ve->add(t('You may not send more than %s messages in %s minutes', USER_PRIVATE_MESSAGE_MAX, USER_PRIVATE_MESSAGE_MAX_TIME_SPAN));
			return $ve;
		}
		/**
		* Alters the admin if isOverLimit occurs
		* @param int $offenderID
		*/
		protected function notifyAdmin($offenderID) {
			$offender = UserInfo::getByID($offenderID);
			Events::fire('on_private_message_over_limit', $offender);
			$admin = UserInfo::getByID(USER_SUPER_ID);

			Log::addEntry(t("User: %s has tried to send more than %s private messages within %s minutes", $offender->getUserName(), USER_PRIVATE_MESSAGE_MAX, USER_PRIVATE_MESSAGE_MAX_TIME_SPAN),t('warning'));

			Loader::helper('mail');
			$mh = new MailHelper();

			$mh->addParameter('offenderUname', $offender->getUserName());
			$mh->addParameter('profileURL', BASE_URL . View::url('/profile', 'view', $offender->getUserID()));
			$mh->addParameter('profilePreferencesURL', BASE_URL . View::url('/profile/edit'));

			$mh->to($admin->getUserEmail());
			$mh->load('private_message_admin_warning');
			$mh->sendMail();
		}
	}