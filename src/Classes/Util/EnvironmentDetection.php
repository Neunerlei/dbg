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
        
        $envPairs = [];
        
        // Default environment variable keys and values to check for, if the built-in detection is not disabled
        if (!$this->config->isBuiltInEnvVarDetectionDisabled()) {
            // APP_ENV=dev is the most common convention, so we check for that by default
            $envPairs['APP_ENV'] = ['development', 'dev', 'local'];
            
            // Legacy support for older applications
            $envPairs['PROJECT_ENV'] = ['development', 'dev'];
            
            // Support for my own docker images (https://github.com/Neunerlei/docker-images/blob/main/docs/php-nginx.md)
            $envPairs['ENVIRONMENT'] = ['development', 'dev'];
            $envPairs['PHP_PROD_DEBUG'] = ['true', '1', 'yes'];
        }
        
        // Explicitly configured environment variable key and value
        // Doing this after the defaults, allows the user to override the expected default values, too.
        $configuredEnvKey = $this->config->getEnvVarKey();
        $configuredEnvValue = $this->config->getEnvVarValue();
        if ($configuredEnvKey !== null && !empty($configuredEnvValue)) {
            $envPairs[$configuredEnvKey] = array_map('strtolower', array_map('trim', explode(',', $configuredEnvValue)));
        }
        
        foreach ($envPairs as $envKey => $values) {
            $value = getenv($envKey);
            if ($value === false) {
                $value = $_ENV[$envKey] ?? null;
            }
            if (is_string($value)) {
                $value = strtolower($value);
            }
            if (in_array($value, $values, true)) {
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
