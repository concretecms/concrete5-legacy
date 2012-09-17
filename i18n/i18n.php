<?php

if(version_compare(PHP_VERSION, '5.3', '<')) {
	Console::WriteLine('Minimum required php version: 5.3, your is ' . PHP_VERSION, true);
	die(1);
}

SetupIni();

Options::Initialize();

try {
	Options::CheckEnviro();
	Options::ReadArguments();
	foreach(Options::$packages as $package) {
		if($package->createPot) {
			POTFile::CreateNew($package);
		}
	}
	die(0);
} catch(Exception $x) {
	@chdir(Options::$INITIAL_CD);
	Console::WriteLine('ERROR: ' . $x->getMessage(), true);
	die(($x->getCode() == 0) ? 1 : $x->getCode());
}

/** Initializes the enviro (error reporting, timezone, …). */
function SetupIni() {
	@ini_set('error_reporting', E_ALL & ~E_DEPRECATED);
	$ddtz = @date_default_timezone_get();
	if((!is_string($ddtz)) || (!strlen($ddtz))) {
		$ddtz = 'UTC';
	}
	@date_default_timezone_set($ddtz);
	@ini_set('track_errors', true);
	@ini_set('html_errors', false);
	@ini_set('display_errors', 'stderr');
	@ini_set('display_startup_errors', true);
	@ini_set('log_errors', false);
	set_error_handler('ErrorCatcher');
}

/** Catches a php error/warning and raises an exception.
* @param int $errNo The level of the error raised.
* @param string $errstr the error message.
* @param unknown $errfile The filename that the error was raised in.
* @param unknown $errline The line number the error was raised at.
* @throws Exception Throws an Exception when an error is detected during the script execution.
*/
function ErrorCatcher($errno, $errstr, $errfile, $errline)
{
	throw new Exception("$errstr in $errfile on line $errline", $errno);
}

/** Static class holding options. */
class Options {

	/** The current folder.
	* @var string
	*/
	public static $INITIAL_CD;

	/** The folder containing this script.
	* @var string
	*/
	public static $I18NROOT;

	/** The folder containing the gettext tools for Windows.
	* @var string
	*/
	public static $I18N_WIN32TOOLS;

	/** The default folder containing the web part of concrete5.
	* @var string
	*/
	public static $DEFAULT_WEBROOT;

	/** The email address to which translators should report localization bugs.
	* @var string
	*/
	public static $DEFAULT_POTCONTACT_CONCRETE5;

	/** Default value of 'indent' option.
	* @var bool
	*/
	public static $DEFAULT_INDENT;

	/** The default folders to be excluded from .pot generation for concrete5 (relative to web root).
	* @var array[string]
	*/
	public static $DEFAULT_EXCLUDEDIRSFROMPOT_CONCRETE5;

	/** The default folders to be excluded from .pot generation for packages (relative to the folder of the package).
	* @var array[string]
	*/
	public static $DEFAULT_EXCLUDEDIRSFROMPOT_PACKAGE;

	/** The directory containing the web part of concrete5.
	* @var string
	*/
	public static $webroot;

	/** List of info about concrete5 / packages.
	* @var array[PackageInfo]
	*/
	public static $packages;

	/** Have we to create indented .pot/.po files?
	* @var bool
	*/
	public static $indent;

	/** Initializes constant/default values. */
	public static function Initialize() {
		self::$INITIAL_CD = getcwd();
		self::$I18NROOT = dirname(__FILE__);
		self::$I18N_WIN32TOOLS = Enviro::MergePath(self::$I18NROOT, 'tools/windows');
		self::$DEFAULT_WEBROOT = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'web';
		self::$DEFAULT_POTCONTACT_CONCRETE5 = 'andrew@concrete5.org';
		self::$DEFAULT_INDENT = true;
		self::$DEFAULT_EXCLUDEDIRSFROMPOT_CONCRETE5 = array('concrete/libraries/3rdparty');
		self::$DEFAULT_EXCLUDEDIRSFROMPOT_PACKAGE = array('libraries/3rdparty');
	}

	/** Checks the environment state.
	* @throws Exception Throws an Exception in case of errors.
	*/
	public static function CheckEnviro() {
		try {
			Enviro::RunTool('xgettext', '--version');
		}
		catch(Exception $x) {
			Console::WriteLine('This tools require the gettext functions.', true);
			if(Enviro::IsWin()) {
				Console::Write('There\'s a ready-to-use version on ftp.gnome.org. Would you like me to download it automatically? [Y/n] ', true);
				if(!Console::AskYesNo(true, true)) {
					Console::WriteLine('Please put gettext functions in the following folder:', true);
					Console::WriteLine(self::$I18N_WIN32TOOLS, true);
					die(1);
				}
				self::DownloadZip(
					'http://ftp.gnome.org/pub/gnome/binaries/win32/dependencies/gettext-runtime_0.18.1.1-2_win32.zip',
					array(
						'bin/intl.dll' => self::$I18N_WIN32TOOLS
					)
				);
				self::DownloadZip(
					'http://ftp.gnome.org/pub/gnome/binaries/win32/dependencies/gettext-tools-dev_0.18.1.1-2_win32.zip',
					array(
						'bin/libgettextlib-0-18-1.dll' => Enviro::MergePath(self::$I18N_WIN32TOOLS),
						'bin/xgettext.exe' => self::$I18N_WIN32TOOLS
					)
				);
			}
			else {
				Console::WriteLine('Usually under *nix you can install it with:', true);
				Console::WriteLine('sudo apt-get install gettext', true);
				die(1);
			}
		}
	}

	/** Show help about the script.
	* @param bool $forInvalidArgs true if we're showning help when we've encountered some unknown arguments (default: false).
	*/
	public static function ShowHelp($forInvalidArgs = false) {
		global $argv;
		if(!$forInvalidArgs) {
			Console::WriteLine($argv[0] . ' is a tool that helps extracting localizable strings from concrete5 and/or from packages.');
			Console::WriteLine();
		}
		Console::WriteLine('Available options:');
		Console::WriteLine('--help                     show this message');
		Console::WriteLine('--webroot=<path>           set the web root of concrete5 (default: ' . self::$DEFAULT_WEBROOT . ')');
		Console::WriteLine('--indent=<yes|no>          use to yes to generate indented .pot/.po files, false for not-indented generation (default: ' . (self::$DEFAULT_INDENT ? 'yes' : 'no') . ')');
		Console::WriteLine('--package=<packagename>    adds a package. Subsequent arguments are relative to the latest package (or to concrete5 itself to ');
		Console::WriteLine();
		Console::WriteLine('For concrete5 and/or each package you can specify specific options. Before the first --package you\'re assigning options to the core of concrete5.');
		Console::WriteLine('Available package options:');
		Console::WriteLine('--createpot=<yes|no>       use to yes to generate the .pot file, no to skip it (defaults to yes, except for concrete5 when you\'ve specified a --package option)');
		Console::WriteLine('--potname=<filename>       name of the .pot filename (just the name, without path: it\'ll be saved in the \'languages\' folder)');
		Console::WriteLine('--excludedirfrompot=<dir>  folder which may not be parsed when creating a.pot file. To specify multiple values you can specify this argument more than once (default for concrete5: ' . implode(self::$DEFAULT_EXCLUDEDIRSFROMPOT_CONCRETE5), ', default for packages: ' . implode(self::$DEFAULT_EXCLUDEDIRSFROMPOT_CONCRETE5, ', ') . ')');
		Console::WriteLine('--potcontact               email address to send bugs to (for c5 the default is ' . self::$DEFAULT_POTCONTACT_CONCRETE5 . ' when working on concrete5, empty when working on a package.');
		if(!$forInvalidArgs) {
			Console::WriteLine();
			Console::WriteLine('Examples:');
			Console::WriteLine('To create the .pot file for concrete5: php ' . $argv[0]);
			Console::WriteLine('To create the .pot file for concrete5 (with path specification): php ' . $argv[0] . ' --webroot=' . (Enviro::IsWin() ? 'C:\\Inetpub\\wwwroot' : '/var/www'));
			Console::WriteLine('To create the .pot file for the package foobar: php ' . $argv[0] . ' --package=foobar');
			Console::WriteLine('To create the .pot file for concrete5 AND for the package foobar: php ' . $argv[0] . ' --createpot=yes --package=foobar');
		}
	}

	/** Read the command line arguments.
	* @return array[string=>string]
	* @throws Exception Throws an Exception in case of parameter errors.
	*/
	public static function ReadArguments() {
		global $argv;
		self::$webroot = self::$DEFAULT_WEBROOT;
		self::$indent = self::$DEFAULT_INDENT;
		self::$packages = array();
		self::$packages[] = $packageInfo = new PackageInfo('');
		foreach($argv as $argi => $arg) {
			if($argi == 0) {
				continue;
			}
			$p = strpos($arg, '=');
			$argument = strtolower(($p === false) ? $arg : substr($arg, 0, $p));
			$value = ($p === false) ? '' : substr($arg, $p + 1);
			switch($argument) {
				case '--help':
					self::ShowHelp();
					die(0);
				case '--webroot':
					if(!strlen($value)) {
						throw new Exception("Argument '$argument' requires a value (a valid path).");
					}
					$dir = @realpath($value);
					if(($dir === false) || (!is_dir($dir))) {
						throw new Exception("Argument '$argument' received an invalid path ('$value').");
					}
					self::$webroot = $dir;
					break;
				case '--indent':
					self::$indent = self::ArgumentToBool($argument, $value);
					break;
				case '--package':
					if(!strlen($value)) {
						throw new Exception("Argument '$argument' requires a value (the package name).");
					}
					if(!Enviro::IsFilenameWithoutPath($value)) {
						throw new Exception("Argument '$argument' requires a package name (not its path), given '$value'.");
					}
					if(is_null(self::$packages[0]->createPot)) {
						self::$packages[0]->createPot = false;
					}
					self::$packages[] = $packageInfo = new PackageInfo($value);
					break;
				case '--createpot':
					$packageInfo->createPot = self::ArgumentToBool($argument, $value);
					break;
				case '--potname':
					if(!strlen($value)) {
						throw new Exception("Argument '$argument' requires a value.");
					}
					if(!Enviro::IsFilenameWithoutPath($value)) {
						throw new Exception("Argument '$argument' requires a .pot filename name (without any path info), given '$value'.");
					}
					$packageInfo->potName = $value;
					break;
				case '--potcontact':
					$packageInfo->potContact = $value;
					break;
				case '--excludedirfrompot':
					if(!strlen($value)) {
						$packageInfo->excludeDirsFromPot = array();
					}
					else {
						if(!is_array($packageInfo->excludeDirsFromPot)) {
							$packageInfo->excludeDirsFromPot = array();
						}
						$packageInfo->excludeDirsFromPot[] = $value;
					}
					break;
				default:
					Console::WriteLine("Invalid argument '$argument'", true);
					self::ShowHelp(true);
					die(1);
			}
		}
		foreach(self::$packages as $packageInfo) {
			$packageInfo->postInitialize();
		}
	}

	/** Return the boolean value of a command line option value.
	* @param string $argumentName The argument name.
	* @param string $argumentValue The argument value.
	* @return boolean
	* @throws Exception Throws an Exception if the argument value can't be converted to a boolean value.
	*/
	private static function ArgumentToBool($argumentName, $argumentValue) {
		$v = @trim($argumentValue);
		if(!strlen($v)) {
			throw new Exception("Argument '$argumentName' requires a boolean value (yes or no).");
		}
		switch(strtolower($v)) {
			case 'yes':
			case 'true':
			case 'on':
			case '1':
				return true;
			case 'no':
			case 'false':
			case 'off':
			case '0':
				return false;
			default:
				throw new Exception("Argument '$argumentName' requires a boolean value (yes or no), given '$argumentValue'.");
		}
	}

	/** Download a file and extract files to a local path.
	* @param string $url
	* @param array $filesToExtract Specify which files to extract (the array keys) and the folders where they should be saved (the values).
	* @throws Exception Throws an Exception in case of errors.
	*/
	private static function DownloadZip($url, $filesToExtract) {
		foreach(array_keys($filesToExtract) as $key) {
			if(!is_dir($filesToExtract[$key])) {
				if(!@mkdir($filesToExtract[$key], 0777, true)) {
					throw new Exception('Error creating the directory \'' . $filesToExtract[$key] . '\'');
				}
			}
			$fullFilename = Enviro::MergePath($filesToExtract[$key], basename($key));
			if(file_exists($fullFilename)) {
				unset($filesToExtract[$key]);
			}
			else {
				if(!is_writable($filesToExtract[$key])) {
					throw new Exception('The directory \'' . $filesToExtract[$key] . '\' is not writable');
				}
				$filesToExtract[$key] = $fullFilename;
			}
		}
		if(empty($filesToExtract)) {
			return;
		}
		Console::Write('Downloading ' . $url . '... ');
		if(!($hUrl = fopen($url, 'rb'))) {
			throw new Exception('fopen() failed!');
		}
		$bufferSize = 8 * 1024;
		try {
			$zipFile = Enviro::GetTemporaryFileName();
			if(!($hZipFile = fopen($zipFile, 'wb'))) {
				throw new Exception('Unable to write local temp file.');
			}
			try {
				while(!feof($hUrl)) {
					fwrite($hZipFile, fread($hUrl, $bufferSize));
				}
				@fflush($hZipFile);
				@fclose($hZipFile);
				@fclose($hUrl);
			}
			catch(Exception $x) {
				@fclose($hZipFile);
				@unlink($zipFile);
				throw $x;
			}
		}
		catch(Exception $x) {
			@fclose($hUrl);
			throw $x;
		}
		Console::WriteLine('done.');
		Console::Write('Extracting files... ');
		try {
			$hZip = @zip_open($zipFile);
			if(!is_resource($hZip)) {
				throw new Exception('zip_open() failed (error code: ' . $hZip . ')!');
			}
			while($hEntry = zip_read($hZip)) {
				if(!is_resource($hEntry)) {
					throw new Exception('zip_read() failed (error code: ' . $hEntry . ')!');
				}
				$name = zip_entry_name($hEntry);
				if(array_key_exists($name, $filesToExtract)) {
					$size = zip_entry_filesize($hEntry);
					if($size <= 0) {
						throw new Exception('zip entry ' . $name . ' is empty!');
					}
					file_put_contents($filesToExtract[$name], zip_entry_read($hEntry, $size));
					unset($filesToExtract[$name]);
				}
			}
			@zip_close($hZip);
			@unlink($zipFile);
		}
		catch(Exception $x) {
			@zip_close($hZip);
			@unlink($zipFile);
			throw $x;
		}
		if(!empty($filesToExtract)) {
			throw new Exception('Files not found in zip file: ' . implode(', ', array_keys($filesToExtract)));
		}
		Console::WriteLine('done.');
	}
}

/** Holds the info about main concrete5 or about a package. */
class PackageInfo {

	/** The package name. Empty means we're going to potify concrete5 itself.
	* @var string
	*/
	public $package;

	/** I'm for concrete5 (true) of for a package (false)?
	* @var bool
	*/
	public $isConcrete5;

	/** The contact email address.
	* @var string
	*/
	public $potContact;

	/** List of dirs to exclude.
	* @var string
	*/
	public $excludeDirsFromPot;

	/** True if we have to create the .pot file.
	* @var bool
	*/
	public $createPot;

	/** The name of the .pot file.
	* @var string
	*/
	public $potName;

	/** The path of the directory to parse to create the .pot file (relative to the web root).
	* @var string
	*/
	public $directoryToPotify;

	/** The full name of the .pot file.
	* @var string
	*/
	public $potFullname;

	/** The version of concrete5/package.
	* @var string
	*/
	public $version;

	/** The relative path from .pot file to web root.
	* @var string
	*/
	public $potfile2root;

	/** Initializes the class instance.
	* @param string $package The package name (empty for concrete5).
	*/
	public function __construct($package) {
		$this->package = is_string($package) ? trim($package) : '';
		if(strlen($this->package)) {
			$this->isConcrete5 = false;
			$this->potContact = '';
		}
		else {
			$this->isConcrete5 = true;
			$this->potContact = Options::$DEFAULT_POTCONTACT_CONCRETE5;
		}
		$this->excludeDirsFromPot = null;
		$this->createPot = null;
		$this->potName = 'messages.pot';
	}

	/** Fix the instance values once we've read all the command line arguments. */
	public function postInitialize() {
		if(!defined('C5_EXECUTE')) {
			define('C5_EXECUTE', true);
		}
		if($this->isConcrete5) {
			if(is_null($this->createPot)) {
				$this->createPot = (count(Options::$packages) == 1) ? true : false;
			}
			if(is_null($this->excludeDirsFromPot)) {
				$this->excludeDirsFromPot = Options::$DEFAULT_EXCLUDEDIRSFROMPOT_CONCRETE5;
			}
			if(!is_file($fn = Enviro::MergePath(Options::$webroot, 'concrete/config/version.php'))) {
				throw new Exception(Options::$webroot . ' is not the valid concrete5 web root directory.');
			}
			@include $fn;
			if(empty($APP_VERSION)) {
				throw new Exception("Unable to parse the concrete5 version file '$fn'.");
			}
			$this->version = $APP_VERSION;
			$this->directoryToPotify = 'concrete';
			$this->potFullname = Enviro::MergePath(Options::$webroot, 'languages/' . $this->potName);
			$this->potfile2root = '..';
		}
		else {
			if(is_null($this->createPot)) {
				$this->createPot = true;
			}
			if(is_null($this->excludeDirsFromPot)) {
				$this->excludeDirsFromPot = Options::$DEFAULT_EXCLUDEDIRSFROMPOT_PACKAGE;
			}
			if(!is_file($fn = Enviro::MergePath(Options::$webroot, 'packages', $this->package, 'controller.php'))) {
				throw new Exception("'" . $this->package . "' is not a valid package name ('$fn' not found).");
			}
			$fc = "\n" . self::GetEvaluableContent($fn);
			if(!preg_match('/[\r\n]\s*class[\r\n\s]+([^\s\r\n]+)[\r\n\s]+extends[\r\n\s]+Package\s*\{/i', $fc, $m)) {
				throw new Exception("'" . self::$package . "' can't be parsed for a version.");
			}
			$packageClass = $m[1];
			if(!class_exists('Package')) {
				eval('class Package {}');
			}
			@ob_start();
			$evalued = eval($fc);
			@ob_end_clean();
			if($evalued === false) {
				throw new Exception("Unable to parse the version of package (file '$fn').");
			}
			if(!class_exists("VersionGetter_$packageClass")) {
				eval("class VersionGetter_$packageClass extends $packageClass { public static function GV(){\$me = new VersionGetter_$packageClass(); return \$me->pkgVersion;} }");
			}
			$r = eval("return VersionGetter_$packageClass::GV();");
			if(empty($r) && ($r !== '0')) {
				throw new Exception("Unable to parse the version of package (file '$fn').");
			}
			$this->version = $r;
			$this->directoryToPotify = 'packages/' . $this->package;
			$this->potFullname = Enviro::MergePath(Options::$webroot, 'packages', $this->package, 'languages', $this->potName);
			$this->potfile2root = '../../..';
		}
	}

	/** Gets the content of a php file which may be passed to the eval() function.
	* @param string $phpFilename The source php file.
	* @throws Exception Throws an Exception in case of errors.
	* @return string
	*/
	private static function GetEvaluableContent($phpFilename) {
		$fc = @file_get_contents($phpFilename);
		if($fc === false) {
			global $php_errormsg;
			throw new Exception("Unable to read file '$phpFilename': $php_errormsg");
		}
		$p1 = strpos($fc, $s1 = '<?php');
		$p2 = strpos($fc, $s2 = '<?');
		if($p2 === false) {
			if($p1 === false) {
				throw new Exception("Unable to parse the file '$phpFilename'.");
			}
			$p = $p1;
			$s = $s1;
		} elseif($p1 === false) {
			$p = $p2;
			$s = $s2;
		} elseif($p1 <= $p2) {
			$p = $p1;
			$s = $s1;
		} else {
			$p = $p2;
			$s = $s2;
		}
		return trim(substr($fc, $p + strlen($s)));
	}

}

/** Console-related functions. */
class Console {

	/** Echoes a string to the console.
	* @param string $str The string to be printed.
	* @param bool $isErr Set to true to echo to stderr, false to echo to stdout.
	*/
	public static function Write($str, $isErr = false) {
		$hOut = fopen($isErr ? 'php://stderr' : 'php://stdout', 'wb');
		fwrite($hOut, $str);
		fflush($hOut);
		fclose($hOut);
	}

	/** Echoes a line to the console.
	* @param string $str The string to be printed (a new-line will be appended to it).
	* @param bool $isErr Set to true to echo to stderr, false to echo to stdout.
	*/
	public static function WriteLine($str = '', $isErr = false) {
		self::Write($str . Enviro::EOL(), $isErr);
	}

	/** Reads a line from the command line.
	* @return string
	*/
	public static function ReadLine() {
		$hIn = fopen ('php://stdin', 'r');
		$line = (string)@fgets($hIn);
		fclose($hIn);
		return $line;
	}

	/** Read a yes/no answer from the command line.
	* @param bool|null $default What to return if user enter an empty string (if null: no default value).
	* @param bool $isErr Set to true to echo to stderr, false to echo to stdout.
	* @return bool
	*/
	public static function AskYesNo($default = null, $msgIsErr = false) {
		for(;;) {
			switch(strtolower(trim(self::ReadLine()))) {
				case 'y':
				case 'yes':
					return true;
				case 'n':
				case 'no':
					return false;
				case '':
					if(!is_null($default)) {
						return $default;
					}
					break;
			}
			self::Write('Pleas answer with Y[es] or N[o]: ', $msgIsErr);
		}
	}
}

/** Functions related to the current executing environment. */
class Enviro {
	/** We're in a Windows operating system?
	* @return boolean
	*/
	public static function IsWin() {
		return (stripos(PHP_OS , 'win') === 0) ? true : false;
	}
	/** The end of line char sequence for the current operating system.
	* @return string
	*/
	public static function EOL() {
		return self::IsWin() ? "\r\n" : "\n";
	}

	/** Check if a string is a valid filename (without any path info).
	* @param string $filename
	* @return boolean
	*/
	public static function IsFilenameWithoutPath($filename) {
		$filename = is_null($filename) ? '' : (string)$filename;
		if(!strlen($filename)) {
			return false;
		}
		if($filename != trim($filename)) {
			return false;
		}
		switch($filename) {
			case '.':
			case '..':
				return false;
		}
		if(!preg_match('/^[^\\\\\\/\?*:\|"<>]+$/', $filename)) {
			return false;
		}
		return true;
	}

	/** Merges OS paths.
	* @param {string} Any number of paths to be merged.
	* @return string
	* @throws Exception Throws an Exception if no arguments is given.
	*/
	public static function MergePath() {
		$args = func_get_args();
		switch(count($args)) {
			case 0:
				throw new Exception(__CLASS__ . '::' . __METHOD__ . ': missing arguments');
			case 1:
				return $args[0];
			default:
				$path = '';
				foreach($args as $arg) {
					if(strlen($arg)) {
						$arg = str_replace('\\', '/', $arg);
						if(!strlen($path)) {
							$path = $arg;
						} else {
							$path = rtrim($path, '/') . '/' . ltrim($arg, '/');
						}
					}
				}
				return str_replace('/', DIRECTORY_SEPARATOR, $path);
		}
	}
	/** Create a temporary file.
	* @return string
	* @throws Exception Throws an Exception in case the temporary file couldn't be created.
	*/
	public static function GetTemporaryFileName() {
		$tempFolder = self::IsWin() ? '%TEMP%' : '/var/tmp';
		$tempFile = @tempnam($tempFolder, 'c5-');
		if($tempFile === false) {
			global $php_errormsg;
			throw new Exception("Unable to create a temporary file in '$tempFolder': $php_errormsg");
		}
		return $tempFile;
	}
	/** Escapes a string to be passed to a shell command, encapsulating it in quotes if necessary.
	* @param string $string The parameter to be escaped.
	* @return string Returns the escaped string.
	*/
	public static function EscapeArg($string)
	{
		$string = (string)$string;
		if(strlen($string)) {
			if(strpos($string, ' ')) {
				$string = '"' . $string . '"';
			}
		}
		return $string;
	}
	/** Execute a shell command (build-in if *nix; an exe file under tools/windows if we're in Windows).
	* @param string $command The command to execute (assumes an exe in tools/windows folder if OS is Windows).
	* @param string|array $arguments If string, the arguments will be used as is, if array they will be escaped.
	* @param int|array $goodResult Valid return code(s) of the command (default: 0).
	* @param array[out] $output The output from stdout/stderr of the command.
	* @return int Return the command result code.
	* @throws Exception Throws an exception in case of errors.
	*/
	public static function RunTool($command, $arguments, $goodResult = 0, &$output = null) {
		if(self::IsWin()) {
			$line = Enviro::MergePath(Options::$I18N_WIN32TOOLS, "$command.exe");
			if(!is_file($line)) {
				throw new Exception('The executable file ' . $line . ' does not exists.');
			}
			$line = self::EscapeArg($line);
		}
		else {
			$line = $command;
		}
		if(is_array($arguments)) {
			if(count($arguments)) {
				$line .= ' ' . implode(' ', $arguments);
			}
		} else {
			$arguments = (string)$arguments;
			if(strlen($arguments)) {
				$line .= ' ' . $arguments;
			}
		}
		$output = array();
		exec(escapeshellcmd($line) . ' 2>&1', $output, $rc);
		if(!@is_int($rc)) {
			$rc = -1;
		}
		if(!is_array($output)) {
			$output = array();
		}
		if(is_array($goodResult)) {
			if(array_search($rc, $goodResult) === false) {
				throw new Exception("$command failed: " . implode("\n", $output));
			}
		}
		elseif($rc != $goodResult) {
			throw new Exception("$command failed: " . implode("\n", $output));
		}
		return $rc;
	}
}

/** Various .pot-related functions. */
class POTFile {
	/** Create the .pot file starting from sources.
	* @param PackageInfo $packageInfo The info about the .pot file to be created (it'll be overwritten if already existing).
	* @throws Exception Throws an Exception in case of errors.
	*/
	public static function CreateNew($packageInfo) {
		Console::WriteLine('* CREATING .POT FILE ' . $packageInfo->potName . ' FOR ' . ($packageInfo->isConcrete5 ? 'concrete5 core' : $packageInfo->package));
		Console::Write('  Listing .php files... ');
		$phpFiles = array();
		self::GetFiles($packageInfo->directoryToPotify, 'php', $phpFiles, $packageInfo->excludeDirsFromPot);
		if(!count($phpFiles)) {
			throw new Exception('No source .php files found.');
		}
		Console::WriteLine(count($phpFiles) . ' files found.');
		$xmlFiles = array();
		if($packageInfo->isConcrete5) {
			Console::Write('  Listing .xml files... ');
			self::GetFiles($packageInfo->directoryToPotify, 'xml', $xmlFiles, $packageInfo->excludeDirsFromPot);
			if(!count($xmlFiles)) {
				throw new Exception('No .xml files found.');
			}
			Console::WriteLine(count($xmlFiles) . ' files found.');
		}
		Console::Write('  Extracting strings from .php files... ');
		$tempList = Enviro::GetTemporaryFileName();
		try {
			if(!@file_put_contents($tempList, implode("\n", $phpFiles))) {
				global $php_errormsg;
				throw new Exception("Error writing to '$tempFile': $php_errormsg");
			}
			$tempPot = Enviro::GetTemporaryFileName();
			try {
				@chdir(Options::$webroot);
				$args = array();
				$args[] = '--default-domain=messages'; // Domain
				$args[] = '--output=' . Enviro::EscapeArg(basename($tempPot)); // Output .pot file name
				$args[] = '--output-dir=' . Enviro::EscapeArg(dirname($tempPot)); // Output .pot folder name
				$args[] = '--language=PHP'; // Source files are in php
				$args[] = '--from-code=UTF-8'; // Source files are in utf-8
				$args[] = '--add-comments=i18n'; // Place comment blocks preceding keyword lines in output file if they start with '// i18n: '
				$args[] = '--keyword'; // Don't use default keywords
				$args[] = '--keyword=t'; // Look for the first argument of the "t" function for extracting translatable text in singular form
				$args[] = '--keyword=t2:1,2'; // Look for the first and second arguments of the "t2" function for extracting both the singular and plural forms
				$args[] = '--no-escape'; // Do not use C escapes in output
				$args[] = '--indent'; // Write using indented style
				$args[] = '--add-location'; // Generate '#: filename:line' lines
				$args[] = '--no-wrap'; // Do not break long message lines, longer than the output page width, into several lines
				$args[] = '--files-from=' . Enviro::EscapeArg($tempList); // Get list of input files from file
				Enviro::RunTool('xgettext', $args);
				Console::WriteLine('done.');
				if(!empty($xmlFiles)) {
					Console::Write('  Extracting strings from .xml files... ');
					$xmlEntries = POEntry::FromXmlFile($xmlFiles);
					Console::WriteLine('done.');
				}
				Console::Write('  Loading .pot file... ');
				$pot = new POTFile($tempPot);
				Console::WriteLine('done.');
				Console::Write('  Fixing .pot file... ');
				$pot->FixHeader($packageInfo);
				$pot->FixFilesSlash();
				if(!Enviro::IsWin()) {
					$pot->Replace_CRLF_LF();
				}
				Console::WriteLine('done.');
				if(!empty($xmlFiles)) {
					Console::Write('  Merging strings from xml... ');
					$pot->MergeEntries($xmlEntries);
					Console::WriteLine('done.');
				}
				Console::Write('  Saving .pot file... ');
				$pot->SaveAs($packageInfo->potFullname, Options::$indent);
				Console::WriteLine('done.');
				Console::WriteLine('  .pot file created: ' . $packageInfo->potFullname);
				@unlink($tempPot);
				@unlink($tempList);
			}
			catch(Exception $x)
			{
				@unlink($tempPot);
				throw $x;
			}
		} catch(Exception $x) {
			@unlink($tempList);
			throw $x;
		}
	}
	/** Sub-function called by POTFile::CreateNew to parse a sub-folder.
	* @param string $relPath The relative path of the sub-folder to be analyzed.
	* @param string $extension The lower-case extension of the files to retrieve (without initial dot).
	* @param array $items Found files will be appended to this array.
	* @throws Exception Throws an exception in case of errors.
	*/
	protected static function GetFiles($relPath, $extension, &$items, $excludedDirs = array(), $_callback = false) {
		global $options;
		if(!$_callback) {
			if(is_array($excludedDirs)) {
				if(DIRECTORY_SEPARATOR != '/') {
					foreach(array_keys($excludedDirs) as $i) {
						$excludedDirs[$i] = str_replace('/', DIRECTORY_SEPARATOR, $excludedDirs[$i]);
					}
				}
			} else {
				$excludedDirs = array();
			}
		}
		$absPath = Enviro::MergePath(Options::$webroot, $relPath);
		if(!($hDir = @opendir($absPath))) {
			global $php_errormsg;
			throw new Exception("Error opening '$absPath': $php_errormsg");
		}
		try {
			while(($entry = @readdir($hDir)) !== false) {
				$relPathSub = Enviro::MergePath($relPath, $entry);
				$absPathSub = Enviro::MergePath($absPath, $entry);
				if(is_dir($absPathSub)) {
					switch($entry) {
						case '.':
						case '..':
							break;
						default:
							if(array_search($relPathSub, $excludedDirs) === false)
							{
								self::GetFiles($relPathSub, $extension, $items, $excludedDirs, true);
							}
							break;
					}
				} else {
					switch(strtolower(pathinfo($absPathSub, PATHINFO_EXTENSION))) {
						case $extension:
							$items[] = $relPathSub;
							break;
					}
				}
			}
			closedir($hDir);
		}
		catch(Exception $x) {
			@closedir($hDir);
			throw $x;
		}
	}

	public $Header;
	public $Entries;
	public function __construct($filename) {
		if(($s = @realpath($filename)) === false) {
			throw new Exception("Error resolving $filename: does it exists?");
		}
		$filename = $s;
		if(!($lines = @file($filename, FILE_IGNORE_NEW_LINES))) {
			global $php_errormsg;
			throw new Exception("Error reading '$filename': $php_errormsg");
		}
		try {
			$this->Header = null;
			$this->Entries = array();
			$i = 0;
			$n = count($lines);
			while($i < $n) {
				if($entry = POEntry::GetNextFromLines($lines, $i, $nextStart)) {
					if((!$this->Header) && empty($this->Entries) && is_a($entry, 'POEntrySingle') && (!strlen($entry->getMsgID()))) {
						$this->Header = $entry;
					} else {
						$hash = $entry->GetHash();
						if(array_key_exists($hash, $this->Entries) !== false) {
							throw new Exception('Duplicated entry!');
						}
						$this->Entries[$hash] = $entry;
					}
				}
				$i = $nextStart;
			}
		}
		catch(Exception $x) {
			throw new Exception("Error reading $filename: " . $x->getMessage());
		}
	}

	/** Fixes the content of a .pot file (set the CHARSET and convert back-slashes to forward-slashes in file paths if os is Windows).
	* @param PackageInfo $packageInfo The info about the package.
	* @param string $filename The full path to the .pot file.
	* @throws Exception Throws an exception in case of errors.
	*/
	public function FixHeader($packageInfo, $timestamp = null) {
		$this->Header = new POEntrySingle(
			array(),
			array(
				'Project-Id-Version: ' . ($packageInfo->isConcrete5 ? 'concrete5' : $packageInfo->package) . ' ' . $packageInfo->version . '\\n',
				'Report-Msgid-Bugs-To: ' . $packageInfo->potContact . '\\n',
				'POT-Creation-Date: ' . gmdate('Y-m-d H:i', $timestamp ? $timestamp : time()) . '+0000\\n',
				'PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\\n',
				'Last-Translator: FULL NAME <EMAIL@ADDRESS>\\n',
				'Language-Team: LANGUAGE <LL@li.org>\\n',
				'MIME-Version: 1.0\\n',
				'X-Poedit-Basepath: ' . $packageInfo->potfile2root . '\\n',
				'X-Poedit-SourceCharset: UTF-8\n',
				'Content-Type: text/plain; charset=UTF-8\\n',
				'Content-Transfer-Encoding: 8bit\\n',
				'Language: \\n'
			),
			array('#, fuzzy')
		);
	}
	public function FixFilesSlash() {
		foreach($this->Entries as $entry) {
			$entry->FixFilesSlash();
		}
	}
	public function Replace_CRLF_LF() {
		if($this->Header) {
			$this->Header->Replace_CRLF_LF();
		}
		foreach($this->Entries as $entry) {
			$entry->Replace_CRLF_LF();
		}
	}
	public function MergeEntries($entries) {
		if(is_array($entries)) {
			foreach($entries as $entry) {
				$this->MergeEntries($entry);
			}
		} else {
			$hash = $entries->GetHash();
			if(array_key_exists($hash, $this->Entries)) {
				$this->Entries[$hash]->MergeWith($entries);
			} else {
				$this->Entries[$hash] = $entries;
			}
		}
	}
	public function SaveAs($filename, $indent = false) {
		$tempFilename = Enviro::GetTemporaryFileName();
		try {
			if(!$hFile = @fopen($tempFilename, 'wb')) {
				global $php_errormsg;
				throw new Exception("Error opening '$tempFilename': $php_errormsg");
			}
			try {
				$isFirst = true;
				if($this->Header) {
					$this->Header->SaveTo($hFile, $indent);
					$isFirst = false;
				}
				foreach($this->Entries as $entry) {
					if($isFirst) {
						$isFirst = false;
					} else {
						fwrite($hFile, "\n");
					}
					$entry->SaveTo($hFile, $indent);
				}
				fflush($hFile);
			} catch(Exception $x) {
				@fclose($hFile);
				throw $x;
			}
			fclose($hFile);
			$dirname = dirname($filename);
			if(is_file($dirname)) {
				throw new Exception("we'd like to use the folder '$dirname', but it's a file!");
			}
			if(!is_dir($dirname)) {
				if(!@mkdir($dirname, 0777, true)) {
					throw new Exception("unable to create the folder '$dirname'!");
				}
			}
			if(!is_writable($dirname)) {
				throw new Exception("the folder '$dirname' is not writable!");
			}
			if(is_file($filename)) {
				if(!is_writable($filename)) {
					throw new Exception("the file $filename is not writable!");
				}
				if(@unlink($filename) === false) {
					global $php_errormsg;
					throw new Exception("error deleting $filename: $php_errormsg");
				}
			}
			rename($tempFilename, $filename);
		} catch(Exception $x) {
			@unlink($tempFilename);
			throw $x;
		}
	}
}

/** Various po-related functions. */
class POFile extends POTFile {

}

/** Holds single translations. */
class POEntry {
	public $comments;
	public $msgctxt;
	public $msgid;

	public function getMsgCtx() {
		return self::ArrayToString($this->msgctxt);
	}
	public function getMsgID() {
		return self::ArrayToString($this->msgid);
	}
	protected static function ArrayToString($array) {
		if(empty($array)) {
			return '';
		}
		return implode($array);
	}
	protected static function Replace_CRLF_LF_in(&$array) {
		if(is_array($array)) {
			foreach(array_keys($array) as $key) {
				$array[$key] = str_replace('\\r\\n', '\\n', $array[$key]);
			}
		}
	}
	protected function Replace_CRLF_LF_msgid() {
		self::Replace_CRLF_LF_in($this->msgid);
	}
	protected static function ArrayToFile($hFile, $name, $array, $indent = false, $skipIfEmptyArray = false) {
		if($indent) {
			if(strlen($name) < 7) {
				$name .= str_repeat(' ', 7 - strlen($name));
			}
			$pre = str_repeat(' ', strlen($name) + 1);
		} else {
			$pre = '';
		}
		if(empty($array)) {
			if(!$skipIfEmptyArray) {
				fwrite($hFile, "$name \"\"\n");
			}
		} else {
			$first = true;
			foreach($array as $line) {
				if($first) {
					fwrite($hFile, "$name \"$line\"\n");
					$first = false;
				} else {
					fwrite($hFile, "$pre\"$line\"\n");
				}
			}
		}
	}
	public function isFuzzy() {
		foreach($this->comments as $comment) {
			if(strpos($comment, '#,') === 0) {
				if(strpos($comment, 'fuzzy') !== 0) {
					return true;
				}
			}
		}
		return false;
	}
	public function FixFilesSlash() {
		$n = count($this->comments);
		for($i = 0; $i < $n; $i++) {
			if(strpos($this->comments[$i], '#: ') === 0) {
				$this->comments[$i] = str_replace('\\', '/', $this->comments[$i]);
			}
		}
	}
	protected function __construct($comments, $msgctxt, $msgid) {
		$this->comments = is_array($comments) ? $comments : (strlen($comments) ? array($comments) : array());
		$this->msgctxt = is_array($msgctxt) ? $msgctxt : (strlen($msgctxt) ? array($msgctxt) : array());
		$this->msgid = is_array($msgid) ? $msgid : (strlen($msgid) ? array($msgid) : array());
	}
	private static function CommentsSorter($a, $b) {
		if((strlen($a) < 2) || (strlen($b) < 2)) {
			return 0;
		}
		/* ORDER:
		#  translator-comments
		#. extracted-comments
		#: reference...
		#, flag...
		#| msgid previous-untranslated-string
		*/
		$order = ' .:,|';
		$delta = strpos($order, $a[1]) - strpos($order, $b[1]);
		if($delta) {
			return $delta;
		}
		switch($a[1]) {
			case ':':
				if(preg_match('/^#:[ \t]+(.*):([\d]+)$/', $a, $cA) && preg_match('/^#:[ \t]+(.*):([\d]+)$/', $b, $cB)) {
					$fd = strcasecmp($cA[1], $cB[1]);
					if(!$fd) {
						$fd = intval($cA[2]) - intval($cB[2]);
					}
					return $fd;
				} else {
					return strcasecmp($a, $b);
				}
			default:
				return 0;
		}
	}
	protected function _saveTo($hFile, $indent = false) {
		usort($this->comments, array(__CLASS__, 'CommentsSorter'));
		foreach($this->comments as $comment) {
			fwrite($hFile, $comment);
			fwrite($hFile, "\n");
		}
		self::ArrayToFile($hFile, 'msgctxt', $this->msgctxt, $indent, true);
	}
	public static function FromXmlFile($xmlFiles) {
		if(is_array($xmlFiles)) {
			$result = array();
			foreach($xmlFiles as $xmlFile) {
				foreach(self::FromXmlFile($xmlFile) as $poEntry) {
					$hash = $poEntry->GetHash();
					if(array_key_exists($hash, $result)) {
						$result[$hash]->MergeWith($poEntry);
					} else {
						$result[$hash] = $poEntry;
					}
				}
			}
			return array_values($result);
		} else {
			global $options;
			$filenameRel = $xmlFiles;
			$filenameAbs = Enviro::MergePath(Options::$webroot, $xmlFiles);
			$xml = new DOMDocument();
			if(!@$xml->load($filenameAbs)) {
				global $php_errormsg;
				throw new Exception("Error loading '$filename': $php_errormsg");
			}
			$entries = array();
			switch($xml->documentElement->tagName) {
				case 'concrete5-cif':
					self::ParseXmlNode($filenameRel, '', $xml->documentElement, $entries);
					break;
				case 'schema':
				case 'access':
					break;
				default:
					throw new Exception('Unknown root node: ' . $xml->documentElement->tagName . ' in ' . $filenameRel);
			}
			return $entries;
		}
	}
	private static function ParseXmlNode($filenameRel, $prePath, $node, &$entries) {
		switch(get_class($node)) {
			case 'DOMElement':
				break;
			case 'DOMText':
			case 'DOMCdataSection':
			case 'DOMComment':
				return;
			default:
				throw new Exception(get_class($node) . ' in ' . $filenameRel);
		}
		$path = $prePath . '/' . $node->tagName;
		switch($path) {
			case '/concrete5-cif':
			case '/concrete5-cif/attributecategories':
			case '/concrete5-cif/attributecategories/category':
			case '/concrete5-cif/attributetypes':
			case '/concrete5-cif/attributetypes/attributetype/categories':
			case '/concrete5-cif/attributetypes/attributetype/categories/category':
			case '/concrete5-cif/attributekeys':
			case '/concrete5-cif/attributekeys/attributekey/type':
			case '/concrete5-cif/attributekeys/attributekey/type/options';
			case '/concrete5-cif/attributekeys/attributekey/type/options/option';
			case '/concrete5-cif/attributesets':
			case '/concrete5-cif/attributesets/attributeset/attributekey':
			case '/concrete5-cif/blocktypes':
			case '/concrete5-cif/blocktypes/blocktype':
			case '/concrete5-cif/singlepages':
			case '/concrete5-cif/singlepages/page/attributes':
			case '/concrete5-cif/singlepages/page/attributes/attributekey':
			case '/concrete5-cif/singlepages/page/attributes/attributekey/value':
			case '/concrete5-cif/pagetypes':
			case '/concrete5-cif/pages':
			case '/concrete5-cif/pages/page/attributes':
			case '/concrete5-cif/pages/page/attributes/attributekey':
			case '/concrete5-cif/pages/page/attributes/attributekey/value':
			case '/concrete5-cif/pages/page/attributes/attributekey/value/option':
			case '/concrete5-cif/pages/page/area/block/data':
			case '/concrete5-cif/pages/page/area/block/stack':
			case '/concrete5-cif/jobs':
			case '/concrete5-cif/jobs/job':
			case '/concrete5-cif/singlepages/page/area':
			case '/concrete5-cif/permissioncategories':
			case '/concrete5-cif/permissioncategories/category':
			case '/concrete5-cif/permissionkeys':
			case '/concrete5-cif/permissionkeys/permissionkey/access':
			case '/concrete5-cif/permissionkeys/permissionkey/access/group':
			case '/concrete5-cif/systemcaptcha':
			case '/concrete5-cif/workflowtypes':
			case '/concrete5-cif/permissionaccessentitytypes':
			case '/concrete5-cif/permissionaccessentitytypes/permissionaccessentitytype/categories':
			case '/concrete5-cif/permissionaccessentitytypes/permissionaccessentitytype/categories/category':
			case '/concrete5-cif/workflowprogresscategories':
			case '/concrete5-cif/workflowprogresscategories/category':
			case '/concrete5-cif/themes':
			case '/concrete5-cif/themes/theme':
			case '/concrete5-cif/taskpermissions':
			case '/concrete5-cif/taskpermissions/taskpermission/access':
			case '/concrete5-cif/taskpermissions/taskpermission/access/group':
			case '/concrete5-cif/stacks':
			case '/concrete5-cif/stacks/stack/area/block/data';
			case '/concrete5-cif/pagetypes/pagetype/page/area/block/data':
			case '/concrete5-cif/pagetypes/pagetype/composer':
			case '/concrete5-cif/pagetypes/pagetype/composer/items':
			case '/concrete5-cif/pagetypes/pagetype/composer/items/attributekey':
			case '/concrete5-cif/pagetypes/pagetype/page/attributes':
			case '/concrete5-cif/pagetypes/pagetype/page/attributes/attribute':
			case '/concrete5-cif/pagetypes/pagetype/page/attributes/attribute':
			case '/concrete5-cif/pagetypes/pagetype/page/attributes/attributekey';
				break;
			case '/concrete5-cif/config':
			case '/concrete5-cif/stacks/stack/area/block/data/record';
			case '/concrete5-cif/pagetypes/pagetype/page/area/block/data/record':
			case '/concrete5-cif/pages/page/area/block/data/record':
				return;
			case '/concrete5-cif/attributetypes/attributetype':
			case '/concrete5-cif/attributekeys/attributekey':
			case '/concrete5-cif/attributesets/attributeset':
			case '/concrete5-cif/pagetypes/pagetype':
			case '/concrete5-cif/pages/page/area':
			case '/concrete5-cif/pages/page/area/block':
			case '/concrete5-cif/systemcaptcha/library':
			case '/concrete5-cif/workflowtypes/workflowtype':
			case '/concrete5-cif/permissionaccessentitytypes/permissionaccessentitytype':
			case '/concrete5-cif/stacks/stack';
			case '/concrete5-cif/stacks/stack/area';
			case '/concrete5-cif/stacks/stack/area/block';
			case '/concrete5-cif/pagetypes/pagetype/page/area':
			case '/concrete5-cif/pagetypes/pagetype/page/area/block':
			case '/concrete5-cif/pagetypes/pagetype/composer/items/block':
				self::ReadNodeAttribute($filenameRel, $node, 'name', $entries);
				break;
			case '/concrete5-cif/singlepages/page':
			case '/concrete5-cif/pages/page':
			case '/concrete5-cif/singlepages/page':
			case '/concrete5-cif/permissionkeys/permissionkey':
			case '/concrete5-cif/taskpermissions/taskpermission':
			case '/concrete5-cif/pagetypes/pagetype/page':
				self::ReadNodeAttribute($filenameRel, $node, 'name', $entries);
				self::ReadNodeAttribute($filenameRel, $node, 'description', $entries);
				break;
			default:
				throw new Exception('Unknown tag name ' . $path . ' in ' . $filenameRel);
		}
		if($node->hasChildNodes()) {
			foreach($node->childNodes as $child) {
				self::ParseXmlNode($filenameRel, $path, $child, $entries);
			}
		}
	}
	private static function ReadNodeAttribute($filenameRel, $node, $attributeName, &$entries) {
		$value = $node->getAttribute($attributeName);
		if(strlen($value)) {
			if(!array_key_exists($value, $entries)) {
				$entries[$value] = new POEntrySingle($value);
			}
			$entries[$value]->comments[] = '#: ' . str_replace('\\', '/', $filenameRel) . ':' . $node->getLineNo();
		}
	}
	public function MergeWith($poEntry) {
		$this->comments = array_merge($this->comments, $poEntry->comments);
	}
	/**
	* @param [string] $lines
	* @param int $start
	* @param int $nextStart
	* @throws Exception
	* @return POEntry|null
	*/
	public static function GetNextFromLines($lines, $start, &$nextStart) {
		$n = count($lines);
		$nextStart = -1;
		$comments = array();
		$msgctxtIndex = -1;
		$msgidIndex = -1;
		$msgid_pluralIndex = -1;
		$msgstrIndex = -1;
		$msgstr_pluralIndexes = array();
		for($i = $start; ($i < $n) && ($nextStart < 0); $i++) {
			$trimmedLine = trim($lines[$i]);
			if(strlen($trimmedLine)) {
				if($trimmedLine[0] == '#') {
					$lineType = 'comment';
				} elseif(preg_match('/^msgctxt($|\s|")/', $trimmedLine)) {
					$lineType = 'msgctxt';
				} elseif(preg_match('/^msgid($|\s|")/', $trimmedLine)) {
					$lineType = 'msgid';
				} elseif(preg_match('/^msgid_plural($|\s|")/', $trimmedLine)) {
					$lineType = 'msgid_plural';
				} elseif(preg_match('/^msgstr($|\s|")/', $trimmedLine)) {
					$lineType = 'msgstr';
				} elseif(preg_match('/^msgstr\[[0-9]+\]/', $trimmedLine)) {
					$lineType = 'msgstr_plural';
				} elseif(preg_match('/^".*"$/', $trimmedLine)) {
					$lineType = 'text';
				} else {
					throw new Exception("Invalid content '$trimmedLine' at line " . ($i + 1) . ".");
				}
				switch($lineType) {
					case 'comment':
						if(($msgstrIndex >= 0) || count($msgstr_pluralIndexes)) {
							$nextStart = $i;
						} else if($msgidIndex < 0) {
							while(preg_match('/^(#:[ \t].*?:\d+)[ \t]+([^ \t].*:\d.*)$/', $trimmedLine, $chunks)) {
								$comments[] = $chunks[1];
								$trimmedLine = '#: ' . trim($chunks[2]);
							}
							$comments[] = $trimmedLine;
						} else {
							throw new Exception("Misplaced '$trimmedLine' (linetype: $lineType) at line " . ($i + 1) . ".");
						}
						break;
					case 'msgctxt':
						if(($msgstrIndex >= 0) || count($msgstr_pluralIndexes)) {
							$nextStart = $i;
						} else if(($msgctxtIndex < 0) && ($msgidIndex < 0)) {
							$msgctxtIndex = $i;
						} else {
							throw new Exception("Misplaced '$trimmedLine' (linetype: $lineType) at line " . ($i + 1) . ".");
						}
						break;
					case 'msgid':
						if(($msgstrIndex >= 0) || count($msgstr_pluralIndexes)) {
							$nextStart = $i;
						} else if($msgidIndex < 0) {
							$msgidIndex = $i;
						} else {
							throw new Exception("Misplaced '$trimmedLine' (linetype: $lineType) at line " . ($i + 1) . ".");
						}
						break;
					case 'msgid_plural':
						if(($msgidIndex >= 0) && ($msgid_pluralIndex < 0) && ($msgstrIndex < 0) && empty($msgstr_pluralIndexes)) {
							$msgid_pluralIndex = $i;
						} else {
							throw new Exception("Misplaced '$trimmedLine' (linetype: $lineType) at line " . ($i + 1) . ".");
						}
						break;
					case 'msgstr':
						if(($msgidIndex >= 0) && ($msgstrIndex < 0) && empty($msgstr_pluralIndexes)) {
							$msgstrIndex = $i;
						} else {
							throw new Exception("Misplaced '$trimmedLine' (linetype: $lineType) at line " . ($i + 1) . ".");
						}
						break;
					case 'msgstr_plural':
						if(($msgidIndex >= 0) && ($msgid_pluralIndex >= 0) && ($msgstrIndex < 0)) {
							$msgstr_pluralIndexes[] = $i;
						} else {
							throw new Exception("Misplaced '$trimmedLine' (linetype: $lineType) at line " . ($i + 1) . ".");
						}
						break;
					case 'text':
						if($msgidIndex < 0) {
							throw new Exception("Misplaced '$trimmedLine' (linetype: $lineType) at line " . ($i + 1) . ".");
						}
						break;
					default:
						throw new Exception("Not implemented line type: $lineType");
				}
			}
		}
		if($nextStart < 0) {
			$nextStart = $n;
		}
		if((!empty($comments)) || ($msgctxtIndex >= 0) || ($msgidIndex >= 0) || ($msgid_pluralIndex >= 0) || ($msgstrIndex >= 0) || count($msgstr_pluralIndexes)) {
			if($msgidIndex < 0) {
				throw new Exception("Missing msgid for entry starting at line " . ($i + 1) . ".");
			}
			if($msgid_pluralIndex >= 0) {
				if(($msgstrIndex >= 0) || empty($msgstr_pluralIndexes)) {
					throw new Exception("Expected plural msgstr for entry starting at line " . ($i + 1) . ".");
				}
				return POEntryPlural::FromLines($lines, $comments, $msgctxtIndex, $msgidIndex, $msgid_pluralIndex, $msgstr_pluralIndexes);
			} else {
				if(($msgstrIndex < 0) || count($msgstr_pluralIndexes)) {
					throw new Exception("Expected singular msgstr for entry starting at line " . ($i + 1) . ".");
				}
				return POEntrySingle::FromLines($lines, $comments, $msgctxtIndex, $msgidIndex, $msgstrIndex);
			}
		} else {
			return null;
		}
	}
}

class POEntrySingle extends POEntry {
	public $msgstr;
	public function __construct($msgid, $msgstr = array(), $comments = array(), $msgctxt = array()) {
		parent::__construct($comments, $msgctxt, $msgid);
		$this->msgstr = is_array($msgstr) ? $msgstr : (strlen($$msgstr) ? array($msgstr) : array());
	}
	public static function FromLines($lines, $comments, $msgctxtIndex, $msgidIndex, $msgstrIndex) {
		$values = array(array(), array(), array());
		$n = count($lines);
		foreach(array($msgctxtIndex, $msgidIndex, $msgstrIndex) as $valueIndex => $i) {
			if($i < 0) {
				continue;
			}
			for($j = $i; $j < $n; $j++) {
				if(preg_match(($j == $i) ? '/"(.*)"[ \t]*$/' : '/^[ \t]*"(.*)"[ \t]*$/', $lines[$j], $matches)) {
					$values[$valueIndex][] = $matches[1];
				} else {
					break;
				}
			}
		}
		$className = __CLASS__;
		return new $className($values[1], $values[2], $comments, $values[0]);
	}
	public function Replace_CRLF_LF() {
		parent::Replace_CRLF_LF_msgid();
	}
	public function SaveTo($hFile, $indent = false) {
		parent::_saveTo($hFile, $indent);
		self::ArrayToFile($hFile, 'msgid', $this->msgid, $indent);
		self::ArrayToFile($hFile, 'msgstr', $this->msgstr, $indent);
	}
	public function GetHash() {
		return $this->getMsgCtx() . chr(1) . $this->getMsgID();
	}
}

class POEntryPlural extends POEntry {
	public $msgid_plural;
	public $msgstr;
	public function __construct($msgid, $msgid_plural, $msgstr = array(), $comments = array(), $msgctxt = array()) {
		parent::__construct($comments, $msgctxt, $msgid);
		$this->msgid_plural = is_array($msgid_plural) ? $msgid_plural : (strlen($msgid_plural) ? array($msgid_plural) : array());
		$this->msgstr = is_array($msgstr) ? $msgstr : array();
	}
	public function getMsgID_Plural() {
		return self::ArrayToString($this->msgid);
	}
	public static function FromLines($lines, $comments, $msgctxtIndex, $msgidIndex, $msgid_pluralIndex, $msgstr_pluralIndexes) {
		$n = count($lines);
		$multiValues = array();
		$pluralCount = 0;
		for($k = 0; $k < 2; $k++) {
			switch($k) {
				case 0:
					$indexes = array($msgctxtIndex, $msgidIndex, $msgid_pluralIndex);
					$multiValues[$k] = array(array(), array(), array());
					break;
				case 1:
					$indexes = $msgstr_pluralIndexes;
					$multiValues[$k] = array();
					for($j = 0; $j < count($msgstr_pluralIndexes); $j++) {
						$multiValues[$k][$j] = array();
					}
					break;
			}
			foreach($indexes as $valueIndex => $i) {
				if($i < 0) {
					continue;
				}
				for($j = $i; $j < $n; $j++) {
					if(preg_match(($j == $i) ? '/"(.*)"[ \t]*$/' : '/^[ \t]*"(.*)"[ \t]*$/', $lines[$j], $matches)) {
						$multiValues[$k][$valueIndex][] = $matches[1];
						if(($k == 1) && ($j = $i)) {
							if(!preg_match('/^[ \t]*msgstr\[(\d)+\]/', $lines[$j], $matches)) {
								throw new Exception("Bad msgstr '{$lines[$j]}' at line " . ($j + 1));
							}
							$msgstr_index = @intval($matches[1]);
							if($pluralCount !== $msgstr_index) {
								throw new Exception("Bad msgstr '{$lines[$j]}' at line " . ($j + 1) . " (expected index $pluralCount).");
							}
							$pluralCount++;
						}
					} else {
						break;
					}
				}
			}
		}
		$className = __CLASS__;
		return new $className($multiValues[0][1], $multiValues[0][2], $multiValues[1], $comments, $multiValues[0][0]);
	}
	public function Replace_CRLF_LF() {
		parent::Replace_CRLF_LF_msgid();
		self::Replace_CRLF_LF_in($this->msgid_plural);
	}
	public function SaveTo($hFile, $indent = false) {
		parent::_saveTo($hFile, $indent = false);
		self::ArrayToFile($hFile, 'msgid', $this->msgid, $indent);
		self::ArrayToFile($hFile, 'msgid_plural', $this->msgid_plural, $indent);
		for($i = 0; $i < count($this->msgstr); $i++) {
			self::ArrayToFile($hFile, "msgstr[$i]", $this->msgstr[$i], $indent);
		}
	}
	public function GetHash() {
		return $this->getMsgCtx() . chr(1) . $this->getMsgID() . chr(2) . $this->getMsgID_Plural();
	}
}

class Languages {
	private function getAll() {
		// Plurals: from http://translate.sourceforge.net/wiki/l10n/pluralforms
		return array(
			'ar' => array('name' => 'Arabic', 'plural' => 'nplurals=6; plural= n==0 ? 0 : n==1 ? 1 : n==2 ? 2 : n%100>=3 && n%100<=10 ? 3 : n%100>=11 ? 4 : 5;'),
			'bg' => array('name' => 'Bulgarian', 'plural' => 'nplurals=2; plural=(n != 1)'),
			'ca' => array('name' => 'Catalan', 'plural' => 'nplurals=2; plural=(n != 1)'),
			'cs_CZ' => array('name' => 'Czech', 'country' => 'Czech Republic', 'plural' => 'nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4) ? 1 : 2'),
			'da_DK' => array('name' => 'Danish', 'country' => 'Denmark', 'plural' => 'nplurals=2; plural=(n != 1)'),
			'de_DE' => array('name' => 'German', 'country' => 'Germany', 'plural' => 'nplurals=2; plural=(n != 1)'),
			'en' => array('name' => 'English', 'plural' => 'nplurals=2; plural=(n != 1)'),
			'es_ES' => array('name' => 'Spanish', 'country' => 'Spain', 'plural' => 'nplurals=2; plural=(n != 1)'),
			'es_PE' => array('name' => 'Spanish', 'country' => 'Peru', 'plural' => 'nplurals=2; plural=(n != 1)'),
			'et' => array('name' => 'Estonian', 'plural' => 'nplurals=2; plural=(n != 1)'),
			'fi_FI' => array('name' => 'Finnish', 'country' => 'Finland', 'plural' => 'nplurals=2; plural=(n != 1)'),
			'fr_FR' => array('name' => 'French', 'country' => 'France', 'plural' => 'nplurals=2; plural=(n > 1)'),
			'he_IL' => array('name' => 'Hebrew', 'country' => 'Israel', 'plural' => 'nplurals=2; plural=(n != 1)'),
			'hi' => array('name' => 'Hindi', 'plural' => 'nplurals=2; plural=(n != 1)'),
			'hr' => array('name' => 'Croatian', 'plural' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'),
			'hu_HU' => array('name' => 'Hungarian', 'country' => 'Hungary', 'plural' => 'nplurals=2; plural=(n != 1)'),
			'id_ID' => array('name' => 'Indonesian', 'country' => 'Indonesia', 'plural' => 'nplurals=1; plural=0'),
			'it_IT' => array('name' => 'Italian', 'country' => 'Italy', 'plural' => 'nplurals=2; plural=(n != 1)'),
			'ja_JP' => array('name' => 'Japanese', 'country' => 'Japan', 'plural' => 'nplurals=1; plural=0'),
			'ku' => array('name' => 'Kurdish', 'plural' => 'nplurals=2; plural=(n!= 1)'),
			'lt_LT' => array('name' => 'Lithuanian', 'country' => 'Lithuania', 'plural' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && (n%100<10 or n%100>=20) ? 1 : 2)'),
			'nl_NL' => array('name' => 'Dutch', 'country' => 'Netherlands', 'plural' => 'nplurals=2; plural=(n != 1)'),
			'no_NO' => array('name' => 'Norwegian', 'country' => 'Norway', 'plural' => 'nplurals=2; plural=(n != 1)'),
			'pl_PL' => array('name' => 'Polish', 'country' => 'Poland', 'plural' => 'nplurals=3; plural=(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'),
			'pt_BR' => array('name' => 'Portuguese', 'country' => 'Brazil', 'plural' => 'nplurals=2; plural=(n != 1)'),
			'pt_PT' => array('name' => 'Portuguese', 'country' => 'Portugal', 'plural' => 'nplurals=2; plural=(n != 1)'),
			'ro_RO' => array('name' => 'Romanian', 'country' => 'Romania', 'plural' => 'nplurals=3; plural=(n==1 ? 0 : (n==0 || (n%100 > 0 && n%100 < 20)) ? 1 : 2)'),
			'ru_RU' => array('name' => 'Russian', 'country' => 'Russia', 'plural' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'),
			'sv_SE' => array('name' => 'Swedish', 'country' => 'Sweden', 'plural' => 'nplurals=2; plural=(n != 1)'),
			'th_TH' => array('name' => 'Thai', 'country' => 'Thailand', 'plural' => 'nplurals=1; plural=0'),
			'tr' => array('name' => 'Turkish', 'plural' => 'nplurals=2; plural=(n>1)'),
			'zh_CN' => array('name' => 'Chinese', 'country' => 'China', 'plural' => 'nplurals=1; plural=0')
		);
	}
}
