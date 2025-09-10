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


use Kint\Kint;
use Kint\Parser\ArrayLimitPlugin;
use Kint\Parser\BlacklistPlugin;
use Kint\Parser\ClosurePlugin;
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
use Neunerlei\Dbg\Renderer\ExtendedCliRenderer;
use Neunerlei\Dbg\Renderer\ExtendedTextRenderer;
use Neunerlei\Dbg\Util\Config;
use Neunerlei\Dbg\Util\ConfigLoader;
use Neunerlei\Dbg\Util\EnvironmentDetection;
use Neunerlei\Dbg\Util\Hooks;

class Dbg
{
    protected static Config|null $config = null;
    protected static Hooks|null $hooks = null;
    protected static EnvironmentDetection|null $envDetection = null;
    protected static string|null $requestId = null;
    protected static bool $isAjax = false;
    protected static bool $isCli = false;
    
    /**
     * Initializes the debugger by applying our configuration to the Kint debugging tool
     */
    public static function init(): void
    {
        if (static::$config !== null) {
            return;
        }
        
        static::$config = new Config();
        static::$hooks = new Hooks();
        static::$envDetection = new EnvironmentDetection(static::$config);
        
        static::$hooks->trigger(HookType::BEFORE_INIT, static::$config);
        
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
            DateTimePlugin::class,
            TimestampPlugin::class,
            IteratorPlugin::class,
            ToStringPlugin::class,
            FsPathPlugin::class,
            ColorPlugin::class,
            JsonPlugin::class,
            MicrotimePlugin::class,
            SerializePlugin::class,
            ArrayLimitPlugin::class,
            ClosurePlugin::class
        ];
        
        // If we detect either a client that does not accept html, or the request
        // is executed using an "AJAX" request, we will use the text-renderer instead of the rich-renderer
        if (
            isset($_SERVER) &&
            (stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'text/html') !== 0
                || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
                || strtolower($_SERVER['X-Requested-With'] ?? '') === 'xmlhttprequest')) {
            Kint::$mode_default = Kint::MODE_TEXT;
            static::$isAjax = true;
        } elseif (PHP_SAPI === 'cli' || defined('STDIN')) {
            static::$isCli = true;
        }
        
        (new ConfigLoader())->load();
        
        static::$hooks->trigger(HookType::AFTER_INIT, static::$config);
    }
    
    /**
     * Used to configure the debugging context.
     * @return Config
     */
    public static function config(): Config
    {
        static::init();
        return static::$config;
    }
    
    /**
     * Used to register custom hooks and listeners
     * @return Hooks
     */
    public static function hooks(): Hooks
    {
        static::init();
        return static::$hooks;
    }
    
    /**
     * Returns true if the debugging capabilities are enabled, false if not
     *
     * @return bool
     */
    public static function isEnabled(): bool
    {
        $config = static::config();
        return $config->isEnabled() && static::$envDetection->isEnabled();
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
    
    /**
     * Returns true if the current request is executed in a CLI context, false if not
     *
     * @return bool
     */
    public static function isCli(): bool
    {
        static::init();
        return static::$isCli;
    }
    
    /**
     * Returns true if the current request is executed as an AJAX request, false if not
     *
     * @return bool
     */
    public static function isAjax(): bool
    {
        static::init();
        return static::$isAjax;
    }
}
