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

use Neunerlei\Dbg\Dbg;

if (! defined('KINT_SKIP_HELPERS')) {
    define('KINT_SKIP_HELPERS', true);
}
if (! defined('KINT_SKIP_FACADE')) {
    define('KINT_SKIP_FACADE', true);
}

Dbg::init();
include __DIR__ . '/functions.php';
