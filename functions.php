<?php
declare(strict_types=1);

/**
 * Copyright 2020 Martin Neundorfer (Neunerlei)
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
 * Last modified: 2020.02.27 at 11:52
 */

use Neunerlei\Dbg\Dbg;
use Neunerlei\Dbg\Module\Dumper;
use Neunerlei\Dbg\Module\FileDumper;
use Neunerlei\Dbg\Module\PhpConsole as PhpConsoleModule;
use Neunerlei\Dbg\Module\StreamDumper;
use Neunerlei\Dbg\Module\Tracer;

if (! function_exists('dbg')) {
    /**
     * Dumps the given arguments to the screen
     *
     * @param   array  $args
     */
    function dbg(...$args)
    {
        Dumper::dump(__FUNCTION__, $args, false);
    }
}

if (! function_exists('dbge')) {
    /**
     * Dumps the given arguments to the screen and stops the execution
     *
     * @param   array  $args
     */
    function dbge(...$args)
    {
        Dumper::dump(__FUNCTION__, $args, true);
    }
}

if (! function_exists('trace')) {
    /**
     * Dumps the debug backtrace to the screen
     *
     * @param   array  $options  Options for the trace generation
     *                           - offset int (0): The offset from the top of the trace that should be ignored
     *                           - length int: By default all steps are show, this option marks the maximum
     *                           number of steps to be shown.
     *                           - withArguments bool|null (NULL): If set to FALSE the arguments and objects will be ignored.
     *                           If set to TRUE they will always be shown
     *                           If left as NULL, the args and objects will be shown except the script runs in the CLI
     */
    function trace(array $options = [])
    {
        Tracer::trace(__FUNCTION__, func_get_args(), false);
    }
}

if (! function_exists('tracee')) {
    /**
     * Dumps the debug backtrace to the screen and kills the script
     *
     * @param   array  $options  Options for the trace generation
     *                           - offset int (0): The offset from the top of the trace that should be ignored
     *                           - length int: By default all steps are show, this option marks the maximum
     *                           number of steps to be shown.
     *                           - withArguments bool|null (NULL): If set to true the arguments and objects will be ignored.
     *                           If set to false they will always be shown
     *                           If left as NULL, the args and objects will be shown except the script runs in the CLI
     */
    function tracee(array $options = [])
    {
        Tracer::trace(__FUNCTION__, func_get_args(), true);
    }
}

if (! function_exists('logConsole')) {
    /**
     * Dumps the given arguments to the javascript console when using the
     * php console chrome extension https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef
     *
     * Set the last, given value to TRUE (with more than a single value) to also print the log to the console
     *
     * @param   array  $args
     */
    function logConsole(...$args)
    {
        PhpConsoleModule::log(__FUNCTION__, $args);
    }
}

if (! function_exists('logFile')) {
    /**
     * Dumps the given arguments into a logfile.
     *
     * The logfile will be created either at:
     * * $_ENV["_DBG_LOG_DIR"]/dbg_debug_logfile.log if this environment variable contains a writable directory path
     * * dbgConfig("logDir") /dbg_debug_logfile.log if the environment variable is empty and the directory is writable
     * * /var/www/logs/dbg_debug_logfile.log if the logs directory is writable
     * * /$SYS_TEMP_DIR/dbg_debug_logfile.log
     *
     * @param   array  $args
     *
     * @return bool Returns true if the log was written, or if the debug mode is disabled. False if the file could not be
     *              written
     */
    function logFile(...$args): bool
    {
        return FileDumper::dump(__FUNCTION__, $args);
    }
}

if (! function_exists('logStream')) {
    /**
     * Dumps the given arguments into a stream (by default the stdout)
     *
     * @param   array  $args
     *
     * @return bool Returns true if the log was written, or if the debug mode is disabled.
     * False if the stream could not be written
     */
    function logStream(...$args): bool
    {
        return StreamDumper::dump(__FUNCTION__, $args);
    }
}

if (! function_exists('dbgConfig')) {
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
     * @param   string  $key
     * @param   null    $value
     *
     * @return bool|mixed
     * @deprecated use Dbg::config instead
     * @see        Dbg::config
     */
    function dbgConfig(string $key = '', $value = null)
    {
        return Dbg::config($key, $value);
    }
    
    // @todo remove this in the next major version
    if (! defined('_DBG_CONFIG_LOADED')) {
        define('_DBG_CONFIG_LOADED', true);
    }
}

if (! function_exists('isDbgEnabled')) {
    /**
     * Returns true if the debugger is enabled
     *
     * @return bool
     * @deprecated use Dbg::isEnabled instead
     * @see        Dbg::isEnabled()
     */
    function isDbgEnabled(): bool
    {
        return Dbg::isEnabled();
    }
    
}

if (! function_exists('_dbgIntCallHooks')) {
    /**
     * Internal helper to keep the hook execution dry...
     *
     * @param   string  $type
     * @param   string  $function
     * @param   array   $args
     *
     * @deprecated use Dbg::runHooks instead
     * @see        Dbg::runHooks()
     */
    function _dbgIntCallHooks(string $type, string $function, array $args)
    {
        Dbg::runHooks($type, $function, $args);
    }
    
}