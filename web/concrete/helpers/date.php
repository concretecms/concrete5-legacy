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
					$timeRemaining=$days.' ';
					if($days!=1) {
						$timeRemaining.=t('days');
					} else {
						$timeRemaining.=t('day');
					}
					if($precise==1) $timeRemaining.=', '.$hours.' '.t('hours');
				} else if ($diff>3600) {
					$timeRemaining=$hours.' ';
					if($hours!=1) {
						$timeRemaining.=t('hours');
					} else {
						$timeRemaining.=t('hour');
					}
					if($precise==1) $timeRemaining.=', '.date("i",$diff).' '.t('minutes');
				}else if ($diff>60){
					$minutes=date("i",$diff);
					if(substr($minutes,0,1)=='0') $minutes=substr($minutes,1);
					$timeRemaining=$minutes.' ';
					if($minutes!=1) {
						$timeRemaining.=t('minutes');
					} else {
						$timeRemaining.=t('minute');
					}
					if($precise==1) $timeRemaining.=', '.date("s",$diff).' '.t('seconds');
				}else{
					$seconds=date("s",$diff);
					if(substr($seconds,0,1)=='0') $seconds=substr($seconds,1);
					$timeRemaining=$seconds.' ';
					if($seconds!=1) {
						$timeRemaining.=t('seconds');
					} else {
						$timeRemaining.=t('second');
					}
				}
		}
		return $timeRemaining;
	}//end timeSince
	
	/**
	 * A substitute for the native php strftime() function
	 * that uses the C5 translation function t() for month names,
	 * day names, am/pm name and preferred date formats
	 * It also re-implements tokens that are unsupported or
	 * incompatible on Windows 
	 * In addition to the strftime tokens, it also defines "%O" for
	 * adding a ordinal suffix (st,nd,rd,th) 
	 * @author Patrick Heck <patrick@patrickheck.de>
	 * @todo implement %g and %G
	 * @param string $format uses the same format as strftime, see: http://www.php.net/manual/de/function.strftime.php
	 * @param int $ts unix timestamp
	 * @param bool $recursive if true no deeper recursion will happen
	 * @return string
	 */
	public function strftime($format,$ts=NULL,$recursive=false) {
		if (!$ts) {
			$ts = time();
		}
		setlocale(LC_ALL, LOCALE);
		/* tokens that get replaced by localized name */
		$mapping = array(
			/* A full textual representation of the day */
			'%A' => $this->intToDayname(date('w', $ts)),
			/* An abbreviated textual representation of the day */
			'%a' => $this->intToDayname(date('w', $ts), true),
			/* Full month name, based on the locale */
			'%B' => $this->intToMonthname(date('n', $ts)), 
			/* Abbreviated month name, based on the locale */
			'%b' => $this->intToMonthname(date('n', $ts), true),
			/* lower-case 'am' or 'pm' based on the given time */
			'%P' => $this->amPmToName(date('a', $ts)),
			/* UPPER-CASE 'AM' or 'PM' based on the given time */
			'%p' => strtoupper($this->amPmToName(date('a', $ts))),
			/* Ordinal sufix like "st", "nd", "rd", "th"
			   inspired by http://de2.php.net/manual/en/function.strftime.php#104894
			*/ 
			'%O' => $this->ordinalSuffixToName(date('S', $ts))
		);   
		/* tokens that get replaced by a pattern */
		/* recursive calls can't use patterns to prevent endless loops */
		if (!$recursive) {
			$mapping = array_merge($mapping, array(
				/* Same as "%I:%M:%S %p" */
				'%r' => $this->strftime('%I:%M:%S %p',$ts,true),
				/* Preferred date representation based on locale, without the time */
				'%x' => $this->strftime(t('%m/%d/%Y'),$ts,true),
				/* Preferred time representation based on locale, without the date */
				'%X' => $this->strftime(t('%H:%M'),$ts,true)
			)); 
		}
		/* tokens that are missing/incompatible on win32 machines 
		 * see: http://de2.php.net/manual/en/function.strftime.php#53340 */
		$mapping = array_merge($mapping, array(
			/* Two digit representation of the century (year divided by 100, truncated to an integer) */
			'%C' => sprintf("%02d", date("Y", $ts) / 100),
			/* Same as "%m/%d/%y" */
			'%D' => '%m/%d/%y',
			/* Day of the month, with a space preceding single digits. */
			'%e' => sprintf("%' 2d", date("j", $ts)),
			/* Same as "%Y-%m-%d" (commonly used in database datestamps) */
			'%F' => '%Y-%m-%d',
			/* Abbreviated month name, based on the locale (an alias of %b) */
			'%h' => '%b',
			/* Hour in 12-hour format, with a space preceeding single digits */
			'%l' => sprintf("%' 2d", date("g", $ts)),
			/* A newline character ("\n") */
			'%n' => "\n",
			/* Same as "%H:%M" */
			'%R' => date("H:i", $ts),
			/* Unix Epoch Time timestamp (same as the time() function) */
			'%s' => $ts, 
			/* A Tab character ("\t") */
			'%t' => "\t",
			/* Same as "%H:%M:%S" */
			'%T' => '%H:%M:%S',
			/* ISO-8601 numeric representation of the day of the week */
			'%u' => ($w = date("w", $ts)) ? $w : 7,
			/* ISO-8601:1988 week number of the given year, starting with the first week of the
			 * year with at least 4 weekdays, with Monday being the start of the week */
			'%V' => $this->weekIsonumber($ts),
			/* The time zone abbreviation  */
			'%z' => date('T',$ts),
			/* The time zone offset  */
			'%Z' => date('O',$ts)
		));
		$format = str_replace(
			array_keys($mapping),
			array_values($mapping),
			$format
		);
		return strftime($format, $ts);		
	}

	/**
	 * Get the localized month name from a number
	 * @author Patrick Heck <patrick@patrickheck.de>
	 *
	 * @param int $month January = 1, December = 12
	 * @param bool $short get short form of month
	 * @return string name of month according to current locale
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
	 * @return string name of day according to current locale
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
	 * @return string localized name of "am" or "pm"
	 */
	public function amPmToName($ampm) {
		if ($ampm == "am") {
			return t("am");
		} else {
			return t("pm");
		}
	}
	
	/**
	 * Get the localized name of an ordinal suffix
	 * @author Patrick Heck <patrick@patrickheck.de>
	 *
	 * @param string $ordinalSuffix "st", "nd", "rd" or "th"
	 * @return string localized name of an ordinal suffix
	 */
	public function ordinalSuffixToName($ordinalSuffix) {
		switch ($ordinalSuffix) {
			case "st":
				return t("st");
			case "nd":
				return t("nd");
			case "rd":
				return t("rd");
			default:
				return t("th");
		}
	}
	
	/** 
	 * When strftime("%V") fails, some unoptimized workaround
	 * http://en.wikipedia.org/wiki/ISO_8601 : week 1 is "the week with the year's first Thursday in it (the formal ISO definition)"
	 * @link http://de2.php.net/manual/en/function.strftime.php#100385
	 * 
	 * @param int $time unix timestamp
	 * @returns string week of the year according to ISO-8601:1988
	 */
	function weekIsonumber ($time) {	
		$year = strftime("%Y", $time);
	
		$first_day = strftime("%w", mktime(0, 0, 0, 1, 1, $year));
		$last_day = strftime("%w", mktime(0, 0, 0, 12, 31, $year));
		   
		$number = $isonumber = strftime("%W", $time);
	
		// According to strftime("%W"), 1st of january is in week 1 if and only if it is a monday
		if ($first_day == 1)
			$isonumber--;
	
		// 1st of january is between monday and thursday; starting (now) at 0 when it should be 1
		if ($first_day >= 1 && $first_day <= 4)
			$isonumber++;
		else if ($number == 0)
			$isonumber = $this->weekIsonumber(mktime(0, 0, 0, 12, 31, $year - 1));
	
		if ($isonumber == 53 && ($last_day == 1 || $last_day == 2 || $last_day == 3))
			$isonumber = 1;
	
		return sprintf("%02d", $isonumber);
	}

}

?>