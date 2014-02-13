<?
/**
 * @package Helpers
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */

/**
 * @package Helpers
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
defined('C5_EXECUTE') or die("Access Denied.");
class Concrete5_Helper_Url { 

	/**
	 * Adds keys and associated values to a query string
	 * @param mixed $variable
	 * @param string $value
	 * @param string $url
	 * @return $string
	 */ 
	public function setVariable($variable, $value = false, $url = false) {
		// either it's key/value as variables, or it's an associative array of key/values
		
		if ($url == false) {
			$url = Loader::helper('security')->sanitizeString($_SERVER['REQUEST_URI']);
		} elseif(!strstr($url,'?')) {
			$url = $url . '?' . Loader::helper('security')->sanitizeString($_SERVER['QUERY_STRING']);
		}

 		$vars = array();
		if (!is_array($variable)) {
			$vars[$variable] = $value;
		} else {
			$vars = $variable;
		}
		
		foreach($vars as $variable => $value) {
			$url = preg_replace('/(.*)(\?|&)' . $variable . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
			$url = substr($url, 0, -1);
			if (strpos($url, '?') === false) {
				$url = $url . '?' . $variable . '=' . $value;
			} else {
				$url = $url . '&' . $variable . '=' . $value;
			}
		}
		
		$url = $this->smartUrlEncode($url);
		return $url;
	}
	
	/**
	 * Removes keys and associated values from a query string
	 * @param mixed $variable
	 * @param string $url
	 * @return string
	 */ 
	public function unsetVariable($variable, $url = false) {
		// either it's key/value as variables, or it's an associative array of key/values
		
		if ($url == false) {
			$url = $_SERVER['REQUEST_URI'];
		} elseif(!strstr($url,'?')) {
			$url = $url . '?' . $_SERVER['QUERY_STRING'];
		}

 		$vars = array();
		if (!is_array($variable)) {
			$vars[] = $variable;
		} else {
			$vars = $variable;
		}
		
		foreach($vars as $variable) {
		  $url = preg_replace('/(.*)(\?|&)' . $variable . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&'); 
		  $url = substr($url, 0, -1); 
		}
		
		
		$url = $this->smartUrlEncode($url);
		return $url;
	}
	
	/**
	 * Builds a query string with http_build_query
	 * @param string $url
	 * @param array $params
	 * @return string
	 */
	public function buildQuery($url, $params) {
		return $url . '?' . http_build_query($params, '', '&');
	}

    	/**
	 * Shortens a given url with the tiny url api
	 * @param string $strURL
	 * @return string $url
	 */
	public function shortenURL($strURL) {
		$file = Loader::helper('file');
		$url = $file->getContents("http://tinyurl.com/api-create.php?url=".$strURL);
		return $url;
	}

	/**
	* Replaces all '&' with '&amp;' in a query string
	* @param string $url
	* @return string
	*/
	function smartUrlEncode($url) {
		if (strpos($url, '=') === false) {
			return $url;
		} else {
			$startpos = strpos($url, "?");
			$tmpurl = substr($url, 0, $startpos + 1);
			$qryStr = substr($url, $startpos + 1);
			$qryvalues = explode("&", $qryStr);
			foreach ($qryvalues as $value) {
				$buffer = explode("=", $value);
				$buffer[1] = urlencode($buffer[1]);
			}
			$finalqrystr = implode("&amp;", $qryvalues);
			$finalURL = $tmpurl . $finalqrystr;
			return $finalURL;
		}
	}
	
}
