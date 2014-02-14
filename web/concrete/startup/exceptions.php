<?php defined('C5_EXECUTE') or die('Access Denied.');

Loader::library('problems_handler');

set_exception_handler('ProblemsHandler::handleException');
register_shutdown_function('ProblemsHandler::handleShutdown');
