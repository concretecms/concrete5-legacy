<?
/**
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Functions useful functions for working with dates.
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Date {

	/** 
	 * Gets the date time for the local time zone/area if user timezones are enabled, if not returns system datetime
	 * @param string $systemDateTime
	 * @param string $format
	 * @return string $datetime
	 */
	public function getLocalDateTime($systemDateTime = 'now', $mask = NULL) {
		if(!isset($mask) || !strlen($mask)) {
			$mask = 'Y-m-d H:i:s';
		}
		
		$req = Request::get();
		if ($req->hasCustomRequestUser()) {
			return date($mask, strtotime($req->getCustomRequestDateTime()));
		}
		
		if(!isset($systemDateTime) || !strlen($systemDateTime)) {
			return NULL; // if passed a null value, pass it back
		} elseif(strlen($systemDateTime)) {
			$datetime = new DateTime($systemDateTime);
		} else {
			$datetime = new DateTime();
		}
		
		if(defined('ENABLE_USER_TIMEZONES') && ENABLE_USER_TIMEZONES) {
			$u = new User();
			if($u && $u->isRegistered()) {
				$utz = $u->getUserTimezone();
				if($utz) {
					$tz = new DateTimeZone($utz);
					$datetime->setTimezone($tz);
				}
			}
		}
		if (Localization::activeLocale() != 'en_US') {
			return $this->dateTimeFormatLocal($datetime,$mask);
		} else {
			return $datetime->format($mask);
		}
	}

	/** 
	 * Converts a user entered datetime to the system datetime
	 * @param string $userDateTime
	 * @param string $systemDateTime
	 * @return string $datetime
	 */
	public function getSystemDateTime($userDateTime = 'now', $mask = NULL) {
		if(!isset($mask) || !strlen($mask)) {
			$mask = 'Y-m-d H:i:s';
		}

		$req = Request::get();
		if ($req->hasCustomRequestUser()) {
			return date($mask, strtotime($req->getCustomRequestDateTime()));
		}
		
		if(!isset($userDateTime) || !strlen($userDateTime)) {
			return NULL; // if passed a null value, pass it back
		} elseif(strlen($userDateTime)) {
			$datetime = new DateTime($userDateTime);
			
			if (defined('APP_TIMEZONE')) {
				$tz = new DateTimeZone(APP_TIMEZONE_SERVER);
				$datetime = new DateTime($userDateTime,$tz); // create the in the user's timezone 				
				$stz = new DateTimeZone(date_default_timezone_get()); // grab the default timezone
				$datetime->setTimeZone($stz); // convert the datetime object to the current timezone
			}
			
			if(defined('ENABLE_USER_TIMEZONES') && ENABLE_USER_TIMEZONES) {
				$u = new User();
				if($u && $u->isRegistered()) {
					$utz = $u->getUserTimezone();
					if($utz) {			
						$tz = new DateTimeZone($utz);
						$datetime = new DateTime($userDateTime,$tz); // create the in the user's timezone 
						
						$stz = new DateTimeZone(date_default_timezone_get()); // grab the default timezone
						$datetime->setTimeZone($stz); // convert the datetime object to the current timezone
					} 
				}
			}
		} else {
			$datetime = new DateTime();
		}
		if (Localization::activeLocale() != 'en_US') {
			return $this->dateTimeFormatLocal($datetime,$mask);
		} else {
			return $datetime->format($mask);
		}
	}
	/**
	 * Gets the localized date according to a specific mask
	 * @param object $datetime A PHP DateTime Object
	 * @param string $mask 
	 * @return string 
	 */
	public function dateTimeFormatLocal(&$datetime,$mask) {
		$locale = new Zend_Locale(Localization::activeLocale());

		$date = new Zend_Date($datetime->format(DATE_ATOM),DATE_ATOM, $locale);
		$date->setTimeZone($datetime->format("e"));
		return $date->toString($mask);
	}
	
	/** 
	 * Subsitute for the native date() function that adds localized date support
	 * This uses Zend's Date Object {@link http://framework.zend.com/manual/en/zend.date.constants.html#zend.date.constants.phpformats}
	 * @param string $mask
	 * @param int $timestamp
	 * @return string
	 */
	public function date($mask,$timestamp=false) {
		$loc = Localization::getInstance();
		if ($timestamp === false) {
			$timestamp = time();
		}
		
		if ($loc->getLocale() == 'en_US') {
			return date($mask, $timestamp);			
		}		

		$locale = new Zend_Locale(Localization::activeLocale());
		Zend_Date::setOptions(array('format_type' => 'php'));
		$cache = Cache::getLibrary();
		if (is_object($cache)) {
			Zend_Date::setOptions(array('cache'=>$cache));
		} 
		$date = new Zend_Date($timestamp, false, $locale);

		return $date->toString($mask);
	}

	/**
	 * returns a keyed array of timezone identifiers
	 * see: http://www.php.net/datetimezone.listidentifiers.php
	 * @return array:
	 */
	public function getTimezones() {
		return array_combine(DateTimeZone::listIdentifiers(),DateTimeZone::listIdentifiers());
	}

	/**
	 * Calculate the elapsed time since a given point in time
	 * e.g. "3 hours, 43 minutes"
	 * based on http://www.php.net/manual/de/dateinterval.format.php#96768
	 *
	 * @param int $posttime unix timestamp
	 * @param int $precise if 1 then return the two biggest parts like minutes and seconds
	 * @return string elapsed time as a string
	 */
	public function timeSince($posttime,$precise=0){
		$now = new DateTime;
		$then = new DateTime("@".$posttime);
		
		if ($now < $then) {
			return $this->date(DATE_APP_GENERIC_MDY,$posttime);
		}
		$interval = $now->diff($then);
		
		$format = array();
		if($interval->y !== 0) {
			$format[] = t2("%s year", '%s years', $interval->y, $interval->y);
		}
		if($interval->m !== 0) {
			$format[] = t2("%s month", '%s months', $interval->m, $interval->m);
		}
		if($interval->d !== 0) {
			$format[] = t2("%s day", '%s days', $interval->d, $interval->d);
		}
		if($interval->h !== 0) {
			$format[] = t2("%s hour", '%s hours', $interval->h, $interval->h);
		}
		if($interval->i !== 0) {
			$format[] = t2("%s minute", '%s minutes', $interval->i, $interval->i);
		}
		$format[] = t2("%s second", '%s seconds', $interval->s, $interval->s);
		
		$result = implode(", ",array_slice($format,0,$precise + 1));
		
		return $result;
		
	}
}