<?php
/**
 * Copyright 2019 LABOR.digital
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
 * Last modified: 2019.07.07 at 23:03
 */

namespace Labor\Dbg\Plugins;


use Kint\Kint;
use Kint\Object\BasicObject;
use Kint\Object\InstanceObject;
use Kint\Parser\Parser;
use Kint\Parser\Plugin;

class DedupePlugin extends Plugin {
	/**
	 * @var Parser
	 */
	protected $parser;
	
	protected $knownObjects = [];
	
	public function getTypes() {
		return ["object"];
	}
	
	public function getTriggers() {
		return Parser::TRIGGER_BEGIN;
	}
	
	public function parse(&$variable, BasicObject &$o, $trigger) {
		// Ignore if this is a class without namespace -> probably built in
		if (stripos(get_class($variable), "\\") === FALSE) return;
		
		// Get the object id
		$id = spl_object_hash($variable);
		if (isset($this->knownObjects[$id])) {
			$object = new InstanceObject();
			$object->transplant($o);
			$object->depth = max($o->depth, Kint::$max_depth - 1);
			$object->classname = \get_class($variable);
			$object->hash = $id;
			$o = $object;
		}
		
		// Mark as known
		$this->knownObjects[$id] = TRUE;
	}
}