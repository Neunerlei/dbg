<?php
declare(strict_types=1);


namespace Neunerlei\Dbg\Util;


use Neunerlei\Dbg\Dbg;

class Headers
{
    public static function exitHeaders(): void
    {
        if (headers_sent()) {
            return;
        }
        
        self::corsHeaders();
    }
    
    private static function corsHeaders(): void
    {
        if (!Dbg::config()->doesDumpCorsHeadersOnExit()) {
            return;
        }
        
        header('Access-Control-Allow-Origin: *');
    }
}
