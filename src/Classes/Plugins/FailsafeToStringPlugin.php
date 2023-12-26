<?php
declare(strict_types=1);


namespace Neunerlei\Dbg\Plugins;


use Kint\Parser\ToStringPlugin;
use Kint\Zval\Value;

class FailsafeToStringPlugin extends ToStringPlugin
{
    public function parse(&$var, Value &$o, int $trigger): void
    {
        try {
            parent::parse($var, $o, $trigger);
        } catch (\Throwable $e){
            return;
        }
    }
    
}