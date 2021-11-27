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
 * Last modified: 2021.11.27 at 16:03
 */

declare(strict_types=1);


namespace Neunerlei\Dbg\Module;


use Kint\Kint;
use Neunerlei\Dbg\Dbg;

class Dumper
{
    public static function dump(string $functionName, array $args, bool $exit): void
    {
        if (! Dbg::isEnabled()) {
            return;
        }
        
        Dbg::runHooks(Dbg::HOOK_TYPE_PRE, $functionName, $args);
        Kint::dump(...$args);
        Dbg::runHooks(Dbg::HOOK_TYPE_POST, $functionName, $args);
        
        if ($exit) {
            exit();
        }
    }
}