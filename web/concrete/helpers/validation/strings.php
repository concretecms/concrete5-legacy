<? defined('C5_EXECUTE') or die("Access Denied.");

class ValidationStringsHelper extends Concrete5_Helper_Validation_Strings {

	public function multiLingualName($field, $allow_spaces = false) {
		if($allow_spaces) {
			return !preg_match("/[<>;'`_ ]+$/u", $field);
		} else {
			return !preg_match("/[<>;'`]+$/u", $field);
		}
	}

}