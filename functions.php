<?php
/**
 * Copyright 2019 LABOR.digital
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Last modified: 2019.05.16 at 14:08
 */

namespace Labor\Dbg;

use Kint\Kint;
use PhpConsole;

if (!function_exists("Labor\\Dbg\\dbg")) {
	
	/**
	 * Dumps the given arguments to the screen
	 */
	function dbg() {
		if (!LABOR_DBG_ENABLED) return;
		$args = func_get_args();
		Kint::dump(...$args);
	}
}

if (!function_exists("Labor\\Dbg\\dbge")) {
	
	/**
	 * Dumps the given arguments to the screen and stops the execution
	 */
	function dbge() {
		if (!LABOR_DBG_ENABLED) return;
		$args = func_get_args();
		Kint::dump(...$args);
		call_user_func_array([Kint::class, "dump"], func_get_args());
		exit();
	}
}

if (!function_exists("Labor\\Dbg\\trace")) {
	
	/**
	 * Dumps the debug backtrace to the screen
	 *
	 * @param int $offset
	 */
	function trace(int $offset = 0) {
		if (!LABOR_DBG_ENABLED) return;
		$trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
		$trace = array_slice($trace, $offset);
		Kint::dump($trace);
	}
}

if (!function_exists("Labor\\Dbg\\tracee")) {
	
	/**
	 * Dumps the debug backtrace to the screen and kills the script
	 *
	 * @param int $offset
	 */
	function tracee(int $offset = 0) {
		if (!LABOR_DBG_ENABLED) return;
		trace($offset + 1);
		exit();
	}
}

if (!function_exists("Labor\\Dbg\\logConsole")) {
	
	/**
	 * Dumps the given arguments to the javascript console when using the
	 * php console chrome extension https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef
	 */
	function logConsole() {
		if (!LABOR_DBG_ENABLED) return;
		$dispatcher = PhpConsole\Connector::getInstance()->getDebugDispatcher();
		$dispatcher->detectTraceAndSource = TRUE;
		foreach (func_get_args() as $arg) {
			$dispatcher->dispatchDebug($arg, "PHP-DEBUG", 1);
		}
	}
}


if (!function_exists("Labor\\Dbg\\logFile")) {
	
	/**
	 * Dumps the given arguments into a logfile.
	 *
	 * The logfile will be created either at:
	 * * LABOR_DBG_LOG_DIR/labor_debug_logfile.log if this constant contains a writable directory path
	 * * /var/www/logs/labor_debug_logfile.log if the logs directory is writable
	 * * /$SYS_TEMP_DIR/labor_debug_logfile.log
	 *
	 * @return bool Returns true if the log was written, or if the debug mode is disabled. False if the file could not be
	 *              written
	 */
	function logFile(): bool {
		if (!LABOR_DBG_ENABLED) return TRUE;
		
		// Try to find logfile
		if (defined("LABOR_DBG_LOG_DIR") && (is_writable(rtrim(LABOR_DBG_LOG_DIR, "/\\") . "/labor_debug_logfile.log") || is_writable(LABOR_DBG_LOG_DIR)))
			$logFile = rtrim(LABOR_DBG_LOG_DIR, "/\\") . "/labor_debug_logfile.log";
		else if (is_writable("/var/www/logs/labor_debug_logfile.log") || !file_exists("/var/www/logs/labor_debug_logfile.log") && is_writable("/var/www/logs/"))
			$logFile = "/var/www/logs/labor_debug_logfile.log";
		else if (is_writable(sys_get_temp_dir() . "/labor_debug_logfile.log") || !file_exists(sys_get_temp_dir() . "/labor_debug_logfile.log") && is_writable("/var/www/logs/"))
			$logFile = sys_get_temp_dir() . "/labor_debug_logfile.log";
		else
			return FALSE;
		
		// Dump the contents
		$args = func_get_args();
		$returnBckp = Kint::$return;
		$rendererBckp = Kint::$mode_default;
		Kint::$mode_default = Kint::MODE_TEXT;
		Kint::$return = TRUE;
		$content = Kint::dump(...$args);
		Kint::$return = $returnBckp;
		Kint::$mode_default = $rendererBckp;
		
		// Add additional data
		if (isset($_SERVER) && isset($_SERVER["SERVER_NAME"]) && isset($_SERVER["REQUEST_URI"]))
			$content .= "URL: " . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"] . PHP_EOL;
		else $content .= "Called from CLI";
		
		// Add footer if required
		if (file_exists($logFile) && filesize($logFile) > 0)
			$content = PHP_EOL . PHP_EOL . PHP_EOL . $content;
		else touch($logFile);
		
		// Write the file
		file_put_contents($logFile, $content, FILE_APPEND);
		return TRUE;
	}
	
}
