<?php

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
