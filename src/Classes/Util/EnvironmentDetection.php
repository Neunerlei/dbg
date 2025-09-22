<?php
declare(strict_types=1);


namespace Neunerlei\Dbg\Util;


use Neunerlei\Dbg\Dbg;

class EnvironmentDetection
{
    protected Config $config;
    
    public function __construct(Config $config)
    {
        $this->config = $config;
    }
    
    public function isAjax(): bool
    {
        if ($this->isCli()) {
            return false;
        }
        
        return isset($_SERVER) &&
            (
                // If the Accept header is set and does not contain text/html, this is probably an ajax request
                (
                    !empty($_SERVER['HTTP_ACCEPT'])
                    && !str_contains($_SERVER['HTTP_ACCEPT'], 'text/html')
                )
                // If a "Bearer" Authorization header is set, this is probably an api request -> this is similar to ajax
                || (!empty($_SERVER['HTTP_AUTHORIZATION'])
                    && str_starts_with(strtolower($_SERVER['HTTP_AUTHORIZATION']), 'bearer '))
                // If the X-Requested-With header is set to XMLHttpRequest, this is an ajax request
                || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
                // Some webservers might strip await the HTTP_ prefix, so we also check for that
                || strtolower($_SERVER['X-Requested-With'] ?? '') === 'xmlhttprequest');
    }
    
    public function isCli(): bool
    {
        return PHP_SAPI === 'cli' || defined('STDIN');
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
        if (Dbg::isCli() && $this->config->isCliIsDev()) {
            return true;
        }
        
        // Debug referrer is set and matches? -> Yes
        if (is_string($this->config->getDebugReferrer())) {
            return ($_SERVER['HTTP_REFERER'] ?? null) === $this->config->getDebugReferrer();
        }
        
        return false;
    }
}
