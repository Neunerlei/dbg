<?php

declare(strict_types=1);


namespace Neunerlei\Dbg\Renderer;


use Kint\Renderer\CliRenderer;

class ExtendedCliRenderer extends CliRenderer
{
    
    public function boxText(string $text, int $width): string
    {
        if (empty($text)) {
            return '';
        }
        
        return parent::boxText($text, $width);
    }
}
