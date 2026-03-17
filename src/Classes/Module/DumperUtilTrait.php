<?php

declare(strict_types=1);


namespace Neunerlei\Dbg\Module;


use Neunerlei\Dbg\Util\Callee;
use Neunerlei\Dbg\Util\RequestSource;
use Neunerlei\Dbg\Util\Timestamp;

trait DumperUtilTrait
{
    protected static RequestSource $requestSource;
    protected static Callee $callee;
    protected static Timestamp $timestamp;
    
    public static function setTimestamp(Timestamp $timestamp): void
    {
        static::$timestamp = $timestamp;
    }
    
    protected static function getTimestamp(): Timestamp
    {
        return static::$timestamp ?? new Timestamp(new \DateTime());
    }
    
    public static function setRequestSource(RequestSource $requestSource): void
    {
        static::$requestSource = $requestSource;
    }
    
    protected static function getRequestSource(): RequestSource
    {
        return static::$requestSource ?? new RequestSource();
    }
    
    public static function setCallee(Callee $callee): void
    {
        static::$callee = $callee;
    }
    
    protected static function getCallee(): Callee
    {
        return static::$callee ?? new Callee();
    }
}
