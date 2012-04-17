<?php
/**
 * This is a PHP port of the htmldiff.py library by Aaron Swartz <me@aaronsw.com>.
 * Uses SequenceMatcher library from the php-diff project:
 * https://github.com/chrisboulton/php-diff
 * 
 * All credits to the original authors of the code!
 * 
 * @author Antti Hukkanen <antti.hukkanen@mainiotech.fi>
 * @license LGPLv3, terms included in LICENSE file
 * 
 */
require_once dirname(__FILE__) . '/lib/php-diff/Diff/SequenceMatcher.php';

class HtmlDiff {
	
	final static function is_whitespace($c) {
		static $chars;
		if (!isset($chars)) {
			$chars = array("\r", "\n", "\t", " ", "\f");
		}
		return in_array($c, $chars);
	}
	
	public function diff($from, $to) {
		Loader::library('3rdparty/php-diff/Diff');
		Loader::library('3rdparty/php-diff/Diff/Renderer/Html/Markup');
		
		$a = $this->htmlToList($from);
		$b = $this->htmlToList($to);
		
		// Options for the SequenceMatcher
		$options = array(
			//'ignoreNewLines' => true,
			//'ignoreWhitespace' => true,
			//'ignoreCase' => true,
		);
		
		$sequenceMatcher = new Diff_SequenceMatcher($a, $b, null, $options);
		$opCodes = $sequenceMatcher->getOpCodes();
		
		$html = '';
		foreach ($opCodes as $code) {
			if ($code[0] === 'replace') {
				$apart = array_slice($a, $code[1], $code[2]-$code[1]);
				$bpart = array_slice($b, $code[3], $code[4]-$code[3]);
				$html .= '<del class="diff modified">' . 
					implode('', $apart) .
					'</del><ins class="diff modified">' .
					implode('', $bpart) .
					'</ins>';
			} else if ($code[0] === 'delete') {
				$apart = array_slice($a, $code[1], $code[2]-$code[1]);
				$html .= '<del class="diff">' . implode('', $apart) . '</del>';
			} else if ($code[0] === 'insert') {
				$bpart = array_slice($b, $code[3], $code[4]-$code[3]);
				$html .= '<ins class="diff">' . implode('', $bpart) . '</ins>';
			} else if ($code[0] === 'equal') {
				$bpart = array_slice($b, $code[3], $code[4]-$code[3]);
				$html .= implode('', $bpart);
			} else {
				throw new Exception("Unexpected opcode: ".$code[0]);
			}
		}
		return $html;
	}
	
	public function htmlToList($html, $b=0) {
		$mode = 'char';
		$cur = '';
		$out = array();
		for ($i=0; $i<strlen($html); $i++) {
			$c = $html[$i];
			if ($mode == 'tag') {
				if ($c == '>') {
					$cur .= $b > 0 ? ']' : $c;
					array_push($out, $cur);
					$cur = '';
					$mode = 'char';
				} else {
					$cur .= $c;
				}
			} else if ($mode == 'char') {
				if ($c == '<') {
					if ($cur != '') {
						array_push($out, $cur);
					}
					$cur = $b > 0 ? '[' : $c;
					$mode = 'tag';
				} else if (self::is_whitespace($c)) {
					array_push($out, $cur.$c);
					$cur = '';
				} else {
					$cur .= $c;
				}
			}
		}
		if ($cur != '') {
			array_push($out, $cur);
		}
		return $out;
	}
	
}