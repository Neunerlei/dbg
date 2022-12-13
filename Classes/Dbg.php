<?php
/*
 * Copyright 2021 LABOR.digital
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
 * Last modified: 2021.11.27 at 15:12
 */

declare(strict_types=1);


namespace Neunerlei\Dbg;


use InvalidArgumentException;
use Kint\Kint;
use Kint\Parser\BlacklistPlugin;
use Kint\Parser\ColorPlugin;
use Kint\Parser\DateTimePlugin;
use Kint\Parser\FsPathPlugin;
use Kint\Parser\IteratorPlugin;
use Kint\Parser\JsonPlugin;
use Kint\Parser\MicrotimePlugin;
use Kint\Parser\SerializePlugin;
use Kint\Parser\TimestampPlugin;
use Kint\Parser\ToStringPlugin;
use Kint\Renderer\RichRenderer;
use Neunerlei\Dbg\Plugins\DedupePlugin;
use Neunerlei\Dbg\Renderer\ExtendedCliRenderer;
use Neunerlei\Dbg\Renderer\ExtendedTextRenderer;

class Dbg
{
    public const HOOK_TYPE_PRE = 'preHooks';
    public const HOOK_TYPE_POST = 'postHooks';
    
    /**
     * True if the debugger was initialized and does not need to be initialized again
     *
     * @var bool
     */
    protected static $initialized = false;
    
    /**
     * The main configuration storage
     *
     * @var array
     */
    protected static $config
        = [
            'enabled' => true,
            'environmentDetection' => false,
            'envVarKey' => 'PROJECT_ENV',
            'envVarValue' => 'dev',
            'cliIsDev' => true,
            'debugReferrer' => null,
            'preHooks' => [],
            'postHooks' => [],
            'consolePassword' => null,
            'logDir' => null,
            'logStream' => null,
        ];
    
    /**
     * The request id to append to log lines
     *
     * @var string
     */
    protected static $requestId;
    
    /**
     * Initializes the debugger by applying our configuration to the Kint debugging tool
     */
    public static function init(): void
    {
        if (static::$initialized) {
            return;
        }
        
        static::$initialized = true;
        
        Kint::$enabled_mode = true;
        RichRenderer::$folder = false;
        RichRenderer::$access_paths = false;
        Kint::$renderers[Kint::MODE_TEXT] = ExtendedTextRenderer::class;
        Kint::$renderers[Kint::MODE_CLI] = ExtendedCliRenderer::class;
        Kint::$depth_limit = 8;
        
        Kint::$aliases[] = 'dbg';
        Kint::$aliases[] = 'dbge';
        Kint::$aliases[] = 'logconsole';
        Kint::$aliases[] = 'logfile';
        Kint::$aliases[] = 'logstream';
        Kint::$aliases[] = 'trace';
        Kint::$aliases[] = 'tracee';
        
        Kint::$plugins = [
            BlacklistPlugin::class,
            DedupePlugin::class,
            DateTimePlugin::class,
            TimestampPlugin::class,
            IteratorPlugin::class,
            ToStringPlugin::class,
            FsPathPlugin::class,
            ColorPlugin::class,
            JsonPlugin::class,
            MicrotimePlugin::class,
            SerializePlugin::class,
        ];
        
        // If we detect either a client that does not accept html, or the request
        // is executed using an "AJAX" request, we will use the text-renderer instead of the rich-renderer
        if (isset($_SERVER)) {
            if (stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'text/html') !== 0
                || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
                || strtolower($_SERVER['X-Requested-With'] ?? '') === 'xmlhttprequest') {
                Kint::$mode_default = Kint::MODE_TEXT;
            }
        }
    }
    
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
     * @param   string|null  $key
     * @param   null         $value
     *
     * @return bool|mixed
     */
    public static function config(?string $key = null, $value = null)
    {
        static::init();
        
        if (empty($key)) {
            return static::$config;
        }
        
        if (! array_key_exists($key, static::$config)) {
            throw new InvalidArgumentException('The given config key: ' . $key . ' was not found!');
        }
        
        if ($value === null) {
            return static::$config[$key];
        }
        
        switch ($key) {
            case static::HOOK_TYPE_PRE:
            case static::HOOK_TYPE_POST:
                if (is_callable($value)) {
                    static::$config[$key][] = $value;
                } elseif (is_array($value)) {
                    static::$config[$key] = $value;
                } else {
                    throw new InvalidArgumentException('The given value for key: ' . $key . ' has to be an array or a callback!');
                }
                break;
            case 'enabled':
                static::$config[$key] = Kint::$enabled_mode = $value === true;
                break;
            default:
                static::$config[$key] = $value;
        }
        
        return true;
    }
    
    /**
     * Returns true if the debugging capabilities are enabled, false if not
     *
     * @return bool
     */
    public static function isEnabled(): bool
    {
        $conf = static::config();
        
        // Disabled? -> No
        if (($conf['enabled'] ?? true) === false) {
            return false;
        }
        
        // NO Environment detection? -> Yes
        if (($conf['environmentDetection'] ?? false) === false) {
            return true;
        }
        
        // Env variable matches expected value? -> Yes
        if (getenv(($conf['envVarKey'] ?? 'PROJECT_ENV')) === (string)($conf['envVarValue'] ?? 'dev')) {
            return true;
        }
        
        // CLI is treated as dev? -> Yes
        if (($conf['cliIsDev'] ?? true) && PHP_SAPI === 'cli') {
            return true;
        }
        
        // Debug referrer is set and matches? -> Yes
        if (is_string($conf['debugReferrer'] ?? null)) {
            return ($_SERVER['HTTP_REFERER'] ?? null) === $conf['debugReferrer'];
        }
        
        return false;
    }
    
    /**
     * Runs a list of registered hook functions
     *
     * @param   string  $type          The hook type to execute
     * @param   string  $functionName  The name of the function to execute the hooks for
     * @param   array   $args          The function arguments to pass to the hook functions
     */
    public static function runHooks(string $type, string $functionName, array $args): void
    {
        $conf = static::config($type);
        if (! is_array($conf)) {
            return;
        }
        
        foreach ($conf as $callback) {
            if (is_callable($callback)) {
                $callback($type, $functionName, $args);
            }
        }
    }
    
    /**
     * Generates/reads a unique request id that will be added to log outputs
     *
     * @return string
     */
    public static function getRequestId(): string
    {
        if (isset(static::$requestId)) {
            return static::$requestId;
        }
        
        if (isset($_SERVER['HTTP_X_REQUEST_ID'])) {
            return static::$requestId = $_SERVER['HTTP_X_REQUEST_ID'];
        }
        
        return static::$requestId = uniqid('request_', true);
    }
}