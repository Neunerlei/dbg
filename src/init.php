<?php
declare(strict_types=1);

use Neunerlei\Dbg\Dbg;

if (!defined('KINT_SKIP_HELPERS')) {
    define('KINT_SKIP_HELPERS', true);
}
if (!defined('KINT_SKIP_FACADE')) {
    define('KINT_SKIP_FACADE', true);
}

Dbg::init();
include __DIR__ . '/functions.php';
