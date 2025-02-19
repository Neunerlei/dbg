<?php
declare(strict_types=1);


namespace Neunerlei\Dbg\Tests\Unit\Module;


use Neunerlei\Dbg\Dbg;
use Neunerlei\Dbg\HookType;
use Neunerlei\Dbg\Module\Dumper;
use Neunerlei\Dbg\Tests\Unit\TestCase\AbstractDbgTestCase;

class DumperTest extends AbstractDbgTestCase
{
    protected function tearDown(): void
    {
        Dbg::config()->setEnabled(true);
    }

    public function testItDoesNothingIfDbgIsNotEnabled(): void
    {
        Dbg::config()->setEnabled(false);
        $this->expectOutputString('');
        Dumper::dump([], false);
    }

    public function testItExecutesTheRegisteredHooksWhenDumping(): void
    {
        $args = ['true', 'bar'];
        $c = 0;

        Dbg::hooks()
            ->addListener(HookType::BEFORE_DUMP, function (...$_args) use (&$c, $args) {
                $this->assertEquals($args, $_args);
                $c++;
            })->addListener(HookType::AFTER_DUMP, function (...$_args) use (&$c, $args) {
                $this->assertEquals($args, $_args);
                $c++;
            });

        $this->runHandler(function (...$args) {
            Dumper::dump($args, false);
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
