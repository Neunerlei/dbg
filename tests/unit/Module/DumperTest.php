<?php
declare(strict_types=1);


namespace Neunerlei\Dbg\Tests\Unit\Module;


use Neunerlei\Dbg\Dbg;
use Neunerlei\Dbg\Module\Dumper;
use Neunerlei\Dbg\Tests\Unit\TestCase\AbstractDbgTestCase;

class DumperTest extends AbstractDbgTestCase
{
    public function testItDoesNothingIfDbgIsNotEnabled(): void
    {
        Dbg::config('enabled', false);
        $this->expectOutputString('');
        Dumper::dump('foo', [], false);
    }

    public function testItExecutesTheRegisteredHooksWhenDumping(): void
    {
        $args = ['true', 'bar'];
        $c = 0;

        Dbg::config('enabled', true);
        Dbg::config(Dbg::HOOK_TYPE_PRE, [
            function ($type, $functionName, $_args) use (&$c, $args) {
                $this->assertEquals(Dbg::HOOK_TYPE_PRE, $type);
                $this->assertEquals(self::TEST_FUNCTION_NAME, $functionName);
                $this->assertEquals($args, $_args);
                $c++;
            }
        ]);
        Dbg::config(Dbg::HOOK_TYPE_POST, [
            function ($type, $functionName, $_args) use (&$c, $args) {
                $this->assertEquals(Dbg::HOOK_TYPE_POST, $type);
                $this->assertEquals(self::TEST_FUNCTION_NAME, $functionName);
                $this->assertEquals($args, $_args);
                $c++;
            }
        ]);

        $this->runHandler(function (...$args) {
            Dumper::dump(self::TEST_FUNCTION_NAME, $args, false);
        }, ...$args);

        $this->assertSame(<<<EXPECTED
┌──────────────────────────────────────────────────────────────────────────────┐
│ array_values(\$args)[0]                                                       │
└──────────────────────────────────────────────────────────────────────────────┘
string (4) "true"
┌──────────────────────────────────────────────────────────────────────────────┐
│ array_values(\$args)[1]                                                       │
└──────────────────────────────────────────────────────────────────────────────┘
string (3) "bar"
════════════════════════════════════════════════════════════════════════════════
Called from <ROOT>normalized_file:normalized_line [__dbg_test_function__()]
EXPECTED,
            $this->getLastOutput()
        );

        $this->assertEquals(2, $c);
    }
}
