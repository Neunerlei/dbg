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
 * Last modified: 2021.11.27 at 16:09
 */

declare(strict_types=1);


namespace Neunerlei\Dbg\Module;


use Kint\Kint;
use Kint\Parser\TracePlugin;
use Kint\Utils;
use Neunerlei\Dbg\Dbg;

class Tracer
{
    public static function trace(string $functionName, array $args, bool $exit): void
    {
        if (! Dbg::isEnabled()) {
            return;
        }
        
        Dbg::runHooks(Dbg::HOOK_TYPE_PRE, $functionName, $args);
        
        $options = static::prepareOptions($args);
        
        $trace = array_slice(
            debug_backtrace($options['backtraceOptions']),
            $options['offset']
        );
        
        $trace = static::processTrace($trace, $options['length']);
        $hasTracePlugin = in_array(TracePlugin::class, Kint::$plugins, true);
        
        if (! $hasTracePlugin) {
            Kint::$plugins[] = TracePlugin::class;
        }
        
        $_expanded = Kint::$expanded;
        Kint::$expanded = true;
        $_calledFrom = Kint::$display_called_from;
        Kint::$display_called_from = false;
        
        Kint::dump($trace);
        
        Kint::$expanded = $_expanded;
        Kint::$display_called_from = $_calledFrom;
        
        if (! $hasTracePlugin) {
            array_pop(Kint::$plugins);
        }
        
        Dbg::runHooks(Dbg::HOOK_TYPE_POST, $functionName, $args);
        
        if ($exit) {
            exit();
        }
    }
    
    /**
     * Extracts the options from the given arguments and returns them as array
     *
     * @param   array  $args
     *
     * @return array
     */
    protected static function prepareOptions(array $args): array
    {
        $options = is_array($args[0] ?? null) ? $args[0] : [];
        
        $withArguments = is_bool($options['withArguments'] ?? null)
            ? $options['withArguments']
            : ! is_null($options['withArguments'] ?? null) || php_sapi_name() !== 'cli';
        
        return [
            'backtraceOptions' => $withArguments ? DEBUG_BACKTRACE_PROVIDE_OBJECT : DEBUG_BACKTRACE_IGNORE_ARGS,
            // + 1 to remove the Tracer::trace from the stack
            'offset' => ((int)($options['offset'] ?? 0)) + 1,
            'length' => (int)($options['length'] ?? 0),
        ];
    }
    
    /**
     * Process the trace by resolving the trace frames and by applying the configured max length
     *
     * @param   array  $trace
     * @param   int    $length
     *
     * @return array
     */
    protected static function processTrace(array $trace, int $length): array
    {
        $_trace = [];
        $stepsToShow = empty($length) ? count($trace) : $length;
        foreach ($trace as $frame) {
            if (Utils::traceFrameIsListed($frame, Kint::$aliases)) {
                $_trace = [];
            }
            
            if (count($_trace) >= $stepsToShow) {
                $_trace[] = [
                    'file' => 'There are ' . (abs($stepsToShow - count($trace))) . ' additional steps hidden by the "length" setting.',
                    'line' => 1,
                    'function' => '',
                    'args' => [],
                ];
                break;
            }
            
            $_trace[] = $frame;
        }
        
        return $_trace;
    }
}