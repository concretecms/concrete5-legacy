<?php
class DateHelperTest extends PHPUnit_Framework_TestCase {
	/**
	* @var DateHelper
	*/
	protected $object;
	/**
	* Sets up the fixture, for example, opens a network connection.
	* This method is called before a test is executed.
	*/
	protected function setUp() {
		$this->object = Loader::helper('date');
	}
	/**
	* Tears down the fixture, for example, closes a network connection.
	* This method is called after a test is executed.
	*/
	protected function tearDown() {
	}

	public function testTimeSince() {
		Localization::changeLocale('en_US');
		$minutes = 60;
		$hours = $minutes * 60;
		$days = $hours * 24;
		// time is in the future
		$future = time() + 7;
		$this->assertEquals(
			$this->object->formatDate($future, false),
			$this->object->timeSince($future)
		);
		// time is now
		$this->assertEquals(
			'0 seconds',
			$this->object->timeSince(time())
		);
		// time is in the past (less that one year)
		$this->assertEquals(
			'7 seconds',
			$this->object->timeSince(time() - 7)
		);
		$this->assertEquals(
			'3 minutes',
			$this->object->timeSince(time() - (3 * $minutes + 13))
		);
		$this->assertEquals(
			'3 minutes, 13 seconds',
			$this->object->timeSince(time() - (3 * $minutes + 13), true)
		);
		$this->assertEquals(
			'4 hours',
			$this->object->timeSince(time() - (4 * $hours + 2 * $minutes))
		);
		$this->assertEquals(
			'4 hours, 1 minute',
			$this->object->timeSince(time() - (4 * $hours + 1 * $minutes), true)
		);
		$this->assertEquals(
			'1 day',
			$this->object->timeSince(time() - (1 * $days + 1 * $minutes))
		);
		$this->assertEquals(
			'2 days, 2 hours',
			$this->object->timeSince(time() - (2 * $days + 2 * $hours), true)
		);
		$this->assertEquals(
			'145 days',
			$this->object->timeSince(time() - (145 * $days))
		);
		// time is in the past (more that one year)
		$this->assertEquals(
			$this->object->formatDate(time() - (367 * $days), false),
			$this->object->timeSince(time() - (367 * $days))
		);
	}
	public function testTimeZones() {
		$timestamp = 1234560000; // 2009-02-13 21:20:00 UTC
		$activeUser = User::isLoggedIn() ? new User() : null;
		$u = User::getByUserID(TESTUSER_JP_ID, true);
		Localization::changeLocale('en_US');
		$this->assertEquals(
			'Asia/Tokyo: February 14, 2009 at 6:20:00 AM',
			'Asia/Tokyo: ' . $this->object->formatDateTime($timestamp, true, true)
		);
		$u = User::getByUserID(TESTUSER_IT_ID, true);
		Localization::changeLocale('en_US');
		$this->assertEquals(
			'Europe/Rome: February 13, 2009 at 10:20:00 PM',
			'Europe/Rome: ' . $this->object->formatDateTime($timestamp, true, true)
		);
		Localization::changeLocale('en_US');
		if($activeUser) {
			User::getByUserID($activeUser->getUserID(), true);
		}
		else {
			$u->logout();
		}
	}
	public function testTranslations() {
		$timestamp = 1234560000; // 2009-02-13 21:20:00 UTC
		$activeUser = User::isLoggedIn() ? new User() : null;
		$u = User::getByUserID(TESTUSER_IT_ID, true);
		Localization::changeLocale('en_US');
		$this->assertEquals(
			'February 13, 2009 at 10:20:00 PM',
			$this->object->formatDateTime($timestamp, true, true)
		);
		Localization::changeLocale('it_IT');
		$this->assertEquals(
			'13 febbraio 2009 alle 22:20:00',
			$this->object->formatDateTime($timestamp, true, true)
		);
		Localization::changeLocale('en_US');
		if($activeUser) {
			User::getByUserID($activeUser->getUserID(), true);
		}
		else {
			$u->logout();
		}
	}
	public function testFormatDates() {
		Localization::changeLocale('en_US');
		$activeUser = User::isLoggedIn() ? new User() : null;
		if($activeUser) {
			$activeUser->logout();
		}
		$timestamp = time();
		$this->assertEquals(
			$this->object->date(DATE_APP_GENERIC_MDY, $timestamp),
			$this->object->formatDate($timestamp, false)
		);
		$this->assertEquals(
			$this->object->date(DATE_APP_GENERIC_MDY_FULL, $timestamp),
			$this->object->formatDate($timestamp, true)
		);
		$this->assertEquals(
			$this->object->date(DATE_APP_GENERIC_T, $timestamp),
			$this->object->formatTime($timestamp, false)
		);
		$this->assertEquals(
			$this->object->date(DATE_APP_GENERIC_TS, $timestamp),
			$this->object->formatTime($timestamp, true)
		);
		$this->assertEquals(
			$this->object->date(DATE_APP_GENERIC_MDYT, $timestamp),
			$this->object->formatDateTime($timestamp, false, false)
		);
		/*
		$this->assertEquals(
			$this->object->date(, $timestamp),
			$this->object->formatDateTime($timestamp, false, true)
		);
		*/
		$this->assertEquals(
			$this->object->date(DATE_APP_GENERIC_MDYT_FULL, $timestamp),
			$this->object->formatDateTime($timestamp, true, false)
		);
		$this->assertEquals(
			$this->object->date(DATE_APP_GENERIC_MDYT_FULL_SECONDS, $timestamp),
			$this->object->formatDateTime($timestamp, true, true)
		);
		if($activeUser) {
			User::getByUserID($activeUser->getUserID(), true);
		}
	}
	public function testSpecialFormats() {
		Localization::changeLocale('en_US');
		$activeUser = User::isLoggedIn() ? new User() : null;
		if($activeUser) {
			$activeUser->logout();
		}
		$timestamp = time();
		foreach(array(
			'FILENAME',
			'FILE_PROPERTIES',
			'FILE_VERSIONS',
			'FILE_DOWNLOAD',
			'PAGE_VERSIONS',
			'DASHBOARD_SEARCH_RESULTS_USERS',
			'DASHBOARD_SEARCH_RESULTS_FILES',
			'DASHBOARD_SEARCH_RESULTS_PAGES',
			'DATE_ATTRIBUTE_TYPE_MDY',
			'DATE_ATTRIBUTE_TYPE_T'
		) as $formatName) {
			$this->assertEquals(
				$this->object->date(constant("DATE_APP_$formatName"), $timestamp),
				$this->object->formatSpecial($formatName, $timestamp)
			);
		}
		if($activeUser) {
			User::getByUserID($activeUser->getUserID(), true);
		}
	}
}
