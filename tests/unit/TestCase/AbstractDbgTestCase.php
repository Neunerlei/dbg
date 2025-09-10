<?php
declare(strict_types=1);

namespace {
    if (!function_exists('__dbg_test_function__')) {
        function __dbg_test_function__()
        {
            return ($GLOBALS['__dbg_test_function__'])(...func_get_args());
        }
    }
}

namespace Neunerlei\Dbg\Tests\Unit\TestCase {
    
    use Kint\Kint;
    use Neunerlei\Dbg\Dbg;
    use PHPUnit\Framework\TestCase;
    
    abstract class AbstractDbgTestCase extends TestCase
    {
        protected const TEST_FUNCTION_NAME = '__dbg_test_function__';
        
        private array $configBackup;
        private string $lastOutput;
        
        protected function setUp(): void
        {
            $this->configBackup = Dbg::config()->jsonSerialize();
            Kint::$aliases['DBG_TEST_FUNCTION'] = '__dbg_test_function__';
        }
        
        protected function tearDown(): void
        {
            $resetter = new class extends Dbg {
                public function reset(): void
                {
                    Dbg::$isAjax = false;
                    Dbg::$isCli = false;
                    Dbg::$requestId = null;
                    Dbg::$config = null;
                    Dbg::$hooks = null;
                    Dbg::$envDetection = null;
                }
            };
            
            $resetter->reset();
        }
        
        protected function runHandler(callable $handler, ...$args)
        {
            ob_start();
            $GLOBALS['__dbg_test_function__'] = $handler;
            $res = __dbg_test_function__(...$args);
            $this->lastOutput = ob_get_clean();
            
            $this->lastOutput = preg_replace('#\\x1b[[][^A-Za-z]*[A-Za-z]#', '', $this->lastOutput);
            $this->lastOutput = preg_replace('~:\d+ \[~', ':normalized_line [', $this->lastOutput);
            $this->lastOutput = preg_replace('~<ROOT>.*?' . preg_quote(basename(__FILE__), '~') . '~', '<ROOT>normalized_file', $this->lastOutput);
            $this->lastOutput = trim($this->lastOutput);
            return $res;
        }
        
        protected function getLastOutput(): string
        {
            return $this->lastOutput ?? '';
        }
    }
}
