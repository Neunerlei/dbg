<?php
declare(strict_types=1);


namespace Neunerlei\Dbg\Util;


class EnvironmentDetection
{
    protected Config $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function isEnabled(): bool
    {
        if (!$this->config->useEnvironmentDetection()) {
            return true;
        }

        // Env variable matches expected value? -> Yes
        $possibleEnvKeys = [$this->config->getEnvVarKey(), 'PROJECT_ENV'];
        $expectedEnvValue = $this->config->getEnvVarValue();

        foreach ($possibleEnvKeys as $envKey) {
            $env = getenv($envKey);
            if ($env === $expectedEnvValue
                || ($env === false && ($_ENV[$envKey] ?? null) === $expectedEnvValue)) {
                return true;
            }
        }

        // CLI is treated as dev? -> Yes
        if (PHP_SAPI === 'cli' && $this->config->isCliIsDev()) {
            return true;
        }

        // Debug referrer is set and matches? -> Yes
        if (is_string($this->config->getDebugReferrer())) {
            return ($_SERVER['HTTP_REFERER'] ?? null) === $this->config->getDebugReferrer();
        }

        return false;
    }
}
