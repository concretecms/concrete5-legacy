<?php defined('C5_EXECUTE') or die('Access Denied.');

class Concrete5_Helper_TranslationFile {
	protected $headers;
	protected $translations;
	public function reset() {
		$this->headers = array();
		$this->setHeader('PO-Revision-Date', gmdate('Y-m-d H:i'));
		$this->setHeader('MIME-Version', '1.0');
		$this->setHeader('Content-Type', 'text/plain; charset=' . APP_CHARSET);
		$this->setHeader('Content-Transfer-Encoding', '8bit');
		$this->translations = array();
	}
	public function isEmpty() {
		return count($this->translations) ? false : true;
	}
	public function add($original, $translation) {
		$this->translations[$original] = $translation;
	}
	public function addWithContext($context, $original, $translation) {
		$this->add($context . "\x04" . $original, $translation);
	}
	public function setHeader($name, $value) {
		$this->headers[$name] = is_null($value) ? '' : strval($value);
	}
	public function save($filename) {
		// Original strings must be in increasing lexicographical order
		$translations = $this->translations;
		$headers = '';
		foreach($this->headers as $hName => $hValue) {
			$headers .= "$hName: $hValue\n";
		}
		if(strlen($headers)) {
			$translations[''] = $headers;
		}
		else {
			unset($translations['']);
		}
		ksort($translations, SORT_STRING);
		$numEntries = count($translations);
		$originalStrings = '';
		$translationStrings = '';
		$originalsInfo = array();
		$translationsInfo = array();
		foreach($translations as $originalString => $translationString) {
			$originalsInfo[] = array('relativeOffset' => strlen($originalStrings), 'length' => strlen($originalString));
			$originalStrings .= $originalString . "\x00";
			$translationsInfo[] = array('relativeOffset' => strlen($translationStrings), 'length' => strlen($translationString));
			$translationStrings .= $translationString . "\x00";
		}
		// Offset of table with the original strings index: right after the header (which is 7 words)
		$originalsIndexOffset = 7 * 4;
		// Size of table with the original strings index
		$originalsIndexSize = $numEntries * (4 + 4);
		// Offset of table with the translation strings index: right after the original strings index table
		$translationsIndexOffset = $originalsIndexOffset + $originalsIndexSize;
		// Size of table with the translation strings index
		$translationsIndexSize = $numEntries * (4 + 4);
		// Hashing table starts after the header and after the index table
		$originalsStringsOffset = $translationsIndexOffset + $translationsIndexSize;
		// Translations start after the keys
		$translationsStringsOffset = $originalsStringsOffset + strlen($originalStrings);
		
		// Let's generate the .mo file binary data
		$mo = '';
		// Magic number
		$mo .= pack('L', 0x950412de);
		// File format revision
		$mo .= pack('L', 0);
		// Number of strings
		$mo .= pack('L', $numEntries);
		// Offset of table with original strings
		$mo .= pack('L', $originalsIndexOffset);
		// Offset of table with translation strings
		$mo .= pack('L', $translationsIndexOffset);
		// Size of hashing table: we don't use it.
		$mo .= pack('L', 0);
		// Offset of hashing table: it would start right after the translations index table
		$mo .= pack('L', $translationsIndexOffset + $translationsIndexSize);
		// Write the lengths & offsets of the original strings
		foreach($originalsInfo as $info) {
			$mo .= pack('L', $info['length']);
			$mo .= pack('L', $originalsStringsOffset + $info['relativeOffset']);
		}
		// Write the lengths & offsets of the translated strings
		foreach($translationsInfo as $info) {
			$mo .= pack('L', $info['length']);
			$mo .= pack('L', $translationsStringsOffset + $info['relativeOffset']);
		}
		// Write original strings
		$mo .= $originalStrings;
		// Write translation strings
		$mo .= $translationStrings;
		return @file_put_contents($filename, $mo) ? true : false;
	}
}