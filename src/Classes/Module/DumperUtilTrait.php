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
 * Last modified: 2022.04.19 at 11:25
 */

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
