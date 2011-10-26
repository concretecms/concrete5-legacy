<?

class SessionCheck {

	protected $usID = null;
	protected $uID = -1;
	protected $ipAddress = null;
	protected $userAgent = null;
	protected $c5session = null;

	protected $uName = null;

	private $_db;
	private $_limit;

	public function __construct($uID = false) {
		$this->_db = Loader::db();

		if($uID && $uID != USER_SUPER_ID) $this->setUserID($uID);
	}


	public function setUserID($uID, $uName) {
		$this->uID = $uID;
		$this->usID = isset($_SESSION['usID']) ? $_SESSION['usID'] : -1;
		$this->ipAddress = $this->getRealAddress();
		$this->userAgent = $this->getBrowser();
		$this->uName = $uName;

		$this->_limit = CONCURRENT_SESSION_LIMIT;

		if($this->uID > 0) {
			/* allow over-ride of number of sessions on a per user basis */
			$ui = UserInfo::getByID($this->uID);
			$limit = $ui->getAttribute('allowed_sessions');
			if(!empty($limit)) $this->_limit = $limit;
		}
	}

	private function clearExpiredSessions() {
		$life = defined('CONCURRENT_SESSION_LIFE') ? CONCURRENT_SESSION_LIFE : 8;
		$this->_db->query("delete from UserSessions where lastSeen < date_sub(now(), interval {$life} hour)");
	}

	private function getSessions() {
		$this->clearExpiredSessions();

		$sessions = array();
		$r = $this->_db->query("select * from UserSessions where uID = ?", $this->uID);
		while($row = $r->fetchRow()) { $sessions[] = $row; }
		return $sessions;
	}

	public function allowSession() {
		if ($this->uID == USER_SUPER_ID) return true;  /* We never want to limit the SuperUser */
		if ($this->_limit == -1) return true;  /* Numbers smaller than 0 mean unlimited sessions */

		$this->c5session = session_id();
		$sessions = $this->getSessions();
		$authorised = false;
		$counter = 0;

		foreach($sessions as $s) {
			$points = 0;
			if($s['ipAddress'] == $this->ipAddress) $points++;
			if($s['userAgent'] == $this->userAgent) $points++; 
			if($s['c5session'] == $this->c5session) $points++;
			if($s['usID']      == $this->usID)      $points++;
	
			if($points >= 3) { 
				$authorised = true; $this->usID = $_SESSION['usID'] = $s['usID']; 
			} else { 
				$counter++; 
			}
		}

		if($authorised == false && $counter >= $this->_limit) { 
			$this->_log = new Log('session_limits', true);
			$this->_log->write(t('Session Limit of %s exceeded for User ID#%s %s', $this->_limit, $this->uID, $this->uName));
			$this->_log->write(t('From %s on %s (%s)', $this->ipAddress, $this->userAgent, $this->c5session));
			$this->_log->close();
			return false; 
		}
		return true;
	}

	public function createSession() {
		if ($uID == USER_SUPER_ID) return true;  /* We never want to limit the SuperUser */
	
		$this->c5session = session_id();

		if ($this->usID && $this->usID > 0) {
			$r = $this->_db->Execute("update UserSessions set ipAddress = ?, userAgent = ?, c5session = ?, sessionStart = NOW(), lastSeen = NOW() where usID = ?",
										array($this->ipAddress, $this->userAgent, $this->c5session, $this->usID));
		} else {
			$r = $this->_db->Execute("insert into UserSessions (uID, ipAddress, userAgent, c5session, sessionstart, lastSeen) VALUES (?,?,?,?,NOW(),NOW())",
										array($this->uID, $this->ipAddress, $this->userAgent, $this->c5session));
			if($r) {
				$this->usID = $_SESSION['usID'] = $this->_db->Insert_ID();
			}
		}
	}

	public function updateLastSeen() {
		if ($this->uID == USER_SUPER_ID) return true;  /* We never want to limit the SuperUser */
		$this->_db->query("update UserSessions SET lastSeen = NOW() where usID = ?", $this->usID);
	}

	public function updateSessionID($newSessionID = false) {
		if ($this->uID == USER_SUPER_ID) return true;  /* We never want to limit the SuperUser */

		if(!$newSessionID) { session_name(SESSION); $newSessionID = session_id(); }
		$this->_db->query("update UserSessions SET c5session = ? WHERE usID = ?", array($newSessionID, $this->usID));
		$this->c5session = $newSessionID;
	}

	public function deleteSession() {
		if ($this->uID == USER_SUPER_ID) return true;  /* We never want to limit the SuperUser */
		$this->_db->query("delete from UserSessions WHERE usID = ?", $this->usID);
		$this->usID = -1;
	}


	public static function getRealAddress() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) { return $_SERVER['HTTP_CLIENT_IP']; }
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { return $_SERVER['HTTP_X_FORWARDED_FOR']; }
		return $_SERVER['REMOTE_ADDR']; 
	}

	public static function getBrowser() { 
		$u_agent = $_SERVER['HTTP_USER_AGENT']; 
		$bname = 'Unknown'; $platform = 'Unknown'; $version= "";

		if (preg_match('/linux/i', $u_agent)) { $platform = 'linux'; }
		elseif (preg_match('/macintosh|mac os x/i', $u_agent)) { $platform = 'mac'; }
		elseif (preg_match('/windows|win32/i', $u_agent)) { $platform = 'windows'; }
    
		if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) { $bname = 'Internet Explorer'; $ub = "MSIE"; } 
		elseif(preg_match('/Firefox/i',$u_agent)) { $bname = 'Mozilla Firefox'; $ub = "Firefox"; } 
		elseif(preg_match('/Chrome/i',$u_agent)) { $bname = 'Google Chrome'; $ub = "Chrome"; } 
		elseif(preg_match('/Safari/i',$u_agent)) { $bname = 'Apple Safari'; $ub = "Safari"; } 
		elseif(preg_match('/Opera/i',$u_agent)) { $bname = 'Opera'; $ub = "Opera"; } 
		elseif(preg_match('/Netscape/i',$u_agent)) { $bname = 'Netscape'; $ub = "Netscape"; } 
    
		$known = array('Version', $ub, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .  ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) { }

		$i = count($matches['browser']);
		if ($i != 1) {
			if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){ $version= $matches['version'][0]; }
			else { $version= $matches['version'][1]; }
		} else {
			$version= $matches['version'][0];
		}
    
		if ($version==null || $version=="") {$version="?";}
   		return sprintf("%s(%s)-%s", $bname,$version,$platform); 
	} 

}

