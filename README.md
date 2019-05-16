# LABOR Debug Helpers
This library is basically just a wrapper around [Kint](https://github.com/kint-php/kint) and [PHP-Console](https://github.com/barbushin/php-console) combining them both into a powerful debugging tool.

The library contains the following functions:

## Installation
Install this package using our [satis repository](https://satis.labor.tools/?#labor/dbg)

In your cli `composer require labor\dbg`

## Functions 
###Labor\Dbg\dbg()
Takes any number of arguments and prints them to the screen, either formatted for html or for cli, depending on the current context.

```php
use function Labor\Dbg\dbg;
dbg("foo");
```

Will render something like this in a browser:

![Preview](ReadmeImages/dbg.png)

and something like this in a CLI app:

![Preview](ReadmeImages/dbg-cli.png)


###Labor\Dbg\dbge()
Works exactly the same way as dbg() but kills the script exit(0) after dumping the arguments to the screen.


###Labor\Dbg\trace()
Prints the current debug backtrace to the screen, either formatted for html or for cli, depending on the current context.

```php
use function Labor\Dbg\trace;
trace();
```

Will render something like this in a browser:

![Preview](ReadmeImages/trace.png)


###Labor\Dbg\tracee()
Again, works exactly the same as trace, but kills the script after printing it.


###Labor\Dbg\logConsole()
This function is mend specifically for in-browser development only. It relies on [PHP-Console](https://github.com/barbushin/php-console) and the [chrome php console extension](https://chrome.google.com/webstore/detail/php-console/nfhmhhlpfleoednkpnnnkolmclajemef) to render the given values to the javascript console without using html script tags or similar.
It works also when performing redirects or throwing exceptions, as the data will be transferred over a http header.
```php
use function Labor\Dbg\logConsole;
logConsole("foo");
```

This will output something like:

![Preview](ReadmeImages/php-console.png)


###Labor\Dbg\logFile()
Receives any number of arguments and will dump them into a plain log file. 
The logfile will be located (in order of priority):

- LABOR_DBG_LOG_DIR/labor_debug_logfile.log if this constant contains a writable directory path
- /var/www/logs/labor_debug_logfile.log if the logs directory is writable
- /$SYS_TEMP_DIR/labor_debug_logfile.log

```php
use function Labor\Dbg\logFile;
logFile("foo");
```