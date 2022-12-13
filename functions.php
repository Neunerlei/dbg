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
    function dbg(...$args): void
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
    function dbge(...$args): void
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
    function trace(array $options = []): void
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
    function tracee(array $options = []): void
    {
        Tracer::trace(__FUNCTION__, func_get_args(), true);
    }
}

if (! function_exists('logConsole')) {
    /**
     * Dumps the given arguments to the javascript console when using the
     * php console Chrome extension https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef
     *
     * Set the last, given value to TRUE (with more than a single value) to also print the log to the console
     *
     * @param array $args
     */
    function logConsole(...$args): void
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