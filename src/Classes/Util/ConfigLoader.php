<?php
declare(strict_types=1);


namespace Neunerlei\Dbg\Util;


class ConfigLoader
{
    public function load(): void
    {
        $directories = [];

        // Load well known keys in the $_SERVER super-globals array as potential directory
        foreach (['DDEV_COMPOSER_ROOT', 'PWD', 'DBG_CONFIG_DIR'] as $wellKnownServerKey) {
            if (isset($_SERVER[$wellKnownServerKey])) {
                $directories[] = $_SERVER[$wellKnownServerKey];
            }
        }

        // Load all parents of the document root
        if (isset($_SERVER['DOCUMENT_ROOT'])) {
            $dir = $_SERVER['DOCUMENT_ROOT'];
            while (is_readable($dir) && $dir !== '/') {
                $directories[] = $dir;
                $dir = dirname($dir);
            }
        }

        foreach ($directories as $dir) {
            $candidates = [
                $this->joinNames($dir, 'dbg.config.php'),
                $this->joinNames($this->joinNames($dir, '.dbg'), 'dbg.config.php'),
                $this->joinNames($this->joinNames($dir, '.dbg'), 'config.php'),
            ];

            foreach ($candidates as $configFile) {
                if (is_readable($configFile)) {
                    $this->loadFile($configFile);
                }
            }
        }
    }

    protected function joinNames(string $dir, string $name): string
    {
        return rtrim($dir, '\\/') . '/' . $name;
    }

    protected function loadFile(string $filename): void
    {
        $that = new class {
        };
        $c = function (string $filename) {
            require_once $filename;
        };
        $c->call($that, $filename);
    }
}
