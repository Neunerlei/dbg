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
 * Last modified: 2021.11.27 at 19:43
 */

declare(strict_types=1);


namespace Neunerlei\Dbg\Renderer;


use Kint\Renderer\TextRenderer;

class ExtendedTextRenderer extends TextRenderer
{
    public function boxText(string $text, int $width): string
    {
        if (empty($text)) {
            return '';
        }

        return parent::boxText($text, $width);
    }
}