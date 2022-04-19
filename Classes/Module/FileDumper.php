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
 * Last modified: 2021.11.27 at 19:18
 */

declare(strict_types=1);


namespace Neunerlei\Dbg\Module;


use Kint\Kint;
use Neunerlei\Dbg\Dbg;

class FileDumper
{
    use DumperUtilTrait;
    
    public static function dump(string $functionName, array $args): bool
    {
        if (! Dbg::isEnabled()) {
            return true;
        }
        
        Dbg::runHooks(Dbg::HOOK_TYPE_PRE, $functionName, $args);
        
        $logFileName = static::resolveLogFileName();
        if ($logFileName === null) {
            return false;
        }
        
        $_return = Kint::$return;
        Kint::$return = true;
        $_modeDefault = Kint::$mode_default;
        Kint::$mode_default = Kint::MODE_TEXT;
        
        $content = Kint::dump(...$args);
        
        Kint::$return = $_return;
        Kint::$mode_default = $_modeDefault;
        
        $content .= static::getTimestamp() . ' ' . static::getRequestSource() . PHP_EOL;
        
        if (is_file($logFileName) && filesize($logFileName) > 0) {
            $content = PHP_EOL . PHP_EOL . PHP_EOL . $content;
        }
        
        file_put_contents($logFileName, $content, FILE_APPEND);
        
        Dbg::runHooks(Dbg::HOOK_TYPE_POST, $functionName, $args);
        
        return true;
    }
    
    /**
     * Tries to resolve a name for a writable log file location
     *
     * @return string|null
     */
    protected static function resolveLogFileName(): ?string
    {
        $logDir = getenv('_DBG_LOG_DIR');
        if (empty($logDir)) {
            $logDir = Dbg::config('logDir');
        }
        
        $ds = DIRECTORY_SEPARATOR;
        $baseName = 'dbg_debug_logfile.log';
        
        $variants = [
            '/var/www/logs/' . $baseName,
            sys_get_temp_dir() . $ds . $baseName,
        ];
        
        if (is_string($logDir)) {
            array_unshift($variants, rtrim($logDir, "/\\") . $ds . $baseName);
        }
        
        foreach ($variants as $variant) {
            if (is_writable($variant) || (! is_file($variant) && is_writable(dirname($variant)))) {
                return $variant;
            }
        }
        
        return null;
    }
}