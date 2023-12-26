<?php
declare(strict_types=1);


namespace Neunerlei\Dbg\Util;


use Kint\Kint;

class Callee
{
    public function __toString(): string
    {
        $callInfo = Kint::getCallInfo(Kint::$aliases, debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), []);
        $output = '';
        
        if (isset($callInfo['callee']['file'])) {
            $output .= 'Called from ' . $callInfo['callee']['file'] . ':' . $callInfo['callee']['line'];
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