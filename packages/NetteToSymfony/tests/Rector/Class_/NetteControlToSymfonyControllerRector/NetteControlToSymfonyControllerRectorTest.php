<?php declare(strict_types=1);

namespace Rector\NetteToSymfony\Tests\Rector\Class_\NetteControlToSymfonyControllerRector;

use Iterator;
use Rector\NetteToSymfony\Rector\Class_\NetteControlToSymfonyControllerRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class NetteControlToSymfonyControllerRectorTest extends AbstractRectorTestCase
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
    }

    protected function getRectorClass(): string
    {
        return NetteControlToSymfonyControllerRector::class;
    }
}
