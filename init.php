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
 * Last modified: 2019.05.16 at 12:45
 */

// Make sure kint does not register it's helpers
use Kint\Kint;
use Kint\Renderer\RichRenderer;
use Labor\Dbg\ExtendedCliRenderer;
use Labor\Dbg\ExtendedTextRenderer;

if (!defined("KINT_SKIP_HELPERS")) define("KINT_SKIP_HELPERS", TRUE);
if (!defined("KINT_SKIP_FACADE")) define("KINT_SKIP_FACADE", TRUE);

// Check the environment
if (!defined("LABOR_DBG_ENABLED"))
	define("LABOR_DBG_ENABLED",
		getenv("PROJECT_ENV") === "dev" || php_sapi_name() === "cli" ||
		isset($_SERVER["HTTP_REFERER"]) && $_SERVER["HTTP_REFERER"] === "LABOR_DEBUG_REFERER_XA2134asfadDf");

// Configure kint
RichRenderer::$folder = FALSE;
Kint::$renderers[Kint::MODE_TEXT] = ExtendedTextRenderer::class;
Kint::$renderers[Kint::MODE_CLI] = ExtendedCliRenderer::class;
Kint::$max_depth = 10;
Kint::$aliases[] = "Labor\\Dbg\\dbg";
Kint::$aliases[] = "Labor\\Dbg\\dbge";
Kint::$aliases[] = "Labor\\Dbg\\consoleLog";
Kint::$aliases[] = "Labor\\Dbg\\fileLog";
Kint::$aliases[] = "Labor\\Dbg\\trace";
Kint::$aliases[] = "Labor\\Dbg\\tracee";

// Switch to text renderer if we are inside an ajax, or other non-html requests
if (isset($_SERVER)) {
	$useTextRenderer = isset($_SERVER["HTTP_ACCEPT"]) && stripos($_SERVER["HTTP_ACCEPT"], "text/html") !== 0;
	$useTextRenderer = !$useTextRenderer && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == "xmlhttprequest";
	if ($useTextRenderer) Kint::$mode_default = Kint::MODE_TEXT;
}

// Disable kint when debug is not enabled
if (!LABOR_DBG_ENABLED) Kint::$enabled_mode = FALSE;

// Load bugifxes
include __DIR__ . "/Bugfixes/KintUtils.php";
include __DIR__ . "/Bugfixes/boxRendererFixes.php";

// Load our own functions
include __DIR__ . "/functions.php";
