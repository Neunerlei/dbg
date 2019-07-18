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
use Kint\Parser\BlacklistPlugin;
use Kint\Parser\ColorPlugin;
use Kint\Parser\DateTimePlugin;
use Kint\Parser\FsPathPlugin;
use Kint\Parser\IteratorPlugin;
use Kint\Parser\JsonPlugin;
use Kint\Parser\MicrotimePlugin;
use Kint\Parser\SerializePlugin;
use Kint\Parser\TimestampPlugin;
use Kint\Parser\ToStringPlugin;
use Kint\Renderer\RichRenderer;
use Labor\Dbg\ExtendedCliRenderer;
use Labor\Dbg\ExtendedTextRenderer;
use Labor\Dbg\Plugins\DedupePlugin;

if (!defined("KINT_SKIP_HELPERS")) define("KINT_SKIP_HELPERS", TRUE);
if (!defined("KINT_SKIP_FACADE")) define("KINT_SKIP_FACADE", TRUE);

// Prepare configuration
$GLOBALS["LABOR_DBG_CONFIG"] = [
	"enabled"              => TRUE,
	"environmentDetection" => FALSE,
	"envVarKey"            => "PROJECT_ENV",
	"envVarValue"          => "dev",
	"cliIsDev"             => TRUE,
	"debugReferrer"        => NULL,
	"preHooks"             => [],
	"postHooks"            => [],
	"consolePassword"      => NULL,
	"logDir"               => NULL,
];

// Configure kint
RichRenderer::$folder = FALSE;
RichRenderer::$access_paths = FALSE;
Kint::$renderers[Kint::MODE_TEXT] = ExtendedTextRenderer::class;
Kint::$renderers[Kint::MODE_CLI] = ExtendedCliRenderer::class;
Kint::$max_depth = 8;
Kint::$aliases[] = "dbg";
Kint::$aliases[] = "dbge";
Kint::$aliases[] = "consoleLog";
Kint::$aliases[] = "fileLog";
Kint::$aliases[] = "trace";
Kint::$aliases[] = "tracee";

// Disable some plugins that really kill performance
$pluginsParsed = [];
Kint::$plugins = [
	BlacklistPlugin::class,
	DedupePlugin::class,
	DateTimePlugin::class,
	TimestampPlugin::class,
	IteratorPlugin::class,
	ToStringPlugin::class,
	FsPathPlugin::class,
	ColorPlugin::class,
	JsonPlugin::class,
	MicrotimePlugin::class,
	SerializePlugin::class,
];

// Switch to text renderer if we are inside an ajax, or other non-html requests
if (isset($_SERVER)) {
	$useTextRenderer = isset($_SERVER["HTTP_ACCEPT"]) && stripos($_SERVER["HTTP_ACCEPT"], "text/html") !== 0;
	$useTextRenderer = $useTextRenderer ||
		(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) && strtolower($_SERVER["HTTP_X_REQUESTED_WITH"]) === "xmlhttprequest" ||
			isset($_SERVER["X-Requested-With"]) && strtolower($_SERVER["X-Requested-With"]) === "xmlhttprequest");
	if ($useTextRenderer) Kint::$mode_default = Kint::MODE_TEXT;
}

// Make sure kint is enabled at all times
Kint::$enabled_mode = TRUE;

// Load bugfixes
include __DIR__ . "/Bugfixes/boxRendererFixes.php";

// Load our own functions
include __DIR__ . "/functions.php";
