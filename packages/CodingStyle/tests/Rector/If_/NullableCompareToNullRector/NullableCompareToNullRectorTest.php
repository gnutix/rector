<?php declare(strict_types=1);

namespace Rector\CodingStyle\Tests\Rector\If_\NullableCompareToNullRector;

use Iterator;
use Rector\CodingStyle\Rector\If_\NullableCompareToNullRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class NullableCompareToNullRectorTest extends AbstractRectorTestCase
{
    /**
     * @dataProvider provideDataForTest()
     */
    public function test(string $file): void
    {
        $this->doTestFile($file);
    }

    public function provideDataForTest(): Iterator
    {
        yield [__DIR__ . '/Fixture/fixture.php.inc'];
        yield [__DIR__ . '/Fixture/fixture2.php.inc'];
        yield [__DIR__ . '/Fixture/fixture3.php.inc'];
        yield [__DIR__ . '/Fixture/nullable_scalars.php.inc'];
    }

    protected function getRectorClass(): string
    {
        return NullableCompareToNullRector::class;
    }
}
