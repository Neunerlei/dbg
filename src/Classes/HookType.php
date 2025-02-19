<?php
declare(strict_types=1);


namespace Neunerlei\Dbg;


enum HookType
{
    case BEFORE_INIT;
    case AFTER_INIT;
    case BEFORE_TRACE;
    case AFTER_TRACE;
    case BEFORE_DUMP;
    case AFTER_DUMP;
    case BEFORE_LOG_CONSOLE;
    case AFTER_LOG_CONSOLE;
    case BEFORE_LOG_FILE;
    case AFTER_LOG_FILE;
    case BEFORE_LOG_STREAM;
    case AFTER_LOG_STREAM;
}
