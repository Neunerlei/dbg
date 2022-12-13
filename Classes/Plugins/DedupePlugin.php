<?php
declare(strict_types=1);

/**
 * Copyright 2020 Martin Neundorfer (Neunerlei)
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
 * Last modified: 2020.02.27 at 11:49
 */

namespace Neunerlei\Dbg\Plugins;


use Kint\Kint;
use Kint\Object\BasicObject;
use Kint\Object\InstanceObject;
use Kint\Parser\AbstractPlugin;
use Kint\Parser\Parser;
use Kint\Zval\Value;
use function get_class;

class DedupePlugin extends AbstractPlugin
{
    /**
     * @var Parser
     */
    protected $parser;

    protected $knownObjects = [];

    public function getTypes(): array
    {
        $this->knownObjects = [];

        return ['object'];
    }

    public function getTriggers(): int
    {
        return Parser::TRIGGER_BEGIN;
    }

    public function parse(&$var, Value &$o, int $trigger): void
    {
        // Ignore if this is a class without namespace -> probably built in
        if (strpos(get_class($var), "\\") === false) {
            return;
        }

        // Get the object id
        $id = spl_object_hash($var);
        if (isset($this->knownObjects[$id])) {
            $object = new Value();
            $object->transplant($o);
            $object->depth = max($o->depth, Kint::$depth_limit - 1);
            $object->classname = get_class($var);
            $object->hash = $id;
            $o = $object;
        }
        
        // Mark as known
        $this->knownObjects[$id] = true;
    }
}