<?php
declare(strict_types=1);


namespace Neunerlei\Dbg\Util;


use Neunerlei\Dbg\Dbg;

class RequestSource
{
    public function isCli(): bool
    {
        return Dbg::isCli();
    }
    
    public function isWebRequest(): bool
    {
        return !$this->isCli();
    }
    
    public function getProtocol(): ?string
    {
        if ($this->isCli()) {
            return null;
        }
        
        if ((isset($_SERVER['HTTPS']) &&
                ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1)) ||
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
                $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            return 'https://';
        }
        
        return 'http://';
    }
    
    public function getHost(): ?string
    {
        if ($this->isCli()) {
            return null;
        }
        
        foreach (['HTTP_HOST', 'SERVER_NAME'] as $lookupKey) {
            if (isset($_SERVER[$lookupKey])) {
                return $_SERVER[$lookupKey];
            }
        }
        
        return null;
    }
    
    public function getUri(): ?string
    {
        return $_SERVER['REQUEST_URI'] ?? null;
    }
    
    public function __toString(): string
    {
        if ($this->isCli()) {
            return 'Called from CLI';
        }
        
        $host = $this->getHost();
        $uri = $this->getUri();
        
        if ($host || $uri) {
            return 'URL: ' . $host . ($uri ?? '/');
        }
        
        return 'Unknown source';
    }
}
