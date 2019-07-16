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

use Kint\Kint;
use PhpConsole\Connector;

if (!function_exists("dbg")) {
	/**
	 * Dumps the given arguments to the screen
	 */
	function dbg(...$args) {
		if (!isDbgEnabled()) return;
		
		// Call hooks
		_dbgIntCallHooks("preHooks", __FUNCTION__, $args);
		
		Kint::dump(...$args);
		
		// Call hooks
		_dbgIntCallHooks("postHooks", __FUNCTION__, $args);
	}
}

if (!function_exists("dbge")) {
	
	/**
	 * Dumps the given arguments to the screen and stops the execution
	 */
	function dbge(...$args) {
		if (!isDbgEnabled()) return;
		
		// Call hooks
		_dbgIntCallHooks("preHooks", __FUNCTION__, $args);
		
		Kint::dump(...$args);
		
		// Call hooks
		_dbgIntCallHooks("postHooks", __FUNCTION__, $args);
		
		exit();
	}
}

if (!function_exists("trace")) {
	
	/**
	 * Dumps the debug backtrace to the screen
	 *
	 * @param int $offset
	 */
	function trace(int $offset = 0) {
		if (!isDbgEnabled()) return;
		
		// Call hooks
		_dbgIntCallHooks("preHooks", __FUNCTION__, func_get_args());
		
		$trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT);
		$trace = array_slice($trace, $offset);
		Kint::dump($trace);
		
		// Call hooks
		_dbgIntCallHooks("postHooks", __FUNCTION__, func_get_args());
	}
}

if (!function_exists("tracee")) {
	
	/**
	 * Dumps the debug backtrace to the screen and kills the script
	 *
	 * @param int $offset
	 */
	function tracee(int $offset = 0) {
		if (!isDbgEnabled()) return;
		trace($offset + 1);
		exit();
	}
}

if (!function_exists("logConsole")) {
	/**
	 * Dumps the given arguments to the javascript console when using the
	 * php console chrome extension https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef
	 *
	 * Set the last, given value to TRUE (with more than a single value) to also print the log to the console
	 */
	function logConsole(...$args) {
		if (!isDbgEnabled()) return;
		
		// Call hooks
		_dbgIntCallHooks("preHooks", __FUNCTION__, $args);
		
		$connector = Connector::getInstance();
		if (!empty(dbgConfig("consolePassword"))) $connector->setPassword(dbgConfig("consolePassword"));
		$dispatcher = $connector->getDebugDispatcher();
		$dispatcher->detectTraceAndSource = count($args) > 0 && end($args) === TRUE;
		foreach ($args as $arg) $dispatcher->dispatchDebug($arg, "PHP-DEBUG", 1);
		
		// Call hooks
		_dbgIntCallHooks("postHooks", __FUNCTION__, $args);
	}
}

if (!function_exists("logFile")) {
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
	function logFile(...$args): bool {
		if (!isDbgEnabled()) return TRUE;
		
		// Call hooks
		_dbgIntCallHooks("preHooks", __FUNCTION__, $args);
		
		// Try to find logfile
		$logDir = dbgConfig("logDir");
		if (is_string($logDir) && (is_writable(rtrim($logDir, "/\\") . "/labor_debug_logfile.log") || is_writable($logDir)))
			$logFile = rtrim($logDir, "/\\") . "/labor_debug_logfile.log";
		else if (is_writable("/var/www/logs/labor_debug_logfile.log") || !file_exists("/var/www/logs/labor_debug_logfile.log") && is_writable("/var/www/logs/"))
			$logFile = "/var/www/logs/labor_debug_logfile.log";
		else if (is_writable(sys_get_temp_dir() . "/labor_debug_logfile.log") || !file_exists(sys_get_temp_dir() . "/labor_debug_logfile.log") && is_writable("/var/www/logs/"))
			$logFile = sys_get_temp_dir() . "/labor_debug_logfile.log";
		else
			return FALSE;
		
		// Dump the contents
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
		
		// Call hooks
		_dbgIntCallHooks("postHooks", __FUNCTION__, $args);
		return TRUE;
	}
}

if (!function_exists("dbgConfig")) {
	/**
	 * Used to configure the debugging context.
	 *
	 * Possible values:
	 *
	 * - enabled: (bool) default: TRUE | Master switch to enable/disable the debugging functionality. If you set this to
	 * false, none of the functions will do or output anything.
	 * - environmentDetection: (bool) default: FALSE | Enables the environment detection mechanism if set to true.
	 * - envVarKey: (string) default: PROJECT_ENV | Determines the name of the environment variable to look for when
	 * enabling the debug feature.
	 * - envVarValue: (string) default: dev | Used in combination with "envVarKey" and determines which value to expect
	 * from the configured environment variable to enable the debugger.
	 * - cliIsDev: (bool) default: TRUE | Determines if the debugger should always output stuff in a CLI environment or
	 * not.
	 * - debugReferrer: (string|NULL) default NULL | If set this will be expected as the referrer to enable the debugger
	 * capabilities.
	 * - preHooks: (callable|array) | One or multiple callbacks to run in front of each debugger function
	 * (dbg,dbge,trace,tracee,...). Useful for extending the functionality. Each callback will receive $hookType,
	 * $callingFunction and $givenArguments as arguments.
	 * - postHooks: (callable|array) | Same as "preHooks" but run after the debug output.
	 * - consolePassword: (string|null) default: NULL | If set the phpConsole will require this value as password before
	 * printing the console output to the browser.
	 * - logDir: (string|NULL) default: NULL | If set, the logFile() function will dump the logfile to the given director.
	 * Make sure it exists and is writable by the webserver!
	 *
	 * @param string $key
	 * @param null   $value
	 *
	 * @return bool|mixed
	 */
	function dbgConfig(string $key = "", $value = NULL) {
		if (empty($key)) return $GLOBALS["LABOR_DBG_CONFIG"];
		if (!array_key_exists($key, $GLOBALS["LABOR_DBG_CONFIG"]))
			throw new \InvalidArgumentException("The given config key: " . $key . " was not found!");
		if ($value === NULL) return $GLOBALS["LABOR_DBG_CONFIG"][$key];
		
		switch ($key) {
			case "preHooks":
			case "postHooks":
				if (is_callable($value)) $GLOBALS["LABOR_DBG_CONFIG"][$key][] = $value;
				else if (is_array($value)) $GLOBALS["LABOR_DBG_CONFIG"][$key] = $value;
				else throw new \InvalidArgumentException("The given value for key: " . $key . " has to be an array or a callback!");
				break;
			case "enabled":
				$GLOBALS["LABOR_DBG_CONFIG"][$key] = Kint::$enabled_mode = $value === TRUE;
				break;
			default:
				$GLOBALS["LABOR_DBG_CONFIG"][$key] = $value;
		}
		return TRUE;
	}
	
	if (!defined("LABOR_DBG_CONFIG_LOADED")) define("LABOR_DBG_CONFIG_LOADED", TRUE);
}

if (!function_exists("isDbgEnabled")) {
	/**
	 * Returns true if the debugger is enabled
	 * @return bool
	 */
	function isDbgEnabled(): bool {
		$conf = $GLOBALS["LABOR_DBG_CONFIG"];
		return
			// Check if the debugging is enabled
			$conf["enabled"] === TRUE && (
				// Check if we can use the environment detection
				$conf["environmentDetection"] === FALSE || (
					// Check env variable
					getenv($conf["envVarKey"]) === "" . $conf["envVarValue"] ||
					// Check if we can run in cli mode
					$conf["cliIsDev"] && php_sapi_name() === "cli" ||
					// Check if we got the correct referrer
					is_string($conf["debugReferrer"]) && isset($_SERVER["HTTP_REFERER"]) && $_SERVER["HTTP_REFERER"] === $conf["debugReferrer"]
				)
			);
	}
	
}

if (!function_exists("_dbgIntCallHooks")) {
	/**
	 * Internal helper to keep the hook execution dry...
	 *
	 * @param string $type
	 * @param string $function
	 * @param array  $args
	 */
	function _dbgIntCallHooks(string $type, string $function, array $args) {
		if (!is_array(dbgConfig($type))) return;
		foreach (dbgConfig($type) as $callback)
			call_user_func($callback, $type, $function, $args);
	}
	
}