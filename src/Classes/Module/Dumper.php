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
use Kint\Renderer\RichRenderer;
use Neunerlei\Dbg\Dbg;
use Neunerlei\Dbg\HookType;
use Neunerlei\Dbg\Util\Headers;

class Dumper
{
    protected static bool $hasDumpedOnce = false;
    protected static bool $isDumping = false;
    
    public static function dump(array $args, bool $exit): void
    {
        if (!Dbg::isEnabled()) {
            return;
        }
        
        if ($exit) {
            Headers::exitHeaders();
        }
        
        if (static::$isDumping && !static::$hasDumpedOnce) {
            $oldPreRenderState = RichRenderer::$always_pre_render;
            RichRenderer::$always_pre_render = true;
        }
        
        static::$isDumping = true;
        
        try {
            Dbg::hooks()->trigger(HookType::BEFORE_DUMP, ...$args);
            Kint::dump(...$args);
            Dbg::hooks()->trigger(HookType::AFTER_DUMP, ...$args);
        } finally {
            static::$isDumping = false;
            static::$hasDumpedOnce = true;
            
            if (isset($oldPreRenderState)) {
                RichRenderer::$always_pre_render = $oldPreRenderState;
            }
        }
        
        if ($exit) {
            exit();
        }
    }
}
