<?php
define('C5_BUILD', true);

if(version_compare(PHP_VERSION, '5.1', '<')) {
	Console::WriteLine('Minimum required php version: 5.1, your is ' . PHP_VERSION, true);
	die(1);
}

require_once dirname(__FILE__) . '/base.php';

class Options extends OptionsBase {
	public static $Compress = true;
	protected static function ShowIntro() {
		global $argv;
		Console::WriteLine($argv[0] . ' is a tool that generates an optimized version of JavaScripts used by the concrete5 core.');
	}
	protected static function ShowOptions() {
		Console::WriteLine('--compress=<yes|no>         if yes (default) the output files will be compressed; use no for debugging');
	}
	protected static function ParseArgument($name, $value) {
		switch($name) {
			case '--compress':
				self::$Compress = self::ArgumentToBool($name, $value);
				return true;
		}
		return false;
	}
}

try {
	Options::Initialize();
	if(Options::$Compress && (!Enviro::CheckNodeJS('uglifyjs'))) {
		die(1);
	}
	MergeJavascript(
		array(
			'concrete/js/bootstrap/bootstrap.tooltip.js',
			'concrete/js/bootstrap/bootstrap.popover.js',
			'concrete/js/bootstrap/bootstrap.dropdown.js',
			'concrete/js/bootstrap/bootstrap.transitions.js',
			'concrete/js/bootstrap/bootstrap.alert.js'
		),
		'concrete/js/bootstrap.js'
	);
	MergeJavascript(
		'concrete/js/ccm_app/jquery.cookie.js',
		'concrete/js/jquery.cookie.js'
	);
	MergeJavascript(
		array(
			'concrete/js/ccm_app/jquery.colorpicker.js',
			'concrete/js/ccm_app/jquery.hoverIntent.js',
			'concrete/js/ccm_app/jquery.liveupdate.js',
			'concrete/js/ccm_app/jquery.metadata.js',
			'concrete/js/ccm_app/chosen.jquery.js',
			'concrete/js/ccm_app/dashboard.js',
			'concrete/js/ccm_app/filemanager.js',
			'concrete/js/ccm_app/jquery.cookie.js',
			'concrete/js/ccm_app/layouts.js',
			'concrete/js/ccm_app/legacy_dialog.js',
			'concrete/js/ccm_app/newsflow.js',
			'concrete/js/ccm_app/page_reindexing.js',
			'concrete/js/ccm_app/quicksilver.js',
			'concrete/js/ccm_app/remote_marketplace.js',
			'concrete/js/ccm_app/search.js',
			'concrete/js/ccm_app/sitemap.js',
			'concrete/js/ccm_app/status_bar.js',
			'concrete/js/ccm_app/tabs.js',
			'concrete/js/ccm_app/tinymce_integration.js',
			'concrete/js/ccm_app/ui.js',
			'concrete/js/ccm_app/toolbar.js',
			'concrete/js/ccm_app/themes.js'
		),
		'concrete/js/ccm.app.js',
		'--no-seqs'
	);
}
catch(Exception $x) {
	DieForException($x);
}

/** Compress one or more javascript files.
* @param string|array[string] $srcFiles The file(s) to compress.
* @param string $dstFile The file where to save the compressed scripts.
* @param array|string $options An optional list of options for the compiler.
* @param bool $pathsAreRelativeToRoot Set to true (default) if all the input/output files are relative to Options::$WebrootFolder, false otherwise.
* @throws Exception Throws an exception in case of errors.
*/
function MergeJavascript($srcFiles, $dstFile, $options = '', $pathsAreRelativeToRoot = true) {
	Console::Write("Generating $dstFile... ");
	if(!is_array($srcFiles)) {
		$srcFiles = array($srcFiles);
	}
	$srcFilesFull = array();
	foreach($srcFiles as $srcFile) {
		$srcFilesFull[] = $pathsAreRelativeToRoot ? Enviro::MergePath(Options::$WebrootFolder, $srcFile) : $srcFile;
	}
	$dstFileFull = $pathsAreRelativeToRoot ? Enviro::MergePath(Options::$WebrootFolder, $dstFile) : $srcFile;
	$tempFileSrc = '';
	$tempFileDst = '';
	try {
		switch(count($srcFilesFull)) {
			case 0:
				throw new Exception('No Javascript file to compress!');
			case 1:
				if(!is_file($srcFilesFull[0])) {
					throw new Exception("Unable to find the file '" . $srcFilesFull[0] . "'");
				}
				$srcLength = @filesize($srcFilesFull[0]);
				if($srcLength === false) {
					throw new Exception("Unable to check the size of the file '" . $srcFilesFull[0] . "'");
				}
				$compressMe = $srcFilesFull[0];
				$numSrc = 1;
				break;
			default:
				$srcLength = 0;
				$numSrc = 0;
				$tempFileSrc = Enviro::GetTemporaryFileName();
				foreach($srcFilesFull as $srcFileFull) {
					if(!is_file($srcFileFull)) {
						throw new Exception("Unable to find the file '$srcFileFull'");
					}
					$s = @file_get_contents($srcFileFull);
					if($s === false) {
						throw new Exception("Unable to read the content of the file '$srcFileFull'");
					}
					$srcLength += strlen($s);
					$numSrc++;
					if(!@file_put_contents($tempFileSrc, $s, FILE_APPEND)) {
						throw new Exception("Unable to write data to the temporary file");
					}
				}
				$compressMe = $tempFileSrc;
				break;
		}
		if(Options::$Compress) {
			$tempFileDst = Enviro::GetTemporaryFileName();
			if(!is_array($options)) {
				if((!is_string($options)) || ($options === '')) {
					$options = array();
				}
				else {
					$options = array($options);
				}
			}
			$options[] = '-o ' . escapeshellarg($tempFileDst);
			$options[] = escapeshellarg($compressMe);
			Enviro::RunNodeJS('uglifyjs', $options);
			$dstLength = @filesize($tempFileDst);
			if($dstLength === false) {
				throw new Exception("Unable to check the size of the file '" . $tempFileDst . "'");
			}
		}
		else {
			$dstLength = @filesize($compressMe);
			if($dstLength === false) {
				throw new Exception("Unable to check the size of the file '" . $compressMe . "'");
			}
		}
		$dstDir = dirname($dstFileFull);
		if(!is_dir($dstDir)) {
			if(!@mkdir($dstDir, 0777, true)) {
				throw new Exception("Unable to create the destination directory '$dstDir'");
			}
		}
		if(is_file($dstFileFull)) {
			@unlink($dstFileFull);
		}
		if(Options::$Compress) {
			if(!@rename($tempFileDst, $dstFileFull)) {
				throw new Exception("Unable to save the result to '$dstFileFull'");
			}
			$tempFileDst = '';
		}
		else {
			if(!@copy($compressMe, $dstFileFull)) {
				throw new Exception("Unable to save the result to '$dstFileFull'");
			}
		}
		if(strlen($tempFileSrc)) {
			@unlink($tempFileSrc);
			$tempFileSrc = '';
		}
		$gain = round(100 * (1 - $dstLength/$srcLength), 1);
		Console::WriteLine('ok.');
		Console::WriteLine("   Number of source files: $numSrc");
		Console::WriteLine("   Original file size(s) : " . number_format($srcLength) . " B");
		Console::WriteLine("   Final file size       : " . number_format($dstLength) . " B");
		Console::WriteLine("   Gain                  : $gain%");
	}
	catch(Exception $x) {
		if(strlen($tempFileDst)) {
			@unlink($tempFileDst);
		}
		if(strlen($tempFileSrc)) {
			@unlink($tempFileSrc);
		}
		throw $x;
	}
}