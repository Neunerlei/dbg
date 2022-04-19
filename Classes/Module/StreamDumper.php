<?php
/*
 * Copyright 2022 LABOR.digital
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
 * Last modified: 2022.04.19 at 11:03
 */

declare(strict_types=1);


namespace Neunerlei\Dbg\Module;


use Neunerlei\Dbg\Dbg;
use UnexpectedValueException;

class StreamDumper
{
    use DumperUtilTrait;
    
    /**
     * Public "API" to define the location of the stream to dump to
     *
     * @var string
     */
    public static $streamPath = 'php://stdout';
    
    /**
     * Contains the stream to dump to
     *
     * @var resource|null
     */
    protected static $stream;
    
    /**
     * The path of the stream that is currently open,
     * used to detect if the streamPath was changed while the stream was already open
     *
     * @var string|null
     */
    protected static $openStreamPath;
    
    /**
     * An error message to catch stream errors
     *
     * @var string|null
     */
    protected static $errorMessage;
    
    public static function dump(string $functionName, array $args): bool
    {
        if (! Dbg::isEnabled()) {
            return true;
        }
        
        Dbg::runHooks(Dbg::HOOK_TYPE_PRE, $functionName, $args);
        
        static::openStream();
        
        if (fwrite(static::$stream, static::buildLogLine($args)) === false) {
            return false;
        }
        
        Dbg::runHooks(Dbg::HOOK_TYPE_POST, $functionName, $args);
        
        return true;
    }
    
    protected static function buildLogLine(array $args): string
    {
        return static::getTimestamp() .
               ' ' . static::stringifyArgs($args) .
               ' | ' . static::getCallee($args) .
               ' | ' . static::getRequestSource();
    }
    
    /**
     * Super simple stringifier that converts the list of arguments into a continuous string to dump to the stream
     *
     * @param   array  $args
     *
     * @return string
     */
    protected static function stringifyArgs(array $args): string
    {
        $out = [];
        
        foreach ($args as $arg) {
            if (is_string($arg) || is_numeric($arg)) {
                $out[] = $arg;
                continue;
            }
            
            $argString = @json_encode($arg);
            $type = gettype($arg);
            if (is_object($arg)) {
                $out[] = '(' . $type . ': ' . get_class($arg) . ') ' . $argString;
                continue;
            }
            
            $out[] = '(' . $type . ') ' . $argString;
        }
        
        return implode(' || ', $out);
    }
    
    /**
     * Opens the stream if not already opened
     * Automatically detects if the streamPath has been changed and reopens the new stream.
     *
     * @return void
     */
    protected static function openStream(): void
    {
        if (isset(static::$stream)) {
            if (static::$streamPath === static::$openStreamPath) {
                return;
            }
            
            static::closeStream();
        }
        
        static::createDir();
        static::$errorMessage = null;
        set_error_handler([static::class, 'customErrorHandler']);
        static::$stream = fopen(static::$streamPath, 'ab');
        restore_error_handler();
        
        if (! is_resource(static::$stream)) {
            static::closeStream();
            
            throw new UnexpectedValueException(
                sprintf(
                    'The stream or file "%s" could not be opened in append mode: ' .
                    static::$errorMessage, static::$streamPath));
        }
    }
    
    /**
     * Closes the currently open stream
     *
     * @return void
     */
    protected static function closeStream(): void
    {
        if (is_resource(static::$stream ?? null)) {
            fclose(static::$stream);
        }
        
        static::$openStreamPath = null;
        static::$stream = null;
    }
    
    /**
     * @param   string  $streamPath
     *
     * @return null|string
     */
    protected static function getDirFromStream(string $streamPath): ?string
    {
        $pos = strpos($streamPath, '://');
        if ($pos === false) {
            return dirname($streamPath);
        }
        
        if (strpos($streamPath, 'file://') === 0) {
            return dirname(substr($streamPath, 7));
        }
        
        return null;
    }
    
    /**
     * Makes sure that the stream directory exists and is writable
     */
    protected static function createDir(): void
    {
        $dir = static::getDirFromStream(static::$streamPath);
        if (null !== $dir && ! is_dir($dir)) {
            static::$errorMessage = null;
            set_error_handler([static::class, 'customErrorHandler']);
            $status = mkdir($dir, 0777, true);
            restore_error_handler();
            if (false === $status && ! is_dir($dir)) {
                throw new UnexpectedValueException(
                    sprintf(
                        'There is no existing directory at "%s" and its not buildable: ' . static::$errorMessage, $dir));
            }
        }
    }
    
    public static function customErrorHandler($code, $msg): void
    {
        static::$errorMessage = preg_replace('{^(fopen|mkdir)\(.*?\): }', '', $msg);
    }
}