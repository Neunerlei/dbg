<?php

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
        if (Dbg::isCli()) {
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
