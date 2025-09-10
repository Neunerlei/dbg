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
 * Last modified: 2021.11.27 at 19:07
 */

declare(strict_types=1);


namespace Neunerlei\Dbg\Module;


use Neunerlei\Dbg\Dbg;
use Neunerlei\Dbg\HookType;
use PhpConsole\Connector;

class PhpConsole
{
    public static function log(array $args): void
    {
        if (!Dbg::isEnabled()) {
            return;
        }
        
        // If cli -> ignore
        if (PHP_SAPI === 'cli') {
            return;
        }
        
        Dbg::hooks()->trigger(HookType::BEFORE_LOG_CONSOLE, ...$args);
        
        $connector = Connector::getInstance();
        $password = Dbg::config()->getConsolePassword();
        if (!empty($password)) {
            $connector->setPassword($password);
        }
        
        $dispatcher = $connector->getDebugDispatcher();
        if ($dispatcher !== null) {
            $argCount = count($args);
            $appendTraceToLastArg = $argCount > 0 && end($args) === true;
            $stripLastArg = $appendTraceToLastArg && $argCount > 1;
            
            $args = array_values($args);
            if ($stripLastArg) {
                array_pop($args);
                $argCount--;
            }
            
            foreach ($args as $k => $arg) {
                if ($appendTraceToLastArg && $k === $argCount - 1) {
                    $dispatcher->detectTraceAndSource = true;
                }
                
                $dispatcher->dispatchDebug($arg, 'PHP-DEBUG' . Dbg::getRequestId(), 1);
            }
        }
        
        Dbg::hooks()->trigger(HookType::AFTER_LOG_CONSOLE, ...$args);
    }
}
