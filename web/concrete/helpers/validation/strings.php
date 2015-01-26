<? defined('C5_EXECUTE') or die("Access Denied.");

class ValidationStringsHelper extends Concrete5_Helper_Validation_Strings {
/*
	E38080 　
	E38181 ぁ
	E381BF み
	E38280 む
	E38293 ん
	[E4-E9][80-BF][80-BF] 漢字
	E382A1 ァ
	E382BF タ
	E38380 ダ
	E383BC ー
	EFBC86 ＆ (FULLWIDTH AMPERSAND)
	EFBC90 ０
	EFBC99 ９
	EFBCA1 Ａ
	EFBCBA Ｚ
	EFBD81 ａ
	EFBD9A ｚ
*/

	public function multiLingualName($field, $allow_spaces = false, $allow_dashes = false) {
		if ($allow_spaces && $allow_dashes) {
			return preg_match("/^(?:[A-Za-z0-9 \-]|\xE3\x80\x80"
			."|\xE3\x81[\x81-\xBF]|\xE3\x82[\x80-\x93]|[\xE4-\xE9][\x80-\xBF][\x80-\xBF]"
			."|\xE3\x82[\xA1-\xBF]|\xE3\x83[\x80-\xBC]|\xEF\xBC\x86"
			."|\xEF\xBC[\xA1-\xBA\xA1-\xBA]|\xEF\xBD[\x81-\x9A])+$/", $field);
		} else if ($allow_spaces) {
			return preg_match("/^(?:[A-Za-z0-9 ]|\xE3\x80\x80"
			."|\xE3\x81[\x81-\xBF]|\xE3\x82[\x80-\x93]|[\xE4-\xE9][\x80-\xBF][\x80-\xBF]"
			."|\xE3\x82[\xA1-\xBF]|\xE3\x83[\x80-\xBC]|\xEF\xBC\x86"
			."|\xEF\xBC[\xA1-\xBA\xA1-\xBA]|\xEF\xBD[\x81-\x9A])+$/", $field);
		} else if ($allow_dashes) {
			return preg_match("/^(?:[A-Za-z0-9\-]"
			."|\xE3\x81[\x81-\xBF]|\xE3\x82[\x80-\x93]|[\xE4-\xE9][\x80-\xBF][\x80-\xBF]"
			."|\xE3\x82[\xA1-\xBF]|\xE3\x83[\x80-\xBC]|\xEF\xBC\x86"
			."|\xEF\xBC[\xA1-\xBA\xA1-\xBA]|\xEF\xBD[\x81-\x9A])+$/", $field);
		} else {
			return preg_match("/^(?:[A-Za-z0-9]"
			."|\xE3\x81[\x81-\xBF]|\xE3\x82[\x80-\x93]|[\xE4-\xE9][\x80-\xBF][\x80-\xBF]"
			."|\xE3\x82[\xA1-\xBF]|\xE3\x83[\x80-\xBC]|\xEF\xBC\x86"
			."|\xEF\xBC[\xA1-\xBA\xA1-\xBA]|\xEF\xBD[\x81-\x9A])+$/", $field);
		}
	}

}