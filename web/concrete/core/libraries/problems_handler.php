<?php defined('C5_EXECUTE') or die('Access Denied.');

/** Helper class to handle unhandled errors/exceptions */
class Concrete5_Library_ProblemsHandler {
	/** The previous exception handler
	* @var callable|null
	*/
	protected static $previousExceptionHandler;
	/** Do we have already handled the unhandled error/exception?
	* @var bool
	*/
	protected static $handled = false;
	/** Initializes the ProblemsHandler library. */
	public static function initialize() {
		self::$previousExceptionHandler = set_exception_handler('ProblemsHandler::handleException');
		register_shutdown_function('ProblemsHandler::handleShutdown');
	}
	/** Handler for unhandled exceptions
	* @param Exception $e
	*/
	public static function handleException($e) {
		if(self::$handled) {
			if(is_callable(self::$previousExceptionHandler)) {
				call_user_func(self::$previousExceptionHandler, $e);
			}
			return;
		}
		self::$handled = true;
		self::handleProblem($e->getMessage(), $e->getFile(), $e->getLine(), $e->getCode(), $e->getTraceAsString());
		if(is_callable(self::$previousExceptionHandler)) {
			call_user_func(self::$previousExceptionHandler, $e);
		}
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
					self::handleProblem($lastError['message'], $lastError['file'], $lastError['line'], 'E_ERROR');
					break;
			}
		}
	}
	/** Handles a problem (exception/fatal error)
	* @param string $message
	* @param string $file
	* @param int|null $line
	* @param int|string $code
	* @param string $stackTrace
	*/
	protected static function handleProblem($message, $file = '', $line = null, $code = '', $stackTrace = '') {
		if(ENABLE_LOG_ERRORS) {
			$full = function_exists('t') ? t('Exception Occurred: ') : 'Exception Occurred: ';
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
			if(class_exists('Database', false)) {
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
			}
			if(!$savedToDB && defined('EMERGENCY_LOG_FILENAME') && strlen(EMERGENCY_LOG_FILENAME)) {
				$hFile = @fopen(EMERGENCY_LOG_FILENAME, 'a');
				if($hFile) {
					@fwrite($hFile, sprintf("%s\n%s\t%s\n\n", str_repeat('#', 20), @date('Y-m-d H.i.s'), $full));
					@fflush($hFile);
					@fclose($hFile);
				}
			}
		}
		Events::fire('on_problem', $message, $file,  $line, $code, $stackTrace);
	}
}
