<?php
declare(strict_types=1);


namespace Neunerlei\Dbg\Util;


class ConfigLoader
{
    public function load(): void
    {
        $directories = [];

        // Load well known keys in the $_SERVER super-globals array as potential directory
        foreach (['DOCUMENT_ROOT', 'DDEV_COMPOSER_ROOT', 'PWD', 'DBG_CONFIG_DIR'] as $wellKnownServerKey) {
            if (isset($_SERVER[$wellKnownServerKey])) {
                $directories[] = $_SERVER[$wellKnownServerKey];
            }
        }

        foreach ($directories as $dir) {
            $candidates = [
                $this->makeFilename($dir),
                $this->makeFilename($this->joinNames($dir, '.dbg')),
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

    protected function makeFilename(string $dir): string
    {
        return $this->joinNames($dir, 'dbg.config.php');
    }

    protected function loadFile(string $filename): void
    {
        $that = new \stdClass();
        $c = function (string $filename) {
            require_once $filename;
        };
        $c->call($that, $filename);
    }
}
