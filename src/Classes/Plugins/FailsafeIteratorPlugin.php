<?php
declare(strict_types=1);


namespace Neunerlei\Dbg\Plugins;


use Kint\Object\BasicObject;
use Kint\Parser\IteratorPlugin;
use Kint\Zval\Value;

class FailsafeIteratorPlugin extends IteratorPlugin
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