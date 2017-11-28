<?php
/**
 * @package Helpers
 * @category Concrete
 * @subpackage Forms
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * Special form elements for date and time items. These can include calendars and time fields automatically.
 * @package Helpers
 * @category Concrete
 * @subpackage Forms
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Form_DateTime {

	/** 
	 * Takes a "field" and grabs all the corresponding disparate fields from $_POST and translates into a timestamp
	 * @param string $field The name of the field to translate
	 * @param array $arr = null The array containing the value. If null (default) we'll use $_POST
	 * @return string|false $dateTime In case of success returns the timestamp (in the format 'Y-m-d H:i:s' or 'Y-m-d'), otherwise returns false ($field value is not in the array) or '' (if $field value is empty).
	 * If $field has both date and time, the resulting value will be converted fro user timezone to system timezone.
	 * If $field has only date and not time, no timezone conversion will occur. 
	 */
	public function translate($field, $arr = null) {
		if ($arr == null) {
			$arr = $_POST;
		}
		/* @var $dateHelper DateHelper */
		$dateHelper = Loader::helper('date');
		if (isset($arr[$field . '_dt'])) {
			$value = $arr[$field . '_dt'];
			if (strlen(trim($value)) === 0) {
				return '';
			}
			$format = defined('CUSTOM_DATE_APP_GENERIC_MDY') ? CUSTOM_DATE_APP_GENERIC_MDY : t(/*i18n: Short date format: see http://www.php.net/manual/en/function.date.php */ 'n/j/Y');
			$h = is_numeric($arr[$field . '_h']) ? $arr[$field . '_h'] : '00';
			$m = is_numeric($arr[$field . '_m']) ? $arr[$field . '_m'] : '00';
			if(isset($arr[$field . '_a'])) {
				if ($arr[$field . '_a'] === 'AM') {
					$a = $dateHelper->date('A', mktime(1));
				} else {
					$a = $dateHelper->date('A', mktime(13));
				}
				$value .= " $h:$m $a";
				$format .= ' h:i A';
			}
			else {
				$value .= " $h:$m";
				$format .= ' H:i';
			}
			$d = new Zend_Date();
			$d->setTimezone($dateHelper->getTimezone('user'));
			$d->set($value, $format, Localization::activeLocale());
			return $dateHelper->formatCustom('Y-m-d H:i:s', $d, 'system');
		}
		elseif (isset($arr[$field])) {
			$value = $arr[$field];
			if (strlen(trim($value)) === 0) {
				return '';
			}
			$format = defined('CUSTOM_DATE_APP_GENERIC_MDY') ? CUSTOM_DATE_APP_GENERIC_MDY : t(/*i18n: Short date format: see http://www.php.net/manual/en/function.date.php */ 'n/j/Y');
			$d = new Zend_Date();
			$d->setTimezone($dateHelper->getTimezone('system'));
			$d->set($value, $format, Localization::activeLocale());
			return $dateHelper->formatCustom('Y-m-d', $d, 'system');
		}
		else {
			return false;
		}
	}

	/** 
	 * Creates form fields and JavaScript calendar includes for a particular item
	 * <code>
	 *     $dateHelper->datetime('yourStartDate', '2008-07-12 3:00:00');
	 * </code>
	 * @param string $prefix
	 * @param string $value
	 * @param bool $includeActivation
	 * @param bool $calendarAutoStart
	 */
	public function datetime($prefix, $value = null, $includeActivation = false, $calendarAutoStart = true) {
		if (substr($prefix, -1) == ']') {
			$prefix = substr($prefix, 0, strlen($prefix) -1);
			$_activate = $prefix . '_activate]';
			$_dt = $prefix . '_dt]';
			$_h = $prefix . '_h]';
			$_m = $prefix . '_m]';
			$_a = $prefix . '_a]';
		} else {
			$_activate = $prefix . '_activate';
			$_dt = $prefix . '_dt';
			$_h = $prefix . '_h';
			$_m = $prefix . '_m';
			$_a = $prefix . '_a';
		}
		
		$dfh = (DATE_FORM_HELPER_FORMAT_HOUR == '12') ? 'h' : 'H';
		$dfhe = (DATE_FORM_HELPER_FORMAT_HOUR == '12') ? '12' : '23';
		$dfhs = (DATE_FORM_HELPER_FORMAT_HOUR == '12') ? '1' : '0';
		$dateHelper = Loader::helper('date'); /* @var $dateHelper DateHelper */
		$zendDate = $dateHelper->toZendDate($value);
		if(is_null($zendDate)) {
			$zendDate = $dateHelper->toZendDate('now');
		}
		$dt = $dateHelper->formatDate($zendDate, false, 'user');
		$h = $dateHelper->formatCustom($dfh, $zendDate, 'user');
		$m = $dateHelper->formatCustom('i', $zendDate, 'user');
		$a = $dateHelper->formatCustom('A', $zendDate, 'user');
		$id = preg_replace("/[^0-9A-Za-z-]/", "_", $prefix);
		$html = '';
		$disabled = false;
		if ($includeActivation) {
			if ($value) {
				$activated = 'checked';
			} else {
				$disabled = 'disabled';
			}
			
			$html .= '<input type="checkbox" id="' . $id . '_activate" class="ccm-activate-date-time" ccm-date-time-id="' . $id . '" name="' . $_activate . '" ' . $activated . ' />';
		}
		$html .= '<span class="ccm-input-date-wrapper" id="' . $id . '_dw"><input id="' . $id . '_dt" name="' . $_dt . '" class="ccm-input-date" value="' . $dt . '" ' . $disabled . ' /></span>';
		$html .= '<span class="ccm-input-time-wrapper" id="' . $id . '_tw">';
		$html .= '<select id="' . $id . '_h" name="' . $_h . '" ' . $disabled . '>';
		for ($i = $dfhs; $i <= $dfhe; $i++) {
			if ($h == $i) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$html .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
		}
		$html .= '</select>:';
		$html .= '<select id="' . $id . '_m" name="' . $_m . '" ' . $disabled . '>';
		for ($i = 0; $i <= 59; $i++) {
			if ($m == sprintf('%02d', $i)) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$html .= '<option value="' . sprintf('%02d', $i) . '" ' . $selected . '>' . sprintf('%02d', $i) . '</option>';
		}
		$html .= '</select>';
		$dateHelper = Loader::helper('date');
		/* @var $dateHelper DateHelper */
		if (DATE_FORM_HELPER_FORMAT_HOUR == '12') {
			$html .= '<select id="' . $id . '_a" name="' . $_a . '" ' . $disabled . '>';
			// Get the translation of "AM" in the current language
			$strAM = $dateHelper->date('A', mktime(1));
			$html .= '<option value="AM" ';
			if ($a == $strAM) {
				$html .= 'selected';
			}
			$html .= '>';
			$html .= $strAM;
			$html .= '</option>';
			// Get the translation of "PM" in the current language
			$strPM = $dateHelper->date('A', mktime(13));
			$html .= '<option value="PM" ';
			if ($a == $strPM) {
				$html .= 'selected';
			}
			$html .= '>';
			$html .= $strPM;
			$html .= '</option>';
			$html .= '</select>';
		}
		$html .= '</span>';
		if ($calendarAutoStart) { 
			$html .= '<script type="text/javascript">$(function() { $("#' . $id . '_dt").datepicker({ dateFormat: ' . Loader::helper('json')->encode($dateHelper->getJQueryUIDatePickerFormat()) . ', changeYear: true, showAnim: \'fadeIn\' }); });</script>';
		}
		// first we add a calendar input
		
		if ($includeActivation) {
			$html .=<<<EOS
			<script type="text/javascript">$("#{$id}_activate").click(function() {
				if ($(this).get(0).checked) {
					$("#{$id}_dw input").each(function() {
						$(this).get(0).disabled = false;
					});
					$("#{$id}_tw select").each(function() {
						$(this).get(0).disabled = false;
					});
				} else {
					$("#{$id}_dw input").each(function() {
						$(this).get(0).disabled = true;
					});
					$("#{$id}_tw select").each(function() {
						$(this).get(0).disabled = true;
					});
				}
			});
			</script>
EOS;
			
		}
		return $html;
	
	}
	
	/** 
	 * Creates form fields and JavaScript calendar includes for a particular item but includes only calendar controls (no time.)
	 * <code>
	 *     $dateHelper->date('yourStartDate', '2008-07-12 3:00:00');
	 * </code>
	 * @param string $prefix
	 * @param string $value
	 * @param bool $includeActivation
	 * @param bool $calendarAutoStart
	 */
	public function date($field, $value = null, $calendarAutoStart = true) {
		$dateHelper = Loader::helper('date');
		/* @var $dateHelper DateHelper */
		$id = preg_replace("/[^0-9A-Za-z-]/", "_", $field);
		if (isset($_REQUEST[$field])) {
			$dt = $_REQUEST[$field];
		}
		elseif ($value != "") {
			$dt = $dateHelper->formatDate($value, false, 'system');
		}
		else {
			$dt = '';
		}
		//$id = preg_replace("/[^0-9A-Za-z-]/", "_", $prefix);
		$html = '';
		$html .= '<span class="ccm-input-date-wrapper" id="' . $id . '_dw"><input id="' . $id . '" name="' . $field . '" class="ccm-input-date" value="' . $dt . '"  /></span>';

		if ($calendarAutoStart) { 
			$html .= '<script type="text/javascript">$(function() { $("#' . $id . '").datepicker({ dateFormat: ' . Loader::helper('json')->encode($dateHelper->getJQueryUIDatePickerFormat()) . ', changeYear: true, showAnim: \'fadeIn\' }); });</script>';
		}
		return $html;
	
	}	

}
