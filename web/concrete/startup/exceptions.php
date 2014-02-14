<?php defined('C5_EXECUTE') or die('Access Denied.');

/** Helper class to handle unhandled errors/exceptions */
class Concrete5_Problems_Handler {
	/** Do we have already handled the unhandler error/exception?
	* @var bool
	*/
	private static $handled = false;
	/** Handler for unhandled exceptions
	* @param Exception $e
	*/
	public static function handleException($e) {
		if(self::$handled) {
			return;
		}
		self::$handled = true;
		self::logError($e->getMessage(), $e->getFile(), $e->getLine(), $e->getCode(), $e->getTraceAsString());
		if (Config::get('SITE_DEBUG_LEVEL') == DEBUG_DISPLAY_ERRORS) {
			View::renderError(t('An unexpected error occurred.'), $e->getMessage(), $e);
		} else {
			View::renderError(t('An unexpected error occurred.'), t('An error occurred while processing this request.'), $e);
		}
	}
	/** Called on shutdown, to check if execution halted due to an E_ERROR */
	public static function handleShutdown() {
		if(self::$handled) {
			return;
		}
		self::$handled = true;
		$lastError = error_get_last();
		if(is_array($lastError)) {
			switch($lastError['type']) {
				case E_ERROR:
					self::logError($lastError['message'], $lastError['file'], $lastError['line'], 'E_ERROR');
					break;
			}
		}
	}
	/** Save an unhandled exception to the system log (or to a file log if the log system failed),
	* @param string $message
	* @param string $file
	* @param int|null $line
	* @param int|string $code
	* @param string $stackTrace
	*/
	protected static function logError($message, $file = '', $line = null, $code = '', $stackTrace = '') {
		if(!ENABLE_LOG_ERRORS) {
			return;
		}
		$full = t('Exception Occurred: ');
		if(strlen($file)) {
			$full .= $file;
			if(!empty($line)) {
				$full .= ':' . $line;
			}
			$full .= "\n";
		}
		$full .= $message;
		if(!empty($code)) {
			$full .= " ($code)";
		}
		if(strlen($stackTrace)) {
			$full .= "\n\n" . $stackTrace;
		}
		$savedToDB = false;
		try {
			$db = Loader::db();
			$tables = $db->MetaTables();
			if(in_array('Logs', $tables)) {
				$l = new Log(LOG_TYPE_EXCEPTIONS, true, true);
				$l->write($full);
				$l->close();
				$savedToDB = true;
			}
		}
		catch(Exception $foo) {
		}
		if(!$savedToDB) {
			if(!is_dir(DIR_BASE . '/files/tmp')) {
				@mkdir(DIR_BASE . '/files/tmp', DIRECTORY_PERMISSIONS_MODE, true);
			}
			$hFile = @fopen(DIR_BASE . '/files/tmp/unhandled-exceptions.log', 'a');
			if($hFile != false) {
				@fwrite($hFile, sprintf("%s\t%s", date('Y-m-d H.i.s'), $full));
				@fflush($hFile);
				@fclose($hFile);
			}
		}
	}
}

set_exception_handler('Concrete5_Problems_Handler::handleException');
register_shutdown_function('Concrete5_Problems_Handler::handleShutdown');
