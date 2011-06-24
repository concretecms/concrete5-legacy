<?

/**
 * Text helper
 * 
 * Functions useful for working with text.
 * 
 * Used as follows:
 * <code>
 * $txt = Loader::helper('text');
 * $string = 'This is some random text.';
 * $linked = $txt->shortText($string, 15);
 * echo $linked;
 * </code>
 *     
 * Which will then output: 
 * <code>
 * This is some ra...
 * </code>      
 *   
 * @package Helpers
 * @category Concrete
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

defined('C5_EXECUTE') or die("Access Denied.");
class TextHelper { 
	
	/** 
	 * Takes text and returns it in the "lowercase_and_underscored_with_no_punctuation" format
	 * @param string $handle
	 * @param bool $leaveSlashes
	 * @return string
	 */
	function sanitizeFileSystem($handle, $leaveSlashes=false) {
		$handle = trim($handle);
		$handle = str_replace(PAGE_PATH_SEPARATOR, '-', $handle);
		$searchMulti = array(
			"ä",
			"ö",
			"ß",
			"ü",
			"æ",
			"ø",
			"å",
			"é",
			"è"	
		);

		$replaceMulti = array(
			'ae',
			'oe',
			'ss',
			'ue',
			'ae',
			'oe',
			'aa',
			'e',
			'e'
		);
		
		$handle = str_replace($searchMulti, $replaceMulti, $handle);

		$searchNormal = array("/[&]/", "/[\s]+/", "/[^0-9A-Za-z-_.]/", "/-+/");
		$searchSlashes = array("/[&]/", "/[\s]+/", "/[^0-9A-Za-z-_.\/]/", "/-+/");
		$replace = array("and", "-", "", "-");
		
		$search = $searchNormal;
		if ($leaveSlashes) {
			$search = $searchSlashes;
		}

		$handle = preg_replace($search, $replace, $handle);
		if (function_exists('mb_substr')) {
			$handle = mb_strtolower($handle, APP_CHARSET);
		} else {
			$handle = strtolower($handle);
		}
		$handle = trim($handle, '-');
		$handle = str_replace('-', PAGE_PATH_SEPARATOR, $handle);
		return $handle;
	}

	/** 
	 * Strips tags and optionally reduces string to specified length.
	 * @param string $string String to sanitize
	 * @param int $maxlength First x characters to keep
	 * @return string
	 */
	function sanitize($string, $maxlength = 0) {
		$text = trim(strip_tags($string));
		if ($maxlength > 0) {
			if (function_exists('mb_substr')) {
				$text = mb_substr($text, 0, $maxlength, APP_CHARSET);
			} else {
				$text = substr($text, 0, $maxlength);
			}
		}
		if ($text == null) {
			return ""; // we need to explicitly return a string otherwise some DB functions might insert this as a ZERO.
		}
		return $text;
	}

	/**
	 * always use in place of htmlentites(), so it works with different langugages
	 * @param string $v String to use htmlentities on
	 * @return string
	**/
	public function entities($v){
		return htmlentities( $v, ENT_COMPAT, APP_CHARSET); 
	}
	 
	 
	/**
	 * An alias for shorten()
	 * @param string $textStr
	 * @param int $numChars
	 * @param string $tail
	 * @return string
	 */
	public function shorten($textStr, $numChars = 255, $tail = '...') {
		return $this->shortText($textStr, $numChars, $tail);
	}
	
	/** 
	 * Like sanitize, but requiring a certain number characters, and assuming a tail
	 * @param string $textStr String to shorten
	 * @param int $numChars Number of characters until the string its trimmed
	 * @param string $tail Text to put after the shortend text (default '...')
	 * @return string
	 */	
	function shortText($textStr, $numChars=255, $tail='...') {
		if (intval($numChars)==0) $numChars=255;
		$textStr=strip_tags($textStr);
		if (function_exists('mb_substr')) {
			if (mb_strlen($textStr, APP_CHARSET) > $numChars) { 
				$textStr = mb_substr($textStr, 0, $numChars, APP_CHARSET) . $tail;
			}
		} else {
			if (strlen($textStr) > $numChars) { 
				$textStr = substr($textStr, 0, $numChars) . $tail;
			}
		}
		return $textStr;			
	}
	
	
	/**
	 * Takes a string and turns it into the CamelCase or StudlyCaps version
	 * @param string $string
	 * @return string
	 */
	public function camelcase($string) {
		return Object::camelcase($string);
	}
	
	/** 
	 * Scans passed text and automatically hyperlinks any URL inside it
	 * @param string $input Text to parse
	 * @param bool $newWindow Open link in a new window
	 * @param string $title Title attribute
	 * @param string $rel Rel attribute
	 * @return string $output
	 */
	public function autolink($input, $newWindow=0, $title='', $rel='') {
		$target=($newWindow)?' target="_blank"':'';
		$ctitle=($title)?' title="'.$this->entities($title).'"':'';
		$crel=($rel)?' rel="'.$this->entities($rel).'"':'';
		$output = preg_replace("/(http:\/\/|https:\/\/|(www\.))(([^\s<]{4,80})[^\s<]*)/", '<a href="http://$2$3" '.$target.$ctitle.$crel.'>http://$2$4</a>', $input);
		return ($output);
	}
	
	/** 
	 * automatically add hyperlinks to any twitter style @usernames in a string
	 * @param string $input Text to parse
	 * @param bool $newWindow Open link in a new window
	 * @param bool $withSearch Instead of a link to the profile, search with the profile.
	 * @return string $output
	 */	
	public function twitterAutolink($input,$newWindow=0,$withSearch=0) {
		$target=($newWindow)?' target="_blank" ':'';
    	$output = preg_replace('/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)@{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://twitter.com/$2\" ".$target." class=\"twitter-username\">@$2</a>$3 ", $input);
		if($withSearch) 
			$output = preg_replace('/([\.|\,|\:|\¡|\¿|\>|\{|\(]?)#{1}(\w*)([\.|\,|\:|\!|\?|\>|\}|\)]?)\s/i', "$1<a href=\"http://search.twitter.com/search?q=%23$2\" ".$target." class=\"twitter-search\">#$2</a>$3 ", $input);		
    	return $output;
	}  
	
	/**
	 * Runs a number of text functions, including autolink, nl2br, strip_tags. Assumes that you want simple
	 * text comments but witih a few niceties.
	 * @param string $input Text to make nice
	 * @return string $output
	 */
	public function makenice($input) {
		$output = strip_tags($input);
		$output = $this->autolink($output);
		$output = nl2br($output);
		return $output;
	}
	
	/** 
	 * A wrapper for PHP's fnmatch() function, which some installations don't have.
	 * @param string $pattern regex to use
	 * @param string $string String to match
	 * @return bool
	 */
	public function fnmatch($pattern, $string) {
		if(!function_exists('fnmatch')) {
			return preg_match("#^".strtr(preg_quote($pattern, '#'), array('\*' => '.*', '\?' => '.', '\[' => '[', '\]' => ']'))."$#i", $string);
		} else {
			return fnmatch($pattern, $string);
		}
	}
	
	
	/** 
	 * Takes a CamelCase string and turns it into camel_case
	 * @param string String to uncamel_case
	 * @return string $a
	 */
	public function uncamelcase($string) {
		$v = preg_split('/([A-Z])/', $string, false, PREG_SPLIT_DELIM_CAPTURE);
		$a = array();
		array_shift($v);
		for($i = 0; $i < count($v); $i++) {
			if ($i % 2) {
				if (function_exists('mb_strtolower')) {
					$a[] = mb_strtolower($v[$i - 1] . $v[$i], APP_CHARSET);
				} else {
					$a[] = strtolower($v[$i - 1] . $v[$i]);
				}
			}
		}
		return implode('_', $a);
	}
	
	/**
	 * Takes a handle-based string like "blah_blah" and turns it into "Blah Blah"
	 * @param string $string
	 * @return string $r1
	 */
	public function unhandle($string) {
		// takes something like collection_types and turns it into "Collection Types"
		$r1 = ucwords(str_replace(array('_', '/'), ' ', $string));
		return $r1;
	}

	/**
	 * Strips out non-alpha-numeric characters
	 * @param string $val
	 * @return string $val
	 */
	public function filterNonAlphaNum($val){ return preg_replace('/[^[:alnum:]]/', '', $val);  }
	
	/** 
	 * Useful for highlighting search strings within results (for nice display)
	 * @param string $value Value to search for
	 * @param string $searchString string to search through
	 * @return string
	 */
	 
	public function highlightSearch($value, $searchString) {
		return str_ireplace($searchString, '<em class="ccm-highlight-search">' . $searchString . '</em>', $value);
	}
}

?>
