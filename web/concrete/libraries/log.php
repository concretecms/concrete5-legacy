<?php defined('C5_EXECUTE') or die("Access Denied.");

/**
 * An object that represents a log entry.
 * @package Utilities
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
class LogEntry extends Object {

	/**
	 * @access private
	 */
	private $logType;
	private $logText;
	private $logID;
	private $timestamp;
	private $logIsInternal;
	
	/**
	 * Get the current log type (eg "exceptions")
	 * @return string
	 */
	public function getType() {
		return $this->logType;
	}
	
	/**
	 * Get the current log's text
	 * @return string
	 */
	public function getText() {
		return $this->logText;
	}
	
	/**
	 * Get the current log's ID
	 * @return int
	 */
	public function getID() {
		return $this->logID;
	}
	
	/**
	 * Get the timestamp of the log returning either the system's time or the user's time
	 * @param string $type system or user
	 * @return string
	 */
	public function getTimestamp($type = 'system') {
		if(ENABLE_USER_TIMEZONES && $type == 'user') {
			$dh = Loader::helper('date');
			$timestamp = $dh->getLocalDateTime($this->timestamp);
		} else {
			$timestamp = $this->timestamp;
		}
		return $timestamp;
	}

	/** 
	 * Returns a log entry by ID
	 * @param int $logID
	 * @return LogEntry|void
	 */
	public static function getByID($logID) {
		$db = Loader::db();
		$r = $db->Execute("select * from Logs where logID = ?", array($logID));
		if ($r) {
			$row = $r->FetchRow();
			$obj = new LogEntry();
			$obj->setPropertiesFromArray($row);
			return $obj;
		}
	}

}


/**
 * A library for dealing with searchable logs.
 * @package Utilities
 * @author Andrew Embler <andrew@concrete5.org>
 * @category Concrete
 * @copyright  Copyright (c) 2003-2012 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 *
 */
 
class Log {

	/**
	 * @access private
	 */
	private $log;
	private $logfile;
	private $name;
	private $session = false;
	private $sessionText = null;
	private $isInternal = false;
	
	/**
	 * Setup a multi-line log
	 * @param string $log Name of the log
	 * @param bool $session If this is a multi-line log (true by default)
	 * @param bool $internal Is this an internal log
	 * @return void
	 */
	public function __construct($log = null, $session = true, $internal = false) {
		$th = Loader::helper('text');
		if ($log == null) {
			$log = '';
		}
		$this->log = $log;
		$this->name = $th->unhandle($log);
		$this->session = $session;
		$this->isInternal = $internal;
	}
	
	/**
	 * Add text to a multi-line log
	 * @param string $message Log text
	 * @return void
	 */
	public function write($message) {
		$this->sessionText .= $message . "\n";
		if (!$this->session) {
			$this->close();
		}
	}

	/**
	 * Add a single line log entry
	 * @param string $message Log text
	 * @param string $namespace log namespace (eg debug or exception)
	 * @return void
	 */
	public static function addEntry($message, $namespace = false) {
		if (!$namespace) {
			$namespace = t('debug');
		}
		$l = new Log($namespace, false);
		$l->write($message);
	}
	
	/** 
	 * Removes all "custom" log entries - these are entries that an app owner has written and don't have a builtin C5 type
	 * @return void
	 */
	public function clearCustom() {
		$db = Loader::db();
		$db->Execute("delete from Logs where logIsInternal = 0");
	}

	/** 
	 * Removes log entries by type- these are entries that an app owner has written and don't have a builtin C5 type
	 * @param string $type Is a lowercase string that uses underscores instead of spaces, e.g. sent_emails or exceptions
	 * @return void
	 */
	public function clearByType($type) {
		$db = Loader::db();
		$db->Execute("delete from Logs where logType = ?", array($type));
	}
	
	/**
	 * Clear Internal logs
	 * @return void
	 */
	public function clearInternal() {
		$db = Loader::db();
		$db->Execute("delete from Logs where logIsInternal = 1");
	}
	
	/** 
	 * Removes all log entries
	 * @return void
	 */
	public function clearAll() {
		$db = Loader::db();
		$db->Execute("delete from Logs");
	}

	/**
	 * Close the multi-line log and insert it in to the database
	 * @return void
	 */
	public function close() {
		$v = array($this->log, htmlentities($this->sessionText, ENT_COMPAT, APP_CHARSET), $this->isInternal);
		$db = Loader::db();
		$db->Execute("insert into Logs (logType, logText, logIsInternal) values (?, ?, ?)", $v);
		$this->sessionText = '';
	}
	
	/** 
	 * @deprecated
	 */
	public function archive() { }
	
	/** 
	 * Returns the total number of entries matching this type 
	 * @param string $keywords
	 * @param string $type
	 * @return int $r
	 */
	public static function getTotal($keywords, $type) {
		$db = Loader::db();
		$kw = '';
		if ($keywords != '') {
			$kw = 'and logText like ' . $db->quote('%' . $keywords . '%');
		}
		if ($type != false) {
			$v = array($type);
			$r = $db->GetOne('select count(logID)  from Logs where logType = ? ' . $kw, $v);
		} else {
			$r = $db->GetOne('select count(logID)  from Logs where 1=1 ' . $kw);
		}
		return $r;
	}
	
	/** 
	 * Returns a list of log entries
	 * @param string $keywords
	 * @param string $type
	 * @param int $limit
	 * @return array $entries
	 */
	public static function getList($keywords, $type, $limit = 10) {
		$db = Loader::db();
		$kw = '';
		if ($keywords != '') {
			$kw = 'and logText like ' . $db->quote('%' . $keywords . '%');
		}
		if ($type != false) {
			$v = array($type);
			$r = $db->Execute('select logID from Logs where logType = ? ' . $kw . ' order by timestamp desc limit ' . $limit, $v);
		} else {
			$r = $db->Execute('select logID from Logs where 1=1 ' . $kw . ' order by timestamp desc limit ' . $limit);
		}
		
		$entries = array();
		while ($row = $r->FetchRow()) {
			$entries[] = LogEntry::getByID($row['logID']);
		}
		return $entries;
	}
	
	/** 
	 * Returns an array of distinct log types
	 * @return array $lt
	 */
	public static function getTypeList() {
		$db = Loader::db();
		$lt = $db->GetCol("select distinct logType from Logs order by logType asc");
		if (!is_array($lt)) {
			$lt = array();
		}
		return $lt;
	}
	
	/**
	 * Get the log name
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/** 
	 * Alias of getTypeList()
	 * @see Log::getTypeList()
	 */
	public static function getLogs() {
		return self::getTypeList();
	}

}