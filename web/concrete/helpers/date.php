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

// Load a compatiblity class for pre php 5.2 installs
Loader::library('datetime_compat');

class DateHelper {

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
		return $datetime->format($mask);
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
		return $datetime->format($mask);
	}

	/**
	 * returns a keyed array of timezone identifiers
	 * see: http://www.php.net/datetimezone.listidentifiers.php
	 * @return array:
	 */
	public function getTimezones() {
		return array_combine(DateTimeZone::listIdentifiers(),DateTimeZone::listIdentifiers());
	}

	
	public function timeSince($posttime,$precise=0){
		$timeRemaining=0;
		$diff=date("U")-$posttime;
		$days=intval($diff/(24*60*60));
		$hoursInSecs=$diff-($days*(24*60*60));
		$hours=intval($hoursInSecs/(60*60));
		if ($hours<=0) $hours=$hours+24;           
		if ($posttime>date("U")) return date(DATE_APP_GENERIC_MDY,$posttime);
		else{
			if ($diff>86400){
					$diff=$diff+86400;
					$days=date("z",$diff);
					$timeRemaining=$days.' '.t('day');
					if($days!=1) $timeRemaining.=t('s');
					if($precise==1) $timeRemaining.=', '.$hours.' '.t('hours');
				} else if ($diff>3600) {
					$timeRemaining=$hours.' '.t('hour');
					if($hours!=1) $timeRemaining.=t('s');
					if($precise==1) $timeRemaining.=', '.date("i",$diff).' '.t('minutes');
				}else if ($diff>60){
					$minutes=date("i",$diff);
					if(substr($minutes,0,1)=='0') $minutes=substr($minutes,1);
					$timeRemaining=$minutes.' '.t('minute');
					if($minutes!=1) $timeRemaining.=t('s');
					if($precise==1) $timeRemaining.=', '.date("s",$diff).' '.t('seconds');
				}else{
					$seconds=date("s",$diff);
					if(substr($seconds,0,1)=='0') $seconds=substr($seconds,1);
					$timeRemaining=$seconds.' '.t('second');
					if($seconds!=1) $timeRemaining.=t('s');
				}
		}
		return $timeRemaining;
	}//end timeSince
	
	/**
	 * A substitution for the native php strftime() function
	 * that uses the C5 translation function t() for month names,
	 * day names, am/pm name and preferred date formats
	 * @author Patrick Heck <patrick@patrickheck.de>
	 *
	 * @param string $format uses the same format as strftime, see: http://www.php.net/manual/de/function.strftime.php
	 * @param int $timestamp 
	 * @param bool $recursive if true no deeper recursion will happen
	 * @return string
	 */
	public function strftime($format,$timestamp=NULL,$recursive=false) {
		if (!$timestamp) {
			$timestamp = time();
		}
		setlocale(LC_ALL, LOCALE);
		/* tokens that get replaced by localized name */
		/* A full textual representation of the day */
		$format = str_replace('%A', $this->intToDayname(date('w', $timestamp)),$format);
		/* An abbreviated textual representation of the day */
		$format = str_replace('%a', $this->intToDayname(date('w', $timestamp), true),$format);
		/* Full month name, based on the locale */
		$format = str_replace('%B', $this->intToMonthname(date('n', $timestamp)),$format); 
		/* Abbreviated month name, based on the locale */
		$format = str_replace('%b', $this->intToMonthname(date('n', $timestamp), true),$format);
		/* lower-case 'am' or 'pm' based on the given time */
		$format = str_replace('%P', $this->amPmToName(date('a', $timestamp)),$format);
		/* UPPER-CASE 'AM' or 'PM' based on the given time */
		$format = str_replace('%p', strtoupper($this->amPmToName(date('a', $timestamp))),$format);
		/* tokens that get replaced by a pattern */
		/* recursive calls can't use patterns to prevent endless loops */
		if (!$recursive) {
			/* Same as "%I:%M:%S %p" */
			$format = str_replace('%r', $this->strftime('%I:%M:%S %p',$timestamp,true),$format);
			/* Preferred date representation based on locale, without the time */
			$format = str_replace('%x', $this->strftime(t('%m/%d/%Y'),$timestamp,true),$format);
			/* Preferred time representation based on locale, without the date */
			$format = str_replace('%X', $this->strftime(t('%H:%M'),$timestamp,true),$format); 
		}
		return strftime($format, $timestamp);		
	}

	/**
	 * Get the localized month name from a number
	 * @author Patrick Heck <patrick@patrickheck.de>
	 *
	 * @param int $month January = 1, December = 12
	 * @param bool $short get short form of month
	 * @return string
	 */
	public function intToMonthname($month,$short=false) {
		if ($short) {
			$format = "short";
		} else {
			$format = "long";
		}
		$monthNames = array( "long" => array(t("January"),t("February"),t("March"),t("April"),t("May"),t("June"),
										 t("July"),t("August"),t("September"),t("October"),t("November"),t("December")),
						 	 "short" => array(t("Jan"),t("Feb"),t("Mar"),t("Apr"),t("May"),t("Jun"),t("Jul"),t("Aug"),
										 t("Sep"),t("Oct"),t("Nov"),t("Dec")));
		$idx = intval($month,10)-1;
		if (isset($monthNames[$format][$idx])) {
			return $monthNames[$format][$idx];
		} else {
			return "";
		}
	}
	
	/**
	 * Get the localized day name from a number
	 * @author Patrick Heck <patrick@patrickheck.de>
	 * 
	 * @param int $day Sunday = 0, Saturday = 6
	 * @param bool $short get short form of day
	 * @return string
	 */
	public function intToDayname($day,$short=false) {
		if ($short) {
			$format = "short";
		} else {
			$format = "long";
		}
		$dayNames = array("long" => array(t("Sunday"),t("Monday"),t("Tuesday"),t("Wednesday"),t("Thursday"),
										t("Friday"),t("Saturday")),
						  "short" => array(t("Su"),t("Mo"),t("Tu"),t("We"),t("Th"),
										t("Fr"),t("Sa")));
		$idx = intval($day,10);
		if (isset($dayNames[$format][$idx])) {
			return $dayNames[$format][$idx];
		} else {
			return "";
		}
	}
	
	/**
	 * Get the localized name of "am" or "pm"
	 * @author Patrick Heck <patrick@patrickheck.de>
	 *
	 * @param string $ampm "am" or "pm"
     * @return string
	 */
	public function amPmToName($ampm) {
		if ($ampm == "am") {
			return t("am");
		} else {
			return t("pm");
		}
	}

}

?>