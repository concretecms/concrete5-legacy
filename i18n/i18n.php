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
	if(Options::$interactive) {
		Options::InteractiveMenu_Main();
	}
	else {
		foreach(Options::$packages as $package) {
			if($package->createPot) {
				POTFile::CreateNew($package);
			}
			if($package->createPo) {
				foreach(Options::$languages as $language) {
					POFile::CreateNew($package, $language);
				}
			}
			if($package->compile) {
				foreach(Options::$languages as $language) {
					POFile::Compile($package, $language);
				}
			}
		}
	}
	@chdir(Options::$INITIAL_CD);
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
function ErrorCatcher($errno, $errstr, $errfile, $errline) {
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

	/** List of languages for which we have to create/update .po files. Each array item is an array with the keys <b>language</b> (required) and <b>country</b> (optional).
	* @var array
	*/
	public static $languages;

	/** We're in an interactive session?
	* @var bool
	*/
	public static $interactive;

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
			Enviro::RunTool('msgmerge', '--version');
			Enviro::RunTool('msgfmt', '--version');
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
						'bin/libgettextlib-0-18-1.dll' => self::$I18N_WIN32TOOLS,
						'bin/xgettext.exe' => self::$I18N_WIN32TOOLS,
						'bin/msgmerge.exe' => self::$I18N_WIN32TOOLS,
						'bin/msgfmt.exe' => self::$I18N_WIN32TOOLS
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
		Console::WriteLine('--help                      show this message');
		Console::WriteLine('--list-languages            list all the usable languages');
		Console::WriteLine('--list-countries            list all the usable countries');
		Console::WriteLine('--interactive               start an interactive session');
		Console::WriteLine('--webroot=<path>            set the web root of concrete5 (default: ' . self::$DEFAULT_WEBROOT . ')');
		Console::WriteLine('--indent=<yes|no>           set to yes to generate indented .pot/.po files, false for not-indented generation (default: ' . (self::$DEFAULT_INDENT ? 'yes' : 'no') . ')');
		Console::WriteLine('--languages=<LanguagesCode> list of comma-separated languages for which create the .po files (default: ' . implode(',', Language::GetStandardCodes()) . ')');
		Console::WriteLine('--package=<packagename>     adds a package. Subsequent arguments are relative to the latest package (or to concrete5 itself to ');
		Console::WriteLine();
		Console::WriteLine('For concrete5 and/or each package you can specify specific options. Before the first --package you\'re assigning options to the core of concrete5.');
		Console::WriteLine('Available package options:');
		Console::WriteLine('--createpot=<yes|no>        set to yes to generate the .pot file, no to skip it (defaults to yes, except for concrete5 when you\'ve specified a --package option)');
		Console::WriteLine('--createpo=<yes|no>         set to yes to generate the .po files, no to skip it (defaults to yes, except for concrete5 when you\'ve specified a --package option)');
		Console::WriteLine('--compile=<yes|no>          set to yes to generate the .mo files from .po files, no to skip it (defaults to yes, except for concrete5 when you\'ve specified a --package option)');
		Console::WriteLine('--potname=<filename>        name of the .pot filename (just the name, without path: it\'ll be saved in the \'languages\' folder)');
		Console::WriteLine('--excludedirfrompot=<dir>   folder which may not be parsed when creating a.pot file. To specify multiple values you can specify this argument more than once (default for concrete5: ' . implode(self::$DEFAULT_EXCLUDEDIRSFROMPOT_CONCRETE5), ', default for packages: ' . implode(self::$DEFAULT_EXCLUDEDIRSFROMPOT_CONCRETE5, ', ') . ')');
		Console::WriteLine('--potcontact=<email>        email address to send bugs to (for c5 the default is ' . self::$DEFAULT_POTCONTACT_CONCRETE5 . ' when working on concrete5, empty when working on a package.');
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
		$packagesMap = array();
		self::$packages[] = $packageInfo = new PackageInfo('');
		self::$languages = null;
		self::$interactive = false;
		$list = '';
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
				case '--list-languages':
				case '--list-countries':
					$list = substr($argument, strpos($argument, '-', 2) + 1);
					break;
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
				case '--interactive':
					self::$interactive = true;
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
					if(is_null(self::$packages[0]->createPo)) {
						self::$packages[0]->createPo = false;
					}
					if(is_null(self::$packages[0]->compile)) {
						self::$packages[0]->compile = false;
					}
					if(array_key_exists($value, $packagesMap)) {
						$packageInfo = self::$packages[$packagesMap[$value]];
					}
					else {
						$packagesMap[$value] = count(self::$packages);
						self::$packages[] = $packageInfo = new PackageInfo($value);
					}
					break;
				case '--createpot':
					$packageInfo->createPot = self::ArgumentToBool($argument, $value);
					break;
				case '--createpo':
					$packageInfo->createPo = self::ArgumentToBool($argument, $value);
					break;
				case '--compile':
					$packageInfo->compile = self::ArgumentToBool($argument, $value);
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
				case '--languages':
					if(is_null(self::$languages)) {
						self::$languages = array();
					}
					if(!strlen($value)) {
						throw new Exception("Argument '$argument' requires a value.");
					}
					foreach(explode(',', $value) as $v1) {
						if(!strlen($v1)) {
							throw new Exception("Argument '$argument' requires values separated by comma.");
						}
						$v1n = Language::NormalizeCode($v1);
						if(array_search($v1n, self::$languages) === false) {
							self::$languages[] = Language::NormalizeCode($v1);
						}
					}
					break;
				default:
					Console::WriteLine("Invalid argument '$argument'", true);
					self::ShowHelp(true);
					die(1);
			}
		}
		if(is_null(self::$languages)) {
			self::$languages = Language::GetStandardCodes();
		}
		sort(self::$languages);
		if(self::$interactive) {
			return;
		}
		switch($list) {
			case 'languages':
				foreach(Language::GetLanguages() as $id => $info) {
					Console::WriteLine("$id\t{$info['name']}");
				}
				die(0);
			case 'countries':
				foreach(Language::GetCountries() as $id => $info) {
					Console::WriteLine("$id\t{$info['name']}");
				}
				die(0);
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
		$bool = self::StringToBool($argumentValue);
		if(is_null($bool)) {
			throw new Exception("Argument '$argumentName' requires a boolean value (yes or no), given '$argumentValue'.");
		}
		return $bool;
	}

	/** Convert a string into boolean (return null in case of conversion error).
	* @param string $value The value to be analyzed
	* @return boolean|null
	*/
	private static function StringToBool($value) {
		$v = @trim($value);
		if(strlen($v)) {
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
			}
		}
		return null;
	}

	/** Download a file and extract files to a local path.
	* @param string $url The url of the file to download.
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

	/** Print out the menu of an interactive menu.
	* @param string $title The menu title.
	*/
	private static function ShowInteractiveMenuTitle($title) {
		$l = strlen($title);
		Console::WriteLine();
		Console::WriteLine(' -' . str_repeat('-', $l) . '- ');
		Console::WriteLine('| ' . $title . ' |');
		Console::WriteLine(' -' . str_repeat('-', $l) . '- ');
	}

	/** Print out an item of an interactive menu.
	* @param string $key The key associated to the item.
	* @param string $text The description of the item.
	* @param string $note [optional] A note about the item.
	*/
	private static function ShowInteractiveMenuEntry($key, $text, $note = '') {
		Console::WriteLine("  $key: $text");
		if(strlen($note)) {
			Console::WriteLine("     $note");
		}
	}

	/** Read the user choice for an interactive menu.
	* @var array[string] $validOptions List of valid options.
	* @return string
	*/
	private static function AskInteractiveMenuOption($validOptions) {
		Console::WriteLine();
		Console::Write('  Option: ');
		for(;;) {
			$option = trim(Console::ReadLine());
			if(!strlen($option)) {
				Console::Write('  Please specify an option: ');
			}
			else {
				foreach($validOptions as $validOption) {
					if(strcasecmp($validOption, $option) === 0) {
						return $validOption;
					}
				}
				Console::Write('  Please a valid option: ');
			}
		}
	}

	/** Shows the main interactive menu. */
	public static function InteractiveMenu_Main() {
		for(;;) {
			$validOptions = array();
			self::ShowInteractiveMenuTitle('MAIN MENU');
			self::ShowInteractiveMenuEntry($validOptions[] = 'C', 'Work on concrete5 core');
			self::ShowInteractiveMenuEntry($validOptions[] = 'P', 'Work on a concrete5 package');
			self::ShowInteractiveMenuEntry($validOptions[] = 'W', 'Change webroot', 'Current value: ' . Options::$webroot);
			self::ShowInteractiveMenuEntry($validOptions[] = 'I', 'Change indentation', 'Current value: ' . (Options::$indent ? 'yes' : 'no'));
			self::ShowInteractiveMenuEntry($validOptions[] = 'L', 'Change .po languages', 'Current value: ' . count(Options::$languages) . ' language' . ((count(Options::$languages) == 1) ? '' : 's'));
			self::ShowInteractiveMenuEntry($validOptions[] = 'X', 'Exit');
			switch(self::AskInteractiveMenuOption($validOptions)) {
				case 'C':
					try {
						PackageInfo::GetVersionOfConcrete5(Options::$webroot);
					}
					catch(Exception $x) {
						Console::Write('Please fix the webroot!');
						break;
					}
					self::InteractiveMenu_Package('');
					break;
				case 'P':
					try {
						PackageInfo::GetVersionOfConcrete5(Options::$webroot);
					}
					catch(Exception $x) {
						Console::Write('Please fix the webroot!');
						break;
					}
					$available = array();
					$parent = Enviro::MergePath(Options::$webroot, 'packages');
					if(is_dir($parent)) {
						if(!($hDir = @opendir($parent))) {
							Console::WriteLine('Unable to read directory \'' . $parent . '\.');
							break;
						}
						while($item = readdir($hDir)) {
							switch($item) {
								case '.':
								case '..':
									break;
								default:
									if(is_dir(Enviro::MergePath($parent, $item))) {
										$available[] = $item;
									}
									break;
							}
						}
						closedir($hDir);
					}
					if(empty($available)) {
						Console::Write('No packages found in \'' . $parent . '\.');
						break;
					}
					$package = '';
					for(;;) {
						Console::Write('Enter new package name [? for pick it]: ');
						$s = trim(Console::ReadLine());
						if(!strlen($s)) {
							break;
						}
						if($s == '?') {
							foreach($available as $index => $name) {
								Console::WriteLine(($index + 1) . ": $name");
							}
							for(;;) {
								Console::Write('Enter package index: ');
								$i = trim(Console::ReadLine());
								if(!strlen($i)) {
									break;
								}
								if(!preg_match('/^[1-9][0-9]*$/', $i)) {
									$i = -1;
								}
								else {
									$i = intval($i) - 1;
									if(!array_key_exists($i, $available)) {
										$i = -1;
									}
								}
								if($i < 0) {
									Console::WriteLine('Invalid option!');
									continue;
								}
								$package = $available[$i];
								break;
							}
							break;
						}
						else {
							if(Enviro::IsFilenameWithoutPath($s)) {
								if(Enviro::FilesAreCaseInsensitive()) {
									foreach($available as $a) {
										if(strcasecmp($a, $s) === 0) {
											$package = $a;
											break;
										}
									}
								} else {
									if(array_search($s, $available) !== false) {
										$package = $s;
									}
								}
							}
							if(!strlen($package)) {
								Console::WriteLine('Invalid package name.');
							}
							else
							{
								break;
							}
						}
					}
					if(!strlen($package)) {
						Console::WriteLine('Skipped.');
					}
					else {
						try {
							PackageInfo::GetVersionOfPackage(Options::$webroot, $package);
						}
						catch(Exception $x) {
							Console::Write($x);
							break;
						}
						self::InteractiveMenu_Package($package);
					}
					break;
				case 'W':
					for(;;) {
						Console::Write('Enter new webroot path: ');
						$s = str_replace('\\', '/', trim(Console::ReadLine()));
						if(!strlen($s)) {
							Console::WriteLine('Skipped.');
							break;
						}
						if(preg_match('/^\\.\\.?\/?/', $s)) {
							$s = Enviro::MergePath(Options::$INITIAL_CD, $s);
						}
						else {
							$s = Enviro::MergePath($s);
						}
						$r = @realpath($s);
						if(($r === false) || (!is_dir($r))) {
							Console::WriteLine('The folder \'' . $s . '\' does not exist.');
						}
						else {
							try {
								PackageInfo::GetVersionOfConcrete5($r);
							}
							catch(Exception $x) {
								Console::WriteLine($x->getMessage());
								continue;
							}
							Options::$webroot = $r;
							break;
						}
					}
					break;
				case 'I':
					for(;;) {
						Console::Write('Enter new intent value [Y/N]: ');
						$s = trim(Console::ReadLine());
						if(!strlen($s)) {
							Console::WriteLine('Skipped.');
							break;
						}
						else {
							$b = self::StringToBool($s);
							if(is_null($b)) {
								Console::WriteLine('Invalid value!');
							}
							else {
								self::$indent = $b;
								break;
							}
						}
					}
					break;
				case 'L':
					self::InteractiveMenu_Languages();
					break;
				case 'X':
					return;
			}
		}
	}

	/** Shows the interactive menu for working on concrete5 core or on a package.
	* @param string $package Empty string for working on concrete5 core, the package name for working on a package.
	*/
	private static function InteractiveMenu_Package($package) {
		$packageInfo = new PackageInfo($package);
		$packageInfo->createPo = false;
		$packageInfo->createPot = false;
		$packageInfo->compile = false;
		$packageInfo->postInitialize();
		for(;;) {
			$validOptions = array();
			self::ShowInteractiveMenuTitle('WORK ON ' . (strlen($package) ? "PACKAGE $package" : 'concrete5 core') . ' v' . $packageInfo->version);
			self::ShowInteractiveMenuEntry($validOptions[] = 'T', 'Create .pot template');
			self::ShowInteractiveMenuEntry($validOptions[] = 'P', 'Create .po language files');
			self::ShowInteractiveMenuEntry($validOptions[] = 'C', 'Compile .po language files into .mo files');
			self::ShowInteractiveMenuEntry($validOptions[] = 'X', 'Back to main menu');
			switch(self::AskInteractiveMenuOption($validOptions)) {
				case 'T':
					try {
						POTFile::CreateNew($packageInfo);
					}
					catch(Exception $x) {
						Console::WriteLine();
						Console::WriteLine( $x->getMessage());
						Console::Write('Press [RETURN] to continue');
						Console::ReadLine();
					}
					break;
				case 'P':
					if(empty(Options::$languages)) {
						Console::WriteLine('No languages to create/update .po files for!');
					}
					else {
						foreach(Options::$languages as $language) {
							try {
								POFile::CreateNew($packageInfo, $language);
							}
							catch(Exception $x) {
								Console::WriteLine();
								Console::WriteLine( $x->getMessage());
								Console::Write('Press [RETURN] to continue');
								Console::ReadLine();
							}
						}
					}
					break;
				case 'C':
					if(empty(Options::$languages)) {
						Console::WriteLine('No languages to create .mo files for!');
					}
					else {
						foreach(Options::$languages as $language) {
							try {
								POFile::Compile($packageInfo, $language);
							}
							catch(Exception $x) {
								Console::WriteLine();
								Console::WriteLine( $x->getMessage());
								Console::Write('Press [RETURN] to continue');
								Console::ReadLine();
							}
						}
					}
					break;
				case 'X':
					return;
			}
		}
	}

	/** Show the languages-management menu. */
	private static function InteractiveMenu_Languages() {
		for(;;) {
			$validOptions = array();
			self::ShowInteractiveMenuTitle('LANGUAGES MENU');
			self::ShowInteractiveMenuEntry($validOptions[] = '?', 'Show current language list');
			self::ShowInteractiveMenuEntry($validOptions[] = 'E', 'Empty current language list');
			self::ShowInteractiveMenuEntry($validOptions[] = 'A', 'Add a language to the list');
			self::ShowInteractiveMenuEntry($validOptions[] = 'R', 'Remove a language from list');
			self::ShowInteractiveMenuEntry($validOptions[] = 'L', 'Show all available languages');
			self::ShowInteractiveMenuEntry($validOptions[] = 'C', 'Show all available countries');
			self::ShowInteractiveMenuEntry($validOptions[] = 'X', 'back to main menu');
			switch(self::AskInteractiveMenuOption($validOptions)) {
				case '?':
					if(empty(Options::$languages)) {
						Console::WriteLine('No current languages');
					}
					else {
						foreach(Options::$languages as $code) {
							Console::WriteLine($code . "\t" . Language::DescribeCode($code));
						}
					}
					break;
				case 'E':
					Options::$languages = array();
					Console::WriteLine('List cleared.');
					break;
				case 'A':
					Console::Write('Language code to add (LL or LL_CC): ');
					$code = trim(Console::ReadLine());
					if(!strlen($code)) {
						Console::WriteLine('Skipped.');
					}
					else {
						try {
							$code = Language::NormalizeCode($code);
						}
						catch(Exception $x) {
							Console::WriteLine('Invalid code. The language code format is LL or LL_CC, where LL is a language code and CC is a country code');
							$code = '';
						}
						if(strlen($code)) {
							$name = Language::DescribeCode($code);
							if(array_search($code, Options::$languages) === false) {
								Options::$languages[] = $code;
								sort(self::$languages);
								Console::WriteLine('Added language ' . $code . ' - ' . $name);
							}
							else {
								Console::WriteLine($name . ' is already in the current list of languages.');
							}
						}
					}
					break;
				case 'R':
					Console::Write('Language code to remove (LL or LL_CC): ');
					$code = trim(Console::ReadLine());
					if(!strlen($code)) {
						Console::WriteLine('Skipped.');
					}
					else {
						try {
							$code = Language::NormalizeCode($code);
						}
						catch(Exception $x) {
							Console::WriteLine('Invalid code. The language code format is LL or LL_CC, where LL is a language code and CC is a country code');
							$code = '';
						}
						if(strlen($code)) {
							$name = Language::DescribeCode($code);
							if(array_search($code, Options::$languages) === false) {
								Console::WriteLine($name . ' is not in the current list of languages.');
							}
							else {
								Console::WriteLine('Removed language ' . $code . ' - ' . $name);
							}
						}
					}
					break;
				case 'L':
					foreach(Language::GetLanguages() as $code => $info) {
						Console::WriteLine($code . "\t" . $info['name']);
					}
					break;
				case 'C':
					foreach(Language::GetCountries() as $code => $info) {
						Console::WriteLine($code . "\t" . $info['name']);
					}
					break;
				case 'X':
					return;
			}
		}
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

	/** True if we have to create the .po files.
	* @var bool
	*/
	public $createPo;

	/** True if we have to compile the .po files into .mo files.
	* @var bool
	*/
	public $compile;

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

	/** The relative path from .po files to web root.
	* @var string
	*/
	public $pofile2root;

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
		$this->createPo = null;
		$this->compile = null;
		$this->potName = 'messages.pot';
	}

	/** Retrieves the version of a concrete5 installation given its root folder.
	* @param string $webroot The path of the webroot containing concrete5.
	* @throws Exception Throws an Exception in case of errors.
	* @return string
	*/
	public static function GetVersionOfConcrete5($webroot) {
		if(!defined('C5_EXECUTE')) {
			define('C5_EXECUTE', true);
		}
		if(!is_dir($webroot)) {
			throw new Exception($webroot . ' is not the valid concrete5 web root directory (it does not exist).');
		}
		if(!is_file($fn = Enviro::MergePath($webroot, 'concrete/config/version.php'))) {
			throw new Exception($webroot . ' is not the valid concrete5 web root directory (the version file does not exist).');
		}
		@include $fn;
		if(empty($APP_VERSION)) {
			throw new Exception("Unable to parse the concrete5 version file '$fn'.");
		}
		return $APP_VERSION;
	}

	/** Retrieves the version of a package given the concrete5 root folder and the package name.
	* @param string $webroot The path of the webroot containing concrete5.
	* @param string $package The package name.
	* @throws Exception Throws an Exception in case of errors.
	* @return string
	*/
	public static function GetVersionOfPackage($webroot, $package) {
		if(!is_file($fn = Enviro::MergePath($webroot, 'packages', $package, 'controller.php'))) {
			throw new Exception("'" . $package . "' is not a valid package name ('$fn' not found).");
		}
		$fc = "\n" . self::GetEvaluableContent($fn);
		if(!preg_match('/[\r\n]\s*class[\r\n\s]+([^\s\r\n]+)[\r\n\s]+extends[\r\n\s]+Package\s*\{/i', $fc, $m)) {
			throw new Exception("'" . self::$package . "' can't be parsed for a version.");
		}
		$packageClassOriginal = $m[1];
		for($x = 0; ; $x++) {
			$packageClassRenamed = $packageClassOriginal . $x;
			if(!class_exists($packageClassRenamed)) {
				if(stripos($fc, $packageClassRenamed) === false) {
					break;
				}
			}
		}
		$fc = preg_replace('/\\b' . preg_quote($packageClassOriginal) . '\\b/i', $packageClassRenamed, $fc);
		if(!class_exists('Package')) {
			eval('class Package {}');
		}
		@ob_start();
		$evalued = eval($fc);
		@ob_end_clean();
		if($evalued === false) {
			throw new Exception("Unable to parse the version of package (file '$fn').");
		}
		if(!class_exists("VersionGetter_$packageClassRenamed")) {
			eval(<<<EOT
				class VersionGetter_$packageClassRenamed extends $packageClassRenamed {
					public static function GV() {
						\$me = new VersionGetter_$packageClassRenamed();
						return \$me->pkgVersion;
					}
				}
EOT
			);
		}
		$r = eval("return VersionGetter_$packageClassRenamed::GV();");
		if(empty($r) && ($r !== '0')) {
			throw new Exception("Unable to parse the version of package (file '$fn').");
		}
		return $r;
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
			if(is_null($this->createPo)) {
				$this->createPo = (count(Options::$packages) == 1) ? true : false;
			}
			if(is_null($this->compile)) {
				$this->compile = (count(Options::$packages) == 1) ? true : false;
			}
			if(is_null($this->excludeDirsFromPot)) {
				$this->excludeDirsFromPot = Options::$DEFAULT_EXCLUDEDIRSFROMPOT_CONCRETE5;
			}
			$this->version = self::GetVersionOfConcrete5(Options::$webroot);
			$this->directoryToPotify = 'concrete';
			$this->potFullname = Enviro::MergePath(Options::$webroot, 'languages/' . $this->potName);
			$this->potfile2root = '..';
			$this->pofile2root = '../../..';
		}
		else {
			if(is_null($this->createPot)) {
				$this->createPot = true;
			}
			if(is_null($this->createPo)) {
				$this->createPo = true;
			}
			if(is_null($this->compile)) {
				$this->compile = true;
			}
			if(is_null($this->excludeDirsFromPot)) {
				$this->excludeDirsFromPot = Options::$DEFAULT_EXCLUDEDIRSFROMPOT_PACKAGE;
			}
			$this->version = self::GetVersionOfPackage(Options::$webroot, $this->package);
			$this->directoryToPotify = 'packages/' . $this->package;
			$this->potFullname = Enviro::MergePath(Options::$webroot, 'packages', $this->package, 'languages', $this->potName);
			$this->potfile2root = '../../..';
			$this->pofile2root = '../../../../..';
		}
	}

	/** Retrieves the full name of the .po file for the specified language.
	* @param string $language The language (and country) code.
	* @return string
	*/
	public function GetPoFullname($language) {
		if($this->isConcrete5) {
			$parent = Options::$webroot;
		}
		else {
			$parent = Enviro::MergePath(Options::$webroot, 'packages', $this->package);
		}
		return Enviro::MergePath($parent, 'languages', Language::NormalizeCode($language), 'LC_MESSAGES', 'messages.po');
	}

	/** Retrieves the full name of the .mo file for the specified language.
	* @param string $language The language (and country) code.
	* @return string
	*/
	public function GetMoFullname($language) {
		if($this->isConcrete5) {
			$parent = Options::$webroot;
		}
		else {
			$parent = Enviro::MergePath(Options::$webroot, 'packages', $this->package);
		}
		return Enviro::MergePath($parent, 'languages', Language::NormalizeCode($language), 'LC_MESSAGES', 'messages.mo');
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
		}
		elseif($p1 === false) {
			$p = $p2;
			$s = $s2;
		}
		elseif($p1 <= $p2) {
			$p = $p1;
			$s = $s1;
		}
		else {
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
			self::Write('Pleas answer with Y[es] or N[o]: ', $msgIsErr ? true : false);
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

	/** File and folder names are case insensitive?
	* @return boolean
	*/
	public static function FilesAreCaseInsensitive() {
		return self::IsWin();
	}

	/** The end of line char sequence for the current operating system.
	* @return string
	*/
	public static function EOL() {
		return self::IsWin() ? "\r\n" : "\n";
	}

	/** Check if a string is a valid filename (without any path info).
	* @param string $filename The string to be checked.
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
				return str_replace('/', DIRECTORY_SEPARATOR, $args[0]);
			default:
				$path = '';
				foreach($args as $arg) {
					if(strlen($arg)) {
						$arg = str_replace('\\', '/', $arg);
						if(!strlen($path)) {
							$path = $arg;
						}
						else {
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
	public static function EscapeArg($string) {
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
	* @param out array $output The output from stdout/stderr of the command.
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
		}
		else {
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

/** Represents a .pot file (and exposes .pot-related functions). */
class POTFile {

	/** Create a .pot file starting from sources.
	* @param PackageInfo $packageInfo The info about the .pot file to be created (it'll be overwritten if already existing).
	* @throws Exception Throws an Exception in case of errors.
	*/
	public static function CreateNew($packageInfo) {
		Console::WriteLine('* CREATING .POT FILE ' . $packageInfo->potName . ' FOR ' . ($packageInfo->isConcrete5 ? 'concrete5 core' : $packageInfo->package) . ' v' . $packageInfo->version);
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
			catch(Exception $x) {
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
	* @param ref array $items Found files will be appended to this array.
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
			}
			else {
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
							if(array_search($relPathSub, $excludedDirs) === false) {
								self::GetFiles($relPathSub, $extension, $items, $excludedDirs, true);
							}
							break;
					}
				}
				else {
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

	/** The file header.
	* @var POEntrySingle|null
	*/
	public $Header;

	/** The entries in the file.
	* @var array[POEntry]
	*/
	public $Entries;

	/** Reads a .pot from file.
	* @param string $filename The name of the file to read.
	* @throws Exception Throws an Exception in case of errors.
	*/
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
					}
					else {
						$hash = $entry->GetHash();
						if(array_key_exists($hash, $this->Entries) !== false) {
							throw new Exception("Duplicated entry:\n" . print_r($this->Entries[$hash]). "\n" . print_r($entry));
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

	/** Fixes the content of the header.
	* @param PackageInfo $packageInfo The info about the package.
	* @param int|null $timestamp The timestamp of the creation of the .pot file (if null we'll use current server time).
	* @throws Exception Throws an exception in case of errors.
	*/
	public function FixHeader($packageInfo, $timestamp = null) {
		$this->Header = new POEntrySingle(
			array(),
			array(
				'Project-Id-Version: ' . ($packageInfo->isConcrete5 ? 'concrete5' : $packageInfo->package) . ' ' . $packageInfo->version . '\\n',
				'Report-Msgid-Bugs-To: ' . $packageInfo->potContact . '\\n',
				'POT-Creation-Date: ' . gmdate('Y-m-d H:i', $timestamp ? $timestamp : time()) . '+0000\\n',
				'MIME-Version: 1.0\\n',
				'X-Poedit-Basepath: ' . $packageInfo->potfile2root . '\\n',
				'X-Poedit-SourceCharset: UTF-8\n',
				'Content-Type: text/plain; charset=UTF-8\\n',
				'Content-Transfer-Encoding: 8bit\\n',
				'Language: \\n'
			)
		);
	}

	/** Fix the slash of path the entry loctaion comments (eg from concrete\dispatcher.php to concrete/dispatcher.php) */
	public function FixFilesSlash() {
		foreach($this->Entries as $entry) {
			$entry->FixFilesSlash();
		}
	}

	/** Fix the cr/lf end-of-line terminator in every msgid (required when parsed source files under Windows). */
	public function Replace_CRLF_LF() {
		if($this->Header) {
			$this->Header->Replace_CRLF_LF();
		}
		foreach($this->Entries as $entry) {
			$entry->Replace_CRLF_LF();
		}
	}

	/** Add one/many entries to the entries of this instance.
	* @param POEntry|array[POEntry] $entries The entry/entries to be merged with this instance.
	*/
	public function MergeEntries($entries) {
		if(is_array($entries)) {
			foreach($entries as $entry) {
				$this->MergeEntries($entry);
			}
		}
		else {
			$hash = $entries->GetHash();
			if(array_key_exists($hash, $this->Entries)) {
				$this->Entries[$hash]->MergeWith($entries);
			}
			else {
				$this->Entries[$hash] = $entries;
			}
		}
	}

	/** Save the data to file.
	* @param string $filename The filename to save the data to (if existing it'll be overwritten).
	* @param bool $indent Set to true to indent data, false otherwise (default: false).
	* @throws Exception Throws an exception in case of errors.
	*/
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
					}
					else {
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
			if(!@rename($tempFilename, $filename)) {
				throw new Exception("error renaming from '$tempFilename' to '$filename'!");
			}
		} catch(Exception $x) {
			@unlink($tempFilename);
			throw $x;
		}
	}
}

/** Represents a .po file (and exposes .po-related functions). */
class POFile extends POTFile {

	/** Reads a .po from file.
	* @param string $filename The name of the file to read.
	* @throws Exception Throws an Exception in case of errors.
	*/
	public function __construct($filename) {
		parent::__construct($filename);
	}

	/** Retrieves a value from the header.
	* @param string $key The name of the header value (with or without ending colon).
	* @param string $default What to return if the value is missing or empty.
	* @return string
	*/
	private function GetHeaderValue($key, $default = '') {
		if($this->Header == null) {
			return $default;
		}
		$key = rtrim($key, ':') . ':';
		$value = '';
		foreach(explode("\n", str_replace("\\n", "\n", $this->Header->getMsgStr())) as $line) {
			$line = trim($line);
			if(stripos($line, $key) === 0) {
				$value = trim(substr($line, strlen($key)));
				break;
			}
		}
		return strlen($value) ? $value : $default;
	}

	/** Retrieves all the headers.
	* @param bool $removeEmptyValues Set to true to remove empty values, false (default) to keep them.
	* @return array
	* @throws Exception Throws an Exception in case of errors.
	*/
	private function GetHeaders($removeEmptyValues = false) {
		$headers = array();
		if(!is_null($this->Header)) {
			foreach(explode("\n", str_replace("\\n", "\n", $this->Header->getMsgStr())) as $line) {
				$line = trim($line);
				if(strlen($line)) {
					$i = strpos($line, ':');
					if(($i === false) || ($i === 0)) {
						throw new Exception('Invalid header line: \'' . $line . '\'');
					}
					$key = substr($line, 0, $i);
					if(array_key_exists($key, $headers)) {
						throw new Exception('Duplicated header: \'' . $key . '\'');
					}
					$headers[$key] = trim(substr($line, $i + 1));
				}
			}
		}
		if($removeEmptyValues) {
			foreach(array_keys($headers) as $key) {
				if(!strlen($headers[$key])) {
					unset($headers[$key]);
				}
			}
		}
		return $headers;
	}

	/** Fixes the content of the header.
	* @param PackageInfo $packageInfo The info about the package.
	* @param string $language The language (and country) code (eg it or it_IT).
	* @throws Exception Throws an exception in case of errors.
	*/
	public function FixHeader($packageInfo, $language, $timestamp = null) {
		$language = Language::NormalizeCode($language);
		$lc = Language::SplitCode($language);
		$languages = Language::GetLanguages();
		$languageInfo = $languages[$lc['language']];
		if(strlen($lc['country'])) {
			$countries = Language::GetCountries();
			$countryInfo = $countries[$lc['country']];
		}
		else {
			$countryInfo = null;
		}
		$oldHeaders = $this->GetHeaders(true);
		$newHeaders = array(
			'Project-Id-Version' => ($packageInfo->isConcrete5 ? 'concrete5' : $packageInfo->package) . ' ' . $packageInfo->version,
			'Report-Msgid-Bugs-To' => (isset($oldHeaders['Report-Msgid-Bugs-To']) ? $oldHeaders['Report-Msgid-Bugs-To'] : $packageInfo->potContact),
			'POT-Creation-Date' => (isset($oldHeaders['POT-Creation-Date']) ? $oldHeaders['POT-Creation-Date'] : (gmdate('Y-m-d H:i', $timestamp ? $timestamp : time()) . '+0000')),
			'PO-Revision-Date' => (isset($oldHeaders['PO-Revision-Date']) ? $oldHeaders['PO-Revision-Date'] : (gmdate('Y-m-d H:i', $timestamp ? $timestamp : time()) . '+0000')),
			'Last-Translator' => (isset($oldHeaders['Last-Translator']) ? $oldHeaders['Last-Translator'] : ''),
			'Language-Team' => (isset($oldHeaders['Language-Team']) ? $oldHeaders['Language-Team'] : ''),
			'MIME-Version' => '1.0',
			'Content-Type' => 'text/plain; charset=UTF-8',
			'Content-Transfer-Encoding' => '8bit',
			'Language' => $language,
			'Plural-Forms' => $languageInfo['plural'],
			'X-Poedit-Basepath' => $packageInfo->pofile2root,
			'X-Poedit-SourceCharset' => 'UTF-8',
			'X-Language' => $language,
			'X-Poedit-Language' => $languageInfo['name'],
			'X-Poedit-Country' => ($countryInfo ? $countryInfo['name'] : '')
		);
		foreach($oldHeaders as $oldKey => $oldValue) {
			if(!array_key_exists($oldKey, $newHeaders)) {
				$newHeaders[$oldKey] = $oldValue;
			}
		}
		$finalHeaders = array();
		foreach($newHeaders as $key => $value) {
			$finalHeaders[] = "$key: $value\\n";
		}
		$this->Header = new POEntrySingle(
			array(),
			$finalHeaders
		);
	}

	/** Create a language .po file starting from .pot file.
	* @param PackageInfo $packageInfo The info about the .po file to be created (it'll be overwritten if already existing).
	* @param string $language The language (and country) code (eg it or it_IT).
	* @throws Exception Throws an Exception in case of errors.
	*/
	public static function CreateNew($packageInfo, $language) {
		Console::WriteLine('* CREATING/UPDATING .PO FILE FOR ' . ($packageInfo->isConcrete5 ? 'concrete5 core' : $packageInfo->package) . ' v' . $packageInfo->version . ', LANGUAGE ' . Language::DescribeCode($language));
		if(!is_file($packageInfo->potFullname)) {
			throw new Exception('The .pot file name \'' . $packageInfo->potFullname . '\' does not exist.');
		}
		$poFullFilename = $packageInfo->GetPoFullname($language);
		$poFolder = dirname($poFullFilename);
		if(!is_dir($poFolder)) {
			if(!@mkdir($poFolder, 0777, true)) {
				throw new Exception('Error creating the directory \'' . $poFolder . '\'');
			}
		}
		if(!is_writable($poFolder)) {
			throw new Exception('The directory \'' . $poFolder . '\' is not writable.');
		}
		$tempPoSrc = null;
		$tempPoDst = null;
		try {
			if(is_file($poFullFilename)) {
				$src = $poFullFilename;
				$isNew = false;
			}
			else {
				Console::Write('  Creating empty .po file... ');
				$src = $tempPoSrc = Enviro::GetTemporaryFileName();
				POFile::CreateEmpty($packageInfo, $language, $tempPoSrc);
				Console::WriteLine('done.');
				$isNew = true;
			}
			Console::Write('  Merging .pot and .po files... ');
			$tempPoDst = Enviro::GetTemporaryFileName();
			$args = array();
			$args[] = '--no-fuzzy-matching'; // Do not use fuzzy matching when an exact match is not found.
			$args[] = '--previous'; // Keep the previous msgids of translated messages, marked with '#|', when adding the fuzzy marker to such messages.
			$args[] = '--lang=' . $language; // Specify the 'Language' field to be used in the header entry
			$args[] = '--force-po'; // Always write an output file even if it contains no message.
			$args[] = '--indent'; // Write the .po file using indented style.
			$args[] = '--add-location'; // Generate '#: filename:line' lines.
			$args[] = '--no-wrap'; // Do not break long message lines
			$args[] = '--output-file=' . Enviro::EscapeArg($tempPoDst); // Write output to specified file.
			$args[] = Enviro::EscapeArg($src);
			$args[] = Enviro::EscapeArg($packageInfo->potFullname);
			Enviro::RunTool('msgmerge', $args);
			Console::WriteLine('done.');
			Console::Write('  Fixing .po header... ');
			$poFile = new POFile($tempPoDst);
			$poFile->FixHeader($packageInfo, $language);
			Console::WriteLine('done.');
			Console::Write('  Saving final .po file... ');
			$poFile->SaveAs($poFullFilename, Options::$indent);
			Console::WriteLine('done.');
			@unlink($tempPoDst);
			$tempPoDst = null;
			if(!is_null($tempPoSrc)) {
				@unlink($tempPoSrc);
				$tempPoSrc = null;
			}
			Console::WriteLine('  .po file ' . ($isNew ? 'created' : 'updated') . ': ' . $packageInfo->GetPoFullname($language));
		}
		catch(Exception $x) {
			if(!is_null($tempPoDst)) {
				@unlink($tempPoDst);
			}
			if(!is_null($tempPoSrc)) {
				@unlink($tempPoSrc);
			}
			throw $x;
		}
		//$packageInfo->potFullname
	}


	/** Compile a language .po into a .mo file (which will be overwritten if existing).
	* @param PackageInfo $packageInfo The info about the .po file to be compiled.
	* @param string $language The language (and country) code (eg it or it_IT).
	* @throws Exception Throws an Exception in case of errors.
	*/
	public static function Compile($packageInfo, $language) {
		Console::WriteLine('* CREATING .MO FILE FOR ' . ($packageInfo->isConcrete5 ? 'concrete5 core' : $packageInfo->package) . ' v' . $packageInfo->version . ', LANGUAGE ' . Language::DescribeCode($language));
		$poFullFilename = $packageInfo->GetPoFullname($language);
		if(!is_file($poFullFilename)) {
			throw new Exception('The .po file name \'' . $poFullFilename . '\' does not exist.');
		}
		$moFullFilename = $packageInfo->GetMoFullname($language);
		$tempMo = Enviro::GetTemporaryFileName();
		try {
			Console::Write('  Compiling .po into temporary .mo file... ');
			$args = array();
			$args[] = '--output-file=' . Enviro::EscapeArg($tempMo);
			$args[] = '--check-format';
			$args[] = '--check-header';
			$args[] = '--check-domain';
			$args[] = Enviro::EscapeArg($poFullFilename);
			Enviro::RunTool('msgfmt', $args);
			Console::WriteLine('done.');
			Console::Write('  Moving .mo file to final location... ');
			if(is_file($moFullFilename)) {
				@unlink($moFullFilename);
			}
			if(!@rename($tempMo, $moFullFilename)) {
				throw new Exception("error renaming from '$tempMo' to '$moFullFilename'!");
			}
			$tempMo = '';
			Console::WriteLine('done.');
			Console::WriteLine('  .mo file created: ' . $moFullFilename);
		}
		catch(Exception $x) {
			if(strlen($tempMo)) {
				@unlink($tempMo);
			}
			throw $x;
		}
	}

	/** Create an epty .po file.
	* @param PackageInfo $packageInfo The info about the .po file to be created.
	* @param string $language The language (and country) code (eg it or it_IT).
	* @param string $filename The name of the file to be created (it'll be overwritten if existing).
	* @throws Exception Throws an Exception in case of errors.
	*/
	private static function CreateEmpty($packageInfo, $language, $filename) {
		if(!($hFile = @fopen($filename, 'wb'))) {
			throw new Exception("Error creating '$filename'.");
		}
		try {
			fwrite($hFile, str_replace(array("\r\n", "\r"), "\n", <<<EOT
msgid ""
msgstr ""
	"Project-Id-Version: \\n"
	"MIME-Version: 1.0\\n"
	"Content-Type: text/plain; charset=utf-8\\n"
	"Content-Transfer-Encoding: 8bit\\n"
	"X-Poedit-SourceCharset: utf-8\\n"

EOT
			));
			@fflush($hFile);
			fclose($hFile);
		}
		catch(Exception $x) {
			@fclose($hFile);
			throw $x;
		}
	}
}

/** Holds a single translation entry.
* @abstract
*/
class POEntry {

	/** All the comments associated to this entry.
	* @var array[string]
	*/
	public $comments;

	/** All the lines associated to the entry context.
	* @var array[string]
	*/
	public $msgctxt;

	/** All the lines associated to the entry msgid.
	* @var array[string]
	*/
	public $msgid;

	/** Is the entry deleted?
	* @var bool
	*/
	public $isDeleted;

	/** Gets the entry context text.
	* @return string
	*/
	public function getMsgCtx() {
		return self::ArrayToString($this->msgctxt);
	}

	/** Gets the entry msgid text.
	* @return string
	*/
	public function getMsgID() {
		return self::ArrayToString($this->msgid);
	}

	/** Builds a string from an array of strings.
	* @param array[string] $array The strings to be merged.
	* @return string
	*/
	protected static function ArrayToString($array) {
		if(empty($array)) {
			return '';
		}
		return implode($array);
	}

	/** Fix CR/LF in the given array of strings.
	* @param ref array[string] $array The array of strings to be fixed.
	*/
	protected static function Replace_CRLF_LF_in(&$array) {
		if(is_array($array)) {
			foreach(array_keys($array) as $key) {
				$array[$key] = str_replace('\\r\\n', '\\n', $array[$key]);
			}
		}
	}

	/** Fix CR/LF in the msgid array of strings. */
	protected function Replace_CRLF_LF_msgid() {
		self::Replace_CRLF_LF_in($this->msgid);
	}

	/** Save an array of strings to file.
	* @param resource $hFile The file to save the data to.
	* @param string $name The name of the data to be saved.
	* @param array[string] $array The data to be saved.
	* @param bool $indent Should we indent data [default: false].
	* @param bool $skipIfEmptyArray Should we skip data writing if the data to write is empty.
	*/
	protected static function ArrayToFile($hFile, $name, $array, $indent = false, $skipIfEmptyArray = false) {
		if($indent) {
			if(strlen($name) < 7) {
				$name .= str_repeat(' ', 7 - strlen($name));
			}
			$pre = str_repeat(' ', strlen($name) + 1);
		}
		else {
			$pre = '';
		}
		if(empty($array)) {
			if(!$skipIfEmptyArray) {
				fwrite($hFile, "$name \"\"\n");
			}
		}
		else {
			$first = true;
			foreach($array as $line) {
				if($first) {
					fwrite($hFile, "$name \"$line\"\n");
					$first = false;
				}
				else {
					fwrite($hFile, "$pre\"$line\"\n");
				}
			}
		}
	}

	/** Fix slash from back to forward in location comments. */
	public function FixFilesSlash() {
		$n = count($this->comments);
		for($i = 0; $i < $n; $i++) {
			if(strpos($this->comments[$i], '#: ') === 0) {
				$this->comments[$i] = str_replace('\\', '/', $this->comments[$i]);
			}
		}
	}

	/** Initializes the data instance.
	* @param array[string] $comments The string array of comments.
	* @param array[string] $msgctxt The string array of msgctx.
	* @param array[string] $msgid The string array of msgid.
	* @param bool $isDeleted Is the entry marked as deleted [default: false].
	*/
	protected function __construct($comments, $msgctxt, $msgid, $isDeleted = false) {
		$this->comments = is_array($comments) ? $comments : (strlen($comments) ? array($comments) : array());
		$this->msgctxt = is_array($msgctxt) ? $msgctxt : (strlen($msgctxt) ? array($msgctxt) : array());
		$this->msgid = is_array($msgid) ? $msgid : (strlen($msgid) ? array($msgid) : array());
		$this->isDeleted = $isDeleted ? true : false;
	}

	/** Callback function to sort comments.
	* @param string $a The first comment to compare.
	* @param string $b The second comment to compare.
	* @return int
	*/
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
				}
				else {
					return strcasecmp($a, $b);
				}
			default:
				return 0;
		}
	}

	/** Save the instance data to file (comments and context).
	* @param resource $hFile The file to save the data to.
	* @param bool $indent Set to true to indent data, false otherwise (default: false).
	*/
	protected function _saveTo($hFile, $indent = false) {
		usort($this->comments, array(__CLASS__, 'CommentsSorter'));
		foreach($this->comments as $comment) {
			fwrite($hFile, $comment);
			fwrite($hFile, "\n");
		}
		self::ArrayToFile($hFile, ($this->isDeleted ? '#~ ' : '') . 'msgctxt', $this->msgctxt, $indent, true);
	}

	/** Retrieves POEntry instances from one/many .xml files.
	* @param string|array[string] $xmlFiles The .xml file(s) to read.
	* @return array[POEntry]
	* @throws Exception Throws an Exception in case of errors.
	*/
	public static function FromXmlFile($xmlFiles) {
		if(is_array($xmlFiles)) {
			$result = array();
			foreach($xmlFiles as $xmlFile) {
				foreach(self::FromXmlFile($xmlFile) as $poEntry) {
					$hash = $poEntry->GetHash();
					if(array_key_exists($hash, $result)) {
						$result[$hash]->MergeWith($poEntry);
					}
					else {
						$result[$hash] = $poEntry;
					}
				}
			}
			return array_values($result);
		}
		else {
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

	/** Parse an xml node and retrieves any associated POEntry.
	* @param string $filenameRel The relative file name of the xml file being read.
	* @param string $prePath The path of the node containing the current node.
	* @param DOMNode $node The current node.
	* @param ref array[POEntry] $entries Will be populated with found entries.
	* @throws Exception Throws an Exception in case of errors.
	*/
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
				// Skip this node
				break;
			case '/concrete5-cif/config':
			case '/concrete5-cif/stacks/stack/area/block/data/record';
			case '/concrete5-cif/pagetypes/pagetype/page/area/block/data/record':
			case '/concrete5-cif/pages/page/area/block/data/record':
				// Skip this node and its children
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
				// Translabe text: name attribute
				self::ReadNodeAttribute($filenameRel, $node, 'name', $entries);
				break;
			case '/concrete5-cif/singlepages/page':
			case '/concrete5-cif/pages/page':
			case '/concrete5-cif/singlepages/page':
			case '/concrete5-cif/permissionkeys/permissionkey':
			case '/concrete5-cif/taskpermissions/taskpermission':
			case '/concrete5-cif/pagetypes/pagetype/page':
				// Translabe text: name attribute, description attribute
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

	/** Parse a node attribute and create a POEntry item if it has a value.
	* @param string $filenameRel The relative file name of the xml file being read.
	* @param DOMNode $node The current node.
	* @param string $attributeName The name of the attribute.
	* @param ref array[POEntry] $entries Will be populated with found entries.
	*/
	private static function ReadNodeAttribute($filenameRel, $node, $attributeName, &$entries) {
		$value = $node->getAttribute($attributeName);
		if(strlen($value)) {
			if(!array_key_exists($value, $entries)) {
				$entries[$value] = new POEntrySingle($value);
			}
			$entries[$value]->comments[] = '#: ' . str_replace('\\', '/', $filenameRel) . ':' . $node->getLineNo();
		}
	}

	/** Merge an instance of a POEntry with this (they must be equal, only comments may differ).
	* @param POEntry $poEntry The entry to be merged.
	*/
	public function MergeWith($poEntry) {
		$this->comments = array_merge($this->comments, $poEntry->comments);
	}

	/** Read the next POEntry from the lines of the .po file.
	* @param array[string] $lines The lines of the file.
	* @param int $start The current line position.
	* @param out int $nextStart The next line position.
	* @throws Exception Throws an Exception in case of errors.
	* @return POEntry|null Returns null if no POEntry has been found, a POEntry if found.
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
		$isDeleted = null;
		for($i = $start; ($i < $n) && ($nextStart < 0); $i++) {
			$trimmedLine = trim($lines[$i]);
			if(strlen($trimmedLine)) {
				$dataReady = (($msgstrIndex >= 0) || count($msgstr_pluralIndexes));
				if(preg_match('/^#~[ \t]+msgctxt($|\s|")/', $trimmedLine)) {
					if($dataReady) {
						$lineType = 'next';
					}
					else {
						if(!is_null($isDeleted)) {
							throw new Exception('Misplaced deleted msgctxt');
						}
						$isDeleted = true;
						$lineType = 'msgctxt';
					}
				}
				elseif(preg_match('/^#~[ \t]+msgid($|\s|")/', $trimmedLine)) {
					if($dataReady) {
						$lineType = 'next';
					}
					else {
						if(is_null($isDeleted) || ($isDeleted)) {
							$isDeleted = true;
						}
						else {
							throw new Exception('Misplaced deleted msgid');
						}
						$lineType = 'msgid';
					}
				}
				elseif(preg_match('/^#~[ \t]+msgid_plural($|\s|")/', $trimmedLine)) {
					if(!$isDeleted) {
						throw new Exception('Misplaced deleted msgid_plural');
					}
					$lineType = 'msgid_plural';
				}
				elseif(preg_match('/^#~[ \t]+msgstr($|\s|")/', $trimmedLine)) {
					if(!$isDeleted) {
						throw new Exception('Misplaced deleted msgstr');
					}
					$lineType = 'msgstr';
				}
				elseif(preg_match('/^#~[ \t]+msgstr\[[0-9]+\]/', $trimmedLine)) {
					if(!$isDeleted) {
						throw new Exception('Misplaced deleted msgstr[]');
					}
					$lineType = 'msgstr_plural';
				}
				elseif($trimmedLine[0] == '#') {
					if($dataReady) {
						$lineType = 'next';
					}
					else {
						$lineType = 'comment';
					}
				}
				elseif(preg_match('/^msgctxt($|\s|")/', $trimmedLine)) {
					if($dataReady) {
						$lineType = 'next';
					}
					else {
						if(!is_null($isDeleted)) {
							throw new Exception('Misplaced non-deleted msgctxt');
						}
						$isDeleted = false;
						$lineType = 'msgctxt';
					}
				}
				elseif(preg_match('/^msgid($|\s|")/', $trimmedLine)) {
					if($dataReady) {
						$lineType = 'next';
					}
					else {
						if(is_null($isDeleted) || (!$isDeleted)) {
							$isDeleted = false;
						}
						else {
							throw new Exception('Misplaced non-deleted msgid');
						}
						$lineType = 'msgid';
					}
				}
				elseif(preg_match('/^msgid_plural($|\s|")/', $trimmedLine)) {
					if($isDeleted) {
						throw new Exception('Misplaced non-deleted msgid_plural');
					}
					$lineType = 'msgid_plural';
				}
				elseif(preg_match('/^msgstr($|\s|")/', $trimmedLine)) {
					if($isDeleted) {
						throw new Exception('Misplaced non-deleted msgstr');
					}
					$lineType = 'msgstr';
				}
				elseif(preg_match('/^msgstr\[[0-9]+\]/', $trimmedLine)) {
					if($isDeleted) {
						throw new Exception('Misplaced non-deleted msgstr[]');
					}
					$lineType = 'msgstr_plural';
				}
				elseif(preg_match('/^".*"$/', $trimmedLine)) {
					$lineType = 'text';
				}
				else {
					throw new Exception("Invalid content '$trimmedLine' at line " . ($i + 1) . ".");
				}
				switch($lineType) {
					case 'next';
						$nextStart = $i;
						break;
					case 'comment':
						if($msgidIndex < 0) {
							while(preg_match('/^(#:[ \t].*?:\d+)[ \t]+([^ \t].*:\d.*)$/', $trimmedLine, $chunks)) {
								$comments[] = $chunks[1];
								$trimmedLine = '#: ' . trim($chunks[2]);
							}
							$comments[] = $trimmedLine;
						}
						else {
							throw new Exception("Misplaced '$trimmedLine' (linetype: $lineType) at line " . ($i + 1) . ".");
						}
						break;
					case 'msgctxt':
						if(($msgctxtIndex < 0) && ($msgidIndex < 0)) {
							$msgctxtIndex = $i;
						}
						else {
							throw new Exception("Misplaced '$trimmedLine' (linetype: $lineType) at line " . ($i + 1) . ".");
						}
						break;
					case 'msgid':
						if($msgidIndex < 0) {
							$msgidIndex = $i;
						}
						else {
							throw new Exception("Misplaced '$trimmedLine' (linetype: $lineType) at line " . ($i + 1) . ".");
						}
						break;
					case 'msgid_plural':
						if(($msgidIndex >= 0) && ($msgid_pluralIndex < 0) && ($msgstrIndex < 0) && empty($msgstr_pluralIndexes)) {
							$msgid_pluralIndex = $i;
						}
						else {
							throw new Exception("Misplaced '$trimmedLine' (linetype: $lineType) at line " . ($i + 1) . ".");
						}
						break;
					case 'msgstr':
						if(($msgidIndex >= 0) && ($msgstrIndex < 0) && empty($msgstr_pluralIndexes)) {
							$msgstrIndex = $i;
						}
						else {
							throw new Exception("Misplaced '$trimmedLine' (linetype: $lineType) at line " . ($i + 1) . ".");
						}
						break;
					case 'msgstr_plural':
						if(($msgidIndex >= 0) && ($msgid_pluralIndex >= 0) && ($msgstrIndex < 0)) {
							$msgstr_pluralIndexes[] = $i;
						}
						else {
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
			if($isDeleted) {
				$isDeleted = true;
			}
			if($msgidIndex < 0) {
				throw new Exception("Missing msgid for entry starting at line " . ($i + 1) . ".");
			}
			if($msgid_pluralIndex >= 0) {
				if(($msgstrIndex >= 0) || empty($msgstr_pluralIndexes)) {
					throw new Exception("Expected plural msgstr for entry starting at line " . ($i + 1) . ".");
				}
				return POEntryPlural::FromLines($lines, $comments, $msgctxtIndex, $msgidIndex, $msgid_pluralIndex, $msgstr_pluralIndexes, $isDeleted);
			}
			else {
				if(($msgstrIndex < 0) || count($msgstr_pluralIndexes)) {
					throw new Exception("Expected singular msgstr for entry starting at line " . ($i + 1) . ".");
				}
				return POEntrySingle::FromLines($lines, $comments, $msgctxtIndex, $msgidIndex, $msgstrIndex, $isDeleted);
			}
		}
		else {
			return null;
		}
	}
}

/** Holds a single translation entry (non-plural). */
class POEntrySingle extends POEntry {

	/** All the lines associated to the entry msgstr.
	* @var array[string]
	*/
	public $msgstr;


	/** Gets the entry msgstr text.
	* @return string
	*/
	public function getMsgStr() {
		return self::ArrayToString($this->msgstr);
	}

	/** Initializes the data instance.
	* @param array[string] $msgid The string array of msgid.
	* @param array[string] $msgstr The string array of msgstr.
	* @param array[string] $comments The string array of comments.
	* @param array[string] $msgctxt The string array of msgctx.
	* @param bool $isDeleted Is the entry marked as deleted [default: false].
	*/
	public function __construct($msgid, $msgstr = array(), $comments = array(), $msgctxt = array(), $isDeleted = false) {
		parent::__construct($comments, $msgctxt, $msgid, $isDeleted);
		$this->msgstr = is_array($msgstr) ? $msgstr : (strlen($msgstr) ? array($msgstr) : array());
	}

	/** Create a POEntrySingle from .po/.pot file lines.
	* @param array[string] $lines All the lines of the file.
	* @param array[string] $comments The comments lines.
	* @param int $msgctxtIndex The index of the first line of msgctxt (-1 if not present).
	* @param int $msgidIndex The index of the first line of msgid.
	* @param int $msgstrIndex The index of the first line of msgstr.
	* @param bool $isDeleted True if the entry is marked as deleted.
	* @return POEntrySingle
	*/
	public static function FromLines($lines, $comments, $msgctxtIndex, $msgidIndex, $msgstrIndex, $isDeleted) {
		$values = array(array(), array(), array());
		$n = count($lines);
		foreach(array($msgctxtIndex, $msgidIndex, $msgstrIndex) as $valueIndex => $i) {
			if($i < 0) {
				continue;
			}
			for($j = $i; $j < $n; $j++) {
				if(preg_match(($j == $i) ? '/"(.*)"[ \t]*$/' : '/^[ \t]*"(.*)"[ \t]*$/', $lines[$j], $matches)) {
					$values[$valueIndex][] = $matches[1];
				}
				else {
					break;
				}
			}
		}
		$className = __CLASS__;
		return new $className($values[1], $values[2], $comments, $values[0], $isDeleted);
	}

	/** Fix the cr/lf end-of-line terminator in every msgid (required when parsed source files under Windows). */
	public function Replace_CRLF_LF() {
		parent::Replace_CRLF_LF_msgid();
	}

	/** Save the instance data to file.
	* @param resource $hFile The file to save the data to.
	* @param bool $indent Set to true to indent data, false otherwise (default: false).
	*/
	public function SaveTo($hFile, $indent = false) {
		parent::_saveTo($hFile, $indent);
		self::ArrayToFile($hFile, ($this->isDeleted ? '#~ ' : '') . 'msgid', $this->msgid, $indent);
		self::ArrayToFile($hFile, ($this->isDeleted ? '#~ ' : '') . 'msgstr', $this->msgstr, $indent);
	}

	/** Retrieves a string that uniquely identifies the entry.
	* @return string
	*/
	public function GetHash() {
		return $this->getMsgCtx() . chr(1) . $this->getMsgID() . chr(3) . ($this->isDeleted ? 0 : 1);
	}
}

/** Holds a single translation entry (plural). */
class POEntryPlural extends POEntry {

	/** All the lines associated to the entry msgstr.
	* @var array[string]
	*/
	public $msgid_plural;

	/** All the lines associated to the all msgstr[] entries.
	* @var array[array[string]]
	*/
	public $msgstr;


	/** Initializes the data instance.
	* @param array[string] $msgid The string array of msgid.
	* @param array[string] $msgid_plural The string array of msgid_plural.
	* @param array[array[string]] $msgstr The string array of every msgstr[].
	* @param array[string] $comments The string array of comments.
	* @param array[string] $msgctxt The string array of msgctx.
	* @param bool $isDeleted Is the entry marked as deleted [default: false].
	*/
	public function __construct($msgid, $msgid_plural, $msgstr = array(), $comments = array(), $msgctxt = array(), $isDeleted = false) {
		parent::__construct($comments, $msgctxt, $msgid, $isDeleted);
		$this->msgid_plural = is_array($msgid_plural) ? $msgid_plural : (strlen($msgid_plural) ? array($msgid_plural) : array());
		$this->msgstr = is_array($msgstr) ? $msgstr : array();
	}

	/** Gets the entry msgid_plural text.
	* @return string
	*/
	public function getMsgID_Plural() {
		return self::ArrayToString($this->msgid);
	}

	/** Create a POEntryPlural from .po/.pot file lines.
	* @param array[string] $lines All the lines of the file.
	* @param array[string] $comments The comments lines.
	* @param int $msgctxtIndex The index of the first line of msgctxt (-1 if not present).
	* @param int $msgidIndex The index of the first line of msgid.
	* @param int $msgid_pluralIndex The index of the first line of msgid_plural.
	* @param array[int] $msgstr_pluralIndexes The indexes of the first lines of each msgstr[].
	* @param bool $isDeleted True if the entry is marked as deleted.
	* @throws Exception Throws an Exception in case of errors.
	* @return POEntryPlural
	*/
	public static function FromLines($lines, $comments, $msgctxtIndex, $msgidIndex, $msgid_pluralIndex, $msgstr_pluralIndexes, $isDeleted) {
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
					}
					else {
						break;
					}
				}
			}
		}
		$className = __CLASS__;
		return new $className($multiValues[0][1], $multiValues[0][2], $multiValues[1], $comments, $multiValues[0][0], $isDeleted);
	}

	/** Fix the cr/lf end-of-line terminator in msgid and msgid_plural (required when parsed source files under Windows). */
	public function Replace_CRLF_LF() {
		parent::Replace_CRLF_LF_msgid();
		self::Replace_CRLF_LF_in($this->msgid_plural);
	}

	/** Save the instance data to file.
	* @param resource $hFile The file to save the data to.
	* @param bool $indent Set to true to indent data, false otherwise (default: false).
	*/
	public function SaveTo($hFile, $indent = false) {
		parent::_saveTo($hFile, $indent = false);
		self::ArrayToFile($hFile, ($this->isDeleted ? '#~ ' : '') . 'msgid', $this->msgid, $indent);
		self::ArrayToFile($hFile, ($this->isDeleted ? '#~ ' : '') . 'msgid_plural', $this->msgid_plural, $indent);
		for($i = 0; $i < count($this->msgstr); $i++) {
			self::ArrayToFile($hFile, ($this->isDeleted ? '#~ ' : '') . "msgstr[$i]", $this->msgstr[$i], $indent);
		}
	}

	/** Retrieves a string that uniquely identifies the entry.
	* @return string
	*/
	public function GetHash() {
		return $this->getMsgCtx() . chr(1) . $this->getMsgID() . chr(2) . $this->getMsgID_Plural() . chr(3) . ($this->isDeleted ? 0 : 1);
	}
}

/** Static class with language-related functions. */
class Language {

	/** Get the code of the language of the original strings.
	* @return string
	*/
	public static function GetSourceCode() {
		return 'en';
	}

	/** Get the predefined language codes.
	* @return array[string]
	*/
	public static function GetStandardCodes() {
		return array(
			'ar',
			'bg',
			'ca',
			'cs_CZ',
			'da_DK',
			'de_DE',
			'es_AR',
			'es_ES',
			'es_PE',
			'et',
			'fa_IR',
			'fi_FI',
			'fr_FR',
			'he_IL',
			'hi',
			'hr',
			'hu_HU',
			'id_ID',
			'it_IT',
			'ja_JP',
			'ku',
			'lt_LT',
			'nl_NL',
			'no_NO',
			'pl_PL',
			'pt_BR',
			'pt_PT',
			'ro_RO',
			'ru_RU',
			'sv_SE',
			'th_TH',
			'tr',
			'zh_CN',
		);
	}

	/** Split a code into language (required) and country (optional) codes (eg: from 'xx_XX' to array with <b>language</b> and <b>country</b>).
	* @param string $code The code to be splitted.
	* @throws Exception Throws an Exception if $code is malformed or if contains invalid values.
	* @return array
	*/
	public static function SplitCode($code) {
		if(!preg_match('/^([a-z]{2,3})([_\\-]([a-z]{2,3}))?$/i', $code, $m)) {
			throw new Exception("'$code' is not a valid language/country code (malfolmed code)");
		}
		$language = $m[1];
		if(!array_key_exists($language, self::GetLanguages())) {
			throw new Exception("'$code' is not a valid language/country code ('$language' is not a valid language code)");
		}
		$country = (count($m) > 3) ? strtoupper($m[3]) : '';
		if(strlen($country) && (!array_key_exists($country, self::GetCountries()))) {
			throw new Exception("'$code' is not a valid language/country code ('$country' is not a valid country code)");
		}
		return array('language' => $language, 'country' => $country);
	}

	/** Check and normalize a code (eg from 'Xx-xX' to 'xx_XX').
	* @param string $code The code to be normalized.
	* @return string
	*/
	public static function NormalizeCode($code) {
		$a = self::SplitCode($code);
		return $a['language'] . (strlen($a['country']) ? ('_' . $a['country']) : '');
	}

	/** Describe a code, eg: from 'it_IT' to 'Italian (Italy)', 'en' to 'English'.
	* @param string|array $code A code or an already splitted code.
	* @throws Exception Throws an Exception if $code is malformed or if contains invalid values.
	* @return string
	*/
	public static function DescribeCode($code) {
		if(is_array($code)) {
			$a = $code;
			$s = implode('-', $code);
			if(!isset($a['language'])) {
				throw new Exception('Missing \'language\' key.');
			}
		}
		else {
			$a = self::SplitCode($code);
			$s = $code;
		}
		$languages = self::GetLanguages();
		if(!array_key_exists($a['language'], $languages)) {
			throw new Exception("'$s' is not a valid language/country code ('{$a['language']}' is not a valid language code)");
		}
		$result = $languages[$a['language']]['name'];
		if(isset($a['country']) && strlen($a['country'])) {
			$a['country'] = strtoupper($a['country']);
			$countries = self::GetCountries();
			if(!array_key_exists($a['country'], $countries)) {
				throw new Exception("'$s' is not a valid language/country code ('{$a['country']}' is not a valid country code)");
			}
			$result .= ' (' . $countries[$a['country']]['name'] . ')';
		}
		return $result;
	}

	/** Cache of the result of Language::GetLanguages().
	* @var null|array
	*/
	private static $_languages = null;

	/** Return the list of all languages.
	* @return array Returns an array with: string <b>key</b> = language identifier, values = an array with:
	* <ul>
	*	<li>string <b>name</b> The language name.</li>
	*	<li>string <b>plural</b> The plural formula.</li>
	* </ul>
	*/
	public static function GetLanguages() {
		if(empty(self::$_languages)) {
			// From http://translate.sourceforge.net/wiki/l10n/pluralforms (there's there a bug in 'lt': there's an 'or' instead of a '||')
			self::$_languages = array(
				'ach' => array('name' => 'Acholi', 'plural' => 'nplurals=2; plural=(n > 1)'),
				'af' => array('name' => 'Afrikaans', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'ak' => array('name' => 'Akan', 'plural' => 'nplurals=2; plural=(n > 1)'),
				'am' => array('name' => 'Amharic', 'plural' => 'nplurals=2; plural=(n > 1)'),
				'an' => array('name' => 'Aragonese', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'ar' => array('name' => 'Arabic', 'plural' => 'nplurals=6; plural= n==0 ? 0 : n==1 ? 1 : n==2 ? 2 : n%100>=3 && n%100<=10 ? 3 : n%100>=11 ? 4 : 5;'),
				'arn' => array('name' => 'Mapudungun', 'plural' => 'nplurals=2; plural=(n > 1)'),
				'ast' => array('name' => 'Asturian', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'ay' => array('name' => 'Aymará', 'plural' => 'nplurals=1; plural=0'),
				'az' => array('name' => 'Azerbaijani', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'be' => array('name' => 'Belarusian', 'plural' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'),
				'bg' => array('name' => 'Bulgarian', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'bn' => array('name' => 'Bengali', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'bo' => array('name' => 'Tibetan', 'plural' => 'nplurals=1; plural=0'),
				'br' => array('name' => 'Breton', 'plural' => 'nplurals=2; plural=(n > 1)'),
				'brx' => array('name' => 'Bodo', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'bs' => array('name' => 'Bosnian', 'plural' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'),
				'ca' => array('name' => 'Catalan', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'cgg' => array('name' => 'Chiga', 'plural' => 'nplurals=1; plural=0'),
				'cs' => array('name' => 'Czech', 'plural' => 'nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4) ? 1 : 2'),
				'csb' => array('name' => 'Kashubian', 'plural' => 'nplurals=3; n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2'),
				'cy' => array('name' => 'Welsh', 'plural' => 'nplurals=4; plural= (n==1) ? 0 : (n==2) ? 1 : (n != 8 && n != 11) ? 2 : 3'),
				'da' => array('name' => 'Danish', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'de' => array('name' => 'German', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'doi' => array('name' => 'Dogri', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'dz' => array('name' => 'Dzongkha', 'plural' => 'nplurals=1; plural=0'),
				'el' => array('name' => 'Greek', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'en' => array('name' => 'English', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'eo' => array('name' => 'Esperanto', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'es' => array('name' => 'Spanish', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'et' => array('name' => 'Estonian', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'eu' => array('name' => 'Basque', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'fa' => array('name' => 'Persian', 'plural' => 'nplurals=1; plural=0'),
				'ff' => array('name' => 'Fulah', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'fi' => array('name' => 'Finnish', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'fil' => array('name' => 'Filipino', 'plural' => 'nplurals=2; plural=n > 1'),
				'fo' => array('name' => 'Faroese', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'fr' => array('name' => 'French', 'plural' => 'nplurals=2; plural=(n > 1)'),
				'fur' => array('name' => 'Friulian', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'fy' => array('name' => 'Frisian', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'ga' => array('name' => 'Irish', 'plural' => 'nplurals=5; plural=n==1 ? 0 : n==2 ? 1 : n<7 ? 2 : n<11 ? 3 : 4'),
				'gd' => array('name' => 'Scottish Gaelic', 'plural' => 'nplurals=4; plural=(n==1 || n==11) ? 0 : (n==2 || n==12) ? 1 : (n > 2 && n < 20) ? 2 : 3'),
				'gl' => array('name' => 'Galician', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'gu' => array('name' => 'Gujarati', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'gun' => array('name' => 'Gun', 'plural' => 'nplurals=2; plural = (n > 1)'),
				'ha' => array('name' => 'Hausa', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'he' => array('name' => 'Hebrew', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'hi' => array('name' => 'Hindi', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'hne' => array('name' => 'Chhattisgarhi', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'hy' => array('name' => 'Armenian', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'hr' => array('name' => 'Croatian', 'plural' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'),
				'hu' => array('name' => 'Hungarian', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'ia' => array('name' => 'Interlingua', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'id' => array('name' => 'Indonesian', 'plural' => 'nplurals=1; plural=0'),
				'is' => array('name' => 'Icelandic', 'plural' => 'nplurals=2; plural=(n%10!=1 || n%100==11)'),
				'it' => array('name' => 'Italian', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'ja' => array('name' => 'Japanese', 'plural' => 'nplurals=1; plural=0'),
				'jbo' => array('name' => 'Lojban', 'plural' => 'nplurals=1; plural=0'),
				'jv' => array('name' => 'Javanese', 'plural' => 'nplurals=2; plural=n!=0'),
				'ka' => array('name' => 'Georgian', 'plural' => 'nplurals=1; plural=0'),
				'kk' => array('name' => 'Kazakh', 'plural' => 'nplurals=1; plural=0'),
				'km' => array('name' => 'Khmer', 'plural' => 'nplurals=1; plural=0'),
				'kn' => array('name' => 'Kannada', 'plural' => 'nplurals=2; plural=(n!=1)'),
				'ko' => array('name' => 'Korean', 'plural' => 'nplurals=1; plural=0'),
				'ku' => array('name' => 'Kurdish', 'plural' => 'nplurals=2; plural=(n!= 1)'),
				'kw' => array('name' => 'Cornish', 'plural' => 'nplurals=4; plural= (n==1) ? 0 : (n==2) ? 1 : (n == 3) ? 2 : 3'),
				'ky' => array('name' => 'Kyrgyz', 'plural' => 'nplurals=1; plural=0'),
				'lb' => array('name' => 'Letzeburgesch', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'ln' => array('name' => 'Lingala', 'plural' => 'nplurals=2; plural=n>1;'),
				'lo' => array('name' => 'Lao', 'plural' => 'nplurals=1; plural=0'),
				'lt' => array('name' => 'Lithuanian', 'plural' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && (n%100<10 || n%100>=20) ? 1 : 2)'),
				'lv' => array('name' => 'Latvian', 'plural' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n != 0 ? 1 : 2)'),
				'mai' => array('name' => 'Maithili', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'mfe' => array('name' => 'Mauritian Creole', 'plural' => 'nplurals=2; plural=(n > 1)'),
				'mg' => array('name' => 'Malagasy', 'plural' => 'nplurals=2; plural=(n > 1)'),
				'mi' => array('name' => 'Maori', 'plural' => 'nplurals=2; plural=(n > 1)'),
				'mk' => array('name' => 'Macedonian', 'plural' => 'nplurals=2; plural= n==1 || n%10==1 ? 0 : 1'),
				'ml' => array('name' => 'Malayalam', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'mn' => array('name' => 'Mongolian', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'mni' => array('name' => 'Manipuri', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'mnk' => array('name' => 'Mandinka', 'plural' => 'nplurals=3; plural=(n==0 ? 0 : n==1 ? 1 : 2'),
				'mr' => array('name' => 'Marathi', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'ms' => array('name' => 'Malay', 'plural' => 'nplurals=1; plural=0'),
				'mt' => array('name' => 'Maltese', 'plural' => 'nplurals=4; plural=(n==1 ? 0 : n==0 || ( n%100>1 && n%100<11) ? 1 : (n%100>10 && n%100<20 ) ? 2 : 3)'),
				'my' => array('name' => 'Burmese', 'plural' => 'nplurals=1; plural=0'),
				'nah' => array('name' => 'Nahuatl', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'nap' => array('name' => 'Neapolitan', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'nb' => array('name' => 'Norwegian Bokmal', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'ne' => array('name' => 'Nepali', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'nl' => array('name' => 'Dutch', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'se' => array('name' => 'Northern Sami', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'nn' => array('name' => 'Norwegian Nynorsk', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'no' => array('name' => 'Norwegian', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'nso' => array('name' => 'Northern Sotho', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'oc' => array('name' => 'Occitan', 'plural' => 'nplurals=2; plural=(n > 1)'),
				'or' => array('name' => 'Oriya', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'ps' => array('name' => 'Pashto', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'pa' => array('name' => 'Punjabi', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'pap' => array('name' => 'Papiamento', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'pl' => array('name' => 'Polish', 'plural' => 'nplurals=3; plural=(n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'),
				'pms' => array('name' => 'Piemontese', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'pt' => array('name' => 'Portuguese', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'rm' => array('name' => 'Romansh', 'plural' => 'nplurals=2; plural=(n!=1);'),
				'ro' => array('name' => 'Romanian', 'plural' => 'nplurals=3; plural=(n==1 ? 0 : (n==0 || (n%100 > 0 && n%100 < 20)) ? 1 : 2);'),
				'ru' => array('name' => 'Russian', 'plural' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'),
				'rw' => array('name' => 'Kinyarwanda', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'sah' => array('name' => 'Yakut', 'plural' => 'nplurals=1; plural=0'),
				'sat' => array('name' => 'Santali', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'sco' => array('name' => 'Scots', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'sd' => array('name' => 'Sindhi', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'si' => array('name' => 'Sinhala', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'sk' => array('name' => 'Slovak', 'plural' => 'nplurals=3; plural=(n==1) ? 0 : (n>=2 && n<=4) ? 1 : 2'),
				'sl' => array('name' => 'Slovenian', 'plural' => 'nplurals=4; plural=(n%100==1 ? 1 : n%100==2 ? 2 : n%100==3 || n%100==4 ? 3 : 0)'),
				'so' => array('name' => 'Somali', 'plural' => 'nplurals=2; plural=n != 1'),
				'son' => array('name' => 'Songhay', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'sq' => array('name' => 'Albanian', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'sr' => array('name' => 'Serbian', 'plural' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'),
				'su' => array('name' => 'Sundanese', 'plural' => 'nplurals=1; plural=0'),
				'sw' => array('name' => 'Swahili', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'sv' => array('name' => 'Swedish', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'ta' => array('name' => 'Tamil', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'te' => array('name' => 'Telugu', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'tg' => array('name' => 'Tajik', 'plural' => 'nplurals=2; plural=(n > 1)'),
				'ti' => array('name' => 'Tigrinya', 'plural' => 'nplurals=2; plural=n > 1'),
				'th' => array('name' => 'Thai', 'plural' => 'nplurals=1; plural=0'),
				'tk' => array('name' => 'Turkmen', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'tr' => array('name' => 'Turkish', 'plural' => 'nplurals=2; plural=(n>1)'),
				'tt' => array('name' => 'Tatar', 'plural' => 'nplurals=1; plural=0'),
				'ug' => array('name' => 'Uyghur', 'plural' => 'nplurals=1; plural=0;'),
				'uk' => array('name' => 'Ukrainian', 'plural' => 'nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)'),
				'ur' => array('name' => 'Urdu', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'uz' => array('name' => 'Uzbek', 'plural' => 'nplurals=2; plural=(n > 1)'),
				'vi' => array('name' => 'Vietnamese', 'plural' => 'nplurals=1; plural=0'),
				'wa' => array('name' => 'Walloon', 'plural' => 'nplurals=2; plural=(n > 1)'),
				'wo' => array('name' => 'Wolof', 'plural' => 'nplurals=1; plural=0'),
				'yo' => array('name' => 'Yoruba', 'plural' => 'nplurals=2; plural=(n != 1)'),
				'zh' => array('name' => 'Chinese', 'plural' => 'nplurals=1; plural=0')
			);
		}
		return self::$_languages;
	}

	/** Cache of the result of Language::GetCountries().
	* @var null|array
	*/
	private static $_countries = null;

	/** Return the list of all countries.
	* @return array Returns an array with: string <b>key</b> = country identifier, values = an array with:
	* <ul>
	*	<li>string <b>name</b> The country name.</li>
	* </ul>
	*/
	public static function GetCountries() {
		if(empty(self::$_countries)) {
			// From http://en.wikipedia.org/wiki/ISO_3166-2
			self::$_countries = array(
				'AD' => array('name' => 'Andorra'),
				'AE' => array('name' => 'United Arab Emirates'),
				'AF' => array('name' => 'Afghanistan'),
				'AG' => array('name' => 'Antigua and Barbuda'),
				'AI' => array('name' => 'Anguilla'),
				'AL' => array('name' => 'Albania'),
				'AM' => array('name' => 'Armenia'),
				'AN' => array('name' => 'Netherlands Antilles'),
				'AO' => array('name' => 'Angola'),
				'AQ' => array('name' => 'Antarctica'),
				'AR' => array('name' => 'Argentina'),
				'AS' => array('name' => 'American Samoa'),
				'AT' => array('name' => 'Austria'),
				'AU' => array('name' => 'Australia'),
				'AW' => array('name' => 'Aruba'),
				'AX' => array('name' => 'Åland Islands'),
				'AZ' => array('name' => 'Azerbaijan'),
				'BA' => array('name' => 'Bosnia and Herzegovina'),
				'BB' => array('name' => 'Barbados'),
				'BD' => array('name' => 'Bangladesh'),
				'BE' => array('name' => 'Belgium'),
				'BF' => array('name' => 'Burkina Faso'),
				'BG' => array('name' => 'Bulgaria'),
				'BH' => array('name' => 'Bahrain'),
				'BI' => array('name' => 'Burundi'),
				'BJ' => array('name' => 'Benin'),
				'BL' => array('name' => 'Saint Barthélemy'),
				'BM' => array('name' => 'Bermuda'),
				'BN' => array('name' => 'Brunei'),
				'BO' => array('name' => 'Bolivia'),
				'BR' => array('name' => 'Brazil'),
				'BS' => array('name' => 'Bahamas'),
				'BT' => array('name' => 'Bhutan'),
				'BV' => array('name' => 'Bouvet Island'),
				'BW' => array('name' => 'Botswana'),
				'BY' => array('name' => 'Belarus'),
				'BZ' => array('name' => 'Belize'),
				'CA' => array('name' => 'Canada'),
				'CC' => array('name' => 'Cocos Islands'),
				'CD' => array('name' => 'Congo, Democratic Republic of the'),
				'CF' => array('name' => 'Central African Republic'),
				'CG' => array('name' => 'Congo, Republic of the'),
				'CH' => array('name' => 'Switzerland'),
				'CI' => array('name' => 'Ivory Coast'),
				'CK' => array('name' => 'Cook Islands'),
				'CL' => array('name' => 'Chile'),
				'CM' => array('name' => 'Cameroon'),
				'CN' => array('name' => 'China'),
				'CO' => array('name' => 'Colombia'),
				'CR' => array('name' => 'Costa Rica'),
				'CU' => array('name' => 'Cuba'),
				'CV' => array('name' => 'Cape Verde'),
				'CX' => array('name' => 'Christmas Island'),
				'CY' => array('name' => 'Cyprus'),
				'CZ' => array('name' => 'Czech Republic'),
				'DE' => array('name' => 'Germany'),
				'DJ' => array('name' => 'Djibouti'),
				'DK' => array('name' => 'Denmark'),
				'DM' => array('name' => 'Dominica'),
				'DO' => array('name' => 'Dominican Republic'),
				'DZ' => array('name' => 'Algeria'),
				'EC' => array('name' => 'Ecuador'),
				'EE' => array('name' => 'Estonia'),
				'EG' => array('name' => 'Egypt'),
				'EH' => array('name' => 'Western Sahara'),
				'ER' => array('name' => 'Eritrea'),
				'ES' => array('name' => 'Spain'),
				'ET' => array('name' => 'Ethiopia'),
				'FI' => array('name' => 'Finland'),
				'FJ' => array('name' => 'Fiji'),
				'FK' => array('name' => 'Falkland Islands'),
				'FM' => array('name' => 'Micronesia'),
				'FO' => array('name' => 'Faroe Islands'),
				'FR' => array('name' => 'France'),
				'GA' => array('name' => 'Gabon'),
				'GB' => array('name' => 'United Kingdom'),
				'GD' => array('name' => 'Grenada'),
				'GE' => array('name' => 'Georgia'),
				'GF' => array('name' => 'French Guiana'),
				'GG' => array('name' => 'Guernsey'),
				'GH' => array('name' => 'Ghana'),
				'GI' => array('name' => 'Gibraltar'),
				'GL' => array('name' => 'Greenland'),
				'GM' => array('name' => 'Gambia'),
				'GN' => array('name' => 'Guinea'),
				'GP' => array('name' => 'Guadeloupe'),
				'GQ' => array('name' => 'Equatorial Guinea'),
				'GR' => array('name' => 'Greece'),
				'GS' => array('name' => 'South Georgia'),
				'GT' => array('name' => 'Guatemala'),
				'GU' => array('name' => 'Guam'),
				'GW' => array('name' => 'Guinea-Bissau'),
				'GY' => array('name' => 'Guyana'),
				'HK' => array('name' => 'Hong Kong'),
				'HM' => array('name' => 'Heard Island and McDonald Islands'),
				'HN' => array('name' => 'Honduras'),
				'HR' => array('name' => 'Croatia'),
				'HT' => array('name' => 'Haiti'),
				'HU' => array('name' => 'Hungary'),
				'ID' => array('name' => 'Indonesia'),
				'IE' => array('name' => 'Ireland'),
				'IL' => array('name' => 'Israel'),
				'IM' => array('name' => 'Isle of Man'),
				'IN' => array('name' => 'India'),
				'IO' => array('name' => 'British Indian Ocean Territory'),
				'IQ' => array('name' => 'Iraq'),
				'IR' => array('name' => 'Iran'),
				'IS' => array('name' => 'Iceland'),
				'IT' => array('name' => 'Italy'),
				'JE' => array('name' => 'Jersey'),
				'JM' => array('name' => 'Jamaica'),
				'JO' => array('name' => 'Jordan'),
				'JP' => array('name' => 'Japan'),
				'KE' => array('name' => 'Kenya'),
				'KG' => array('name' => 'Kyrgyzstan'),
				'KH' => array('name' => 'Cambodia'),
				'KI' => array('name' => 'Kiribati'),
				'KM' => array('name' => 'Comoros'),
				'KN' => array('name' => 'Saint Kitts and Nevis'),
				'KP' => array('name' => 'North Korea'),
				'KR' => array('name' => 'South Korea'),
				'KW' => array('name' => 'Kuwait'),
				'KY' => array('name' => 'Cayman Islands'),
				'KZ' => array('name' => 'Kazakhstan'),
				'LA' => array('name' => 'Laos'),
				'LB' => array('name' => 'Lebanon'),
				'LC' => array('name' => 'Saint Lucia'),
				'LI' => array('name' => 'Liechtenstein'),
				'LK' => array('name' => 'Sri Lanka'),
				'LR' => array('name' => 'Liberia'),
				'LS' => array('name' => 'Lesotho'),
				'LT' => array('name' => 'Lithuania'),
				'LU' => array('name' => 'Luxembourg'),
				'LV' => array('name' => 'Latvia'),
				'LY' => array('name' => 'Libya'),
				'MA' => array('name' => 'Morocco'),
				'MC' => array('name' => 'Monaco'),
				'MD' => array('name' => 'Moldova'),
				'ME' => array('name' => 'Montenegro'),
				'MF' => array('name' => 'Saint Martin'),
				'MG' => array('name' => 'Madagascar'),
				'MH' => array('name' => 'Marshall Islands'),
				'MK' => array('name' => 'Macedonia'),
				'ML' => array('name' => 'Mali'),
				'MM' => array('name' => 'Myanmar'),
				'MN' => array('name' => 'Mongolia'),
				'MO' => array('name' => 'Macau'),
				'MP' => array('name' => 'Northern Mariana Islands'),
				'MQ' => array('name' => 'Martinique'),
				'MR' => array('name' => 'Mauritania'),
				'MS' => array('name' => 'Montserrat'),
				'MT' => array('name' => 'Malta'),
				'MU' => array('name' => 'Mauritius'),
				'MV' => array('name' => 'Maldives'),
				'MW' => array('name' => 'Malawi'),
				'MX' => array('name' => 'Mexico'),
				'MY' => array('name' => 'Malaysia'),
				'MZ' => array('name' => 'Mozambique'),
				'NA' => array('name' => 'Namibia'),
				'NC' => array('name' => 'New Caledonia'),
				'NE' => array('name' => 'Niger'),
				'NF' => array('name' => 'Norfolk Island'),
				'NG' => array('name' => 'Nigeria'),
				'NI' => array('name' => 'Nicaragua'),
				'NL' => array('name' => 'Netherlands'),
				'NO' => array('name' => 'Norway'),
				'NP' => array('name' => 'Nepal'),
				'NR' => array('name' => 'Nauru'),
				'NU' => array('name' => 'Niue'),
				'NZ' => array('name' => 'New Zealand'),
				'OM' => array('name' => 'Oman'),
				'PA' => array('name' => 'Panama'),
				'PE' => array('name' => 'Peru'),
				'PF' => array('name' => 'French Polynesia'),
				'PG' => array('name' => 'Papua New Guinea'),
				'PH' => array('name' => 'Philippines'),
				'PK' => array('name' => 'Pakistan'),
				'PL' => array('name' => 'Poland'),
				'PM' => array('name' => 'Saint Pierre and Miquelon'),
				'PN' => array('name' => 'Pitcairn Islands'),
				'PR' => array('name' => 'Puerto Rico'),
				'PS' => array('name' => 'Palestine'),
				'PT' => array('name' => 'Portugal'),
				'PW' => array('name' => 'Palau'),
				'PY' => array('name' => 'Paraguay'),
				'QA' => array('name' => 'Qatar'),
				'RE' => array('name' => 'Réunion'),
				'RO' => array('name' => 'Romania'),
				'RS' => array('name' => 'Serbia'),
				'RU' => array('name' => 'Russia'),
				'RW' => array('name' => 'Rwanda'),
				'SA' => array('name' => 'Saudi Arabia'),
				'SB' => array('name' => 'Solomon Islands'),
				'SC' => array('name' => 'Seychelles'),
				'SD' => array('name' => 'Sudan'),
				'SE' => array('name' => 'Sweden'),
				'SG' => array('name' => 'Singapore'),
				'SH' => array('name' => 'Saint Helena'),
				'SI' => array('name' => 'Slovenia'),
				'SJ' => array('name' => 'Svalbard and Jan Mayen Island'),
				'SK' => array('name' => 'Slovakia'),
				'SL' => array('name' => 'Sierra Leone'),
				'SM' => array('name' => 'San Marino'),
				'SN' => array('name' => 'Senegal'),
				'SO' => array('name' => 'Somalia'),
				'SR' => array('name' => 'Suriname'),
				'ST' => array('name' => 'Saint Tome and Principe'),
				'SV' => array('name' => 'El Salvador'),
				'SX' => array('name' => 'Sint Maarten'),
				'SY' => array('name' => 'Syria'),
				'SZ' => array('name' => 'Swaziland'),
				'TC' => array('name' => 'Turks and Caicos Islands'),
				'TD' => array('name' => 'Chad'),
				'TF' => array('name' => 'French Southern Lands'),
				'TG' => array('name' => 'Togo'),
				'TH' => array('name' => 'Thailand'),
				'TJ' => array('name' => 'Tajikistan'),
				'TK' => array('name' => 'Tokelau'),
				'TL' => array('name' => 'East Timor'),
				'TM' => array('name' => 'Turkmenistan'),
				'TN' => array('name' => 'Tunisia'),
				'TO' => array('name' => 'Tonga'),
				'TR' => array('name' => 'Turkey'),
				'TT' => array('name' => 'Trinidad and Tobago'),
				'TV' => array('name' => 'Tuvalu'),
				'TW' => array('name' => 'Taiwan'),
				'TZ' => array('name' => 'Tanzania'),
				'UA' => array('name' => 'Ukraine'),
				'UG' => array('name' => 'Uganda'),
				'UM' => array('name' => 'United States Minor Outlying Islands'),
				'US' => array('name' => 'United States of America'),
				'UY' => array('name' => 'Uruguay'),
				'UZ' => array('name' => 'Uzbekistan'),
				'VA' => array('name' => 'Vatican City'),
				'VC' => array('name' => 'Saint Vincent and the Grenadines'),
				'VE' => array('name' => 'Venezuela'),
				'VG' => array('name' => 'British Virgin Islands'),
				'VI' => array('name' => 'United States Virgin Islands'),
				'VN' => array('name' => 'Vietnam'),
				'VU' => array('name' => 'Vanuatu'),
				'WF' => array('name' => 'Wallis and Futuna'),
				'WS' => array('name' => 'Samoa'),
				'YE' => array('name' => 'Yemen'),
				'YT' => array('name' => 'Mayotte'),
				'ZA' => array('name' => 'South Africa'),
				'ZM' => array('name' => 'Zambia'),
				'ZW' => array('name' => 'Zimbabwe')
			);
		}
		return self::$_countries;
	}
}
