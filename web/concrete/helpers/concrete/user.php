<?
/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * @access private
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class ConcreteUserHelper {

	function getOnlineNow($uo, $showSpacer = true) {
		$ul = 0;
		if (is_object($uo)) {
			// user object
			$ul = $uo->getLastOnline();
		} else if (is_numeric($uo)) {
			$db = Loader::db();
			$ul = $db->getOne("select uLastOnline from Users where uID = {$uo}");
		}

		$online = (time() - $ul) <= ONLINE_NOW_TIMEOUT;			
		
		if ($online) {
			
			return ONLINE_NOW_SRC_ON;
		} else {
			if ($showSpacer) {
				return ONLINE_NOW_SRC_OFF;
			}
			
		}
	}
	
public function validNewPassword( $password, $errorObj=NULL){
			
		$vs = Loader::helper('validation/strings');
  		if ((strlen($password) < USER_PASSWORD_MINIMUM) || (strlen($password) > USER_PASSWORD_MAXIMUM)) {
			if($errorObj) 
				$errorObj->add( t('A password must be between %s and %s characters', USER_PASSWORD_MINIMUM, USER_PASSWORD_MAXIMUM) );
			$invalid=1;
		}
        
        $numberCount=$vs->containsNumber($password);
        $capitalLetterCount=$vs->containsUpperCase($password);
        $specialCharCount=$vs->containsSymbol($password);
        $string_error_array_parts=array();
        if($numberCount<USER_PASSWORD_NUMBER_MINIMUM)
        {
            $string_error_array_parts[]= t(' %s number ',USER_PASSWORD_NUMBER_MINIMUM);
        }
        if($capitalLetterCount<USER_PASSWORD_CAPITAL_LETTER_MINIMUM)
        {
            $string_error_array_parts[]= t(' %s capital letter ',USER_PASSWORD_CAPITAL_LETTER_MINIMUM);
        }
        if($specialCharCount<USER_PASSWORD_SPECIAL_CHAR_MINIMUM)
        {
            $string_error_array_parts[]= t(' %s special characters ',USER_PASSWORD_SPECIAL_CHAR_MINIMUM);
        }
        $error_string=t('Password must contain at least ');
        //add errors
 		if(sizeof($string_error_array_parts)>0&&sizeof($string_error_array_parts)==1)$error_string.=$string_error_array_parts[0];
        elseif(sizeof($string_error_array_parts)==2)
        {
            $error_string.=implode(t(" and "),$string_error_array_parts);
        }
        else //>3
        {
            $error_string.=implode(" , ",array_slice($string_error_array_parts,0,sizeof($string_error_array_parts)-1)).t(" and ").end($string_error_array_parts);
        }
        if(sizeof($string_error_array_parts)>0)
        {
            if($errorObj)
            {
                $errorObj->add($error_string);
            }
            $invalid=1;
        }

		if($invalid) return false;
		
		return true;
	}
}
