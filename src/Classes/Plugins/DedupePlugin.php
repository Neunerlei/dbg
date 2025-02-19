<?php
declare(strict_types=1);

namespace Neunerlei\Dbg\Plugins;


use Kint\Parser\AbstractPlugin;
use Kint\Parser\Parser;
use Kint\Parser\PluginCompleteInterface;
use Kint\Value\AbstractValue;

class DedupePlugin extends AbstractPlugin implements PluginCompleteInterface
{
    protected array $knownObjects = [];

    public function getTypes(): array
    {
        $this->knownObjects = [];

        return ['object'];
    }

    public function getTriggers(): int
    {
        return Parser::TRIGGER_BEGIN;
    }

    public function parseComplete(&$var, AbstractValue $v, int $trigger): AbstractValue
    {
        if (!str_contains(get_class($var), "\\")) {
            return $v;
        }

        $id = spl_object_hash($var);
        if (isset($this->knownObjects[$id])) {
            $v->flags |= AbstractValue::FLAG_DEPTH_LIMIT;
            $v->clearRepresentations();
        }

        $this->knownObjects[$id] = true;

        return $v;
    }
}
