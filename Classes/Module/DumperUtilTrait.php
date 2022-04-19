<?php
/*
 * Copyright 2022 LABOR.digital
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
 * Last modified: 2022.04.19 at 11:25
 */

declare(strict_types=1);


namespace Neunerlei\Dbg\Module;


use Kint\Kint;

trait DumperUtilTrait
{
    protected static function getTimestamp(): string
    {
        return '[' . (new \DateTime())->format('Y-m-d H:i:s e') . ']';
    }
    
    protected static function getRequestSource(): string
    {
        if (isset($_SERVER['SERVER_NAME'], $_SERVER['REQUEST_URI'])) {
            return 'URL: ' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        }
        
        if (PHP_SAPI === 'cli') {
            return 'Called from CLI';
        }
        
        return 'Unknown source';
    }
    
    protected static function getCallee(array $args): string
    {
        $callInfo = Kint::getCallInfo(Kint::$aliases, debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), count($args));
        $output = '';
        
        if (isset($callInfo['callee']['file'])) {
            $output .= 'Called from ' . Kint::shortenPath($callInfo['callee']['file']) . ':' . $callInfo['callee']['line'];
        }
        
        if (isset($callInfo['callee']['function']) && (
                ! empty($callInfo['callee']['class']) ||
                ! in_array(
                    $callInfo['callee']['function'],
                    ['include', 'include_once', 'require', 'require_once'],
                    true
                )
            )
        ) {
            $output .= ' [';
            if (isset($callInfo['callee']['class'])) {
                $output .= $callInfo['callee']['class'];
            }
            if (isset($callInfo['callee']['type'])) {
                $output .= $callInfo['callee']['type'];
            }
            $output .= $callInfo['callee']['function'] . '()]';
        }
        
        return $output;
    }
}