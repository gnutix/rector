<?php

namespace Rector\DeadCode\Tests\Rector\Plus\RemoveDeadZeroAndOneOperationRector\Fixture;

class SomeClass
{
    public function run()
    {
        $value = 5 * 1;
        $value = 1 * 5;

        $value = 5 + 0;
    }
}

?>
-----
<?php

namespace Rector\DeadCode\Tests\Rector\Plus\RemoveDeadZeroAndOneOperationRector\Fixture;

class SomeClass
{
    public function run()
    {
        $value = 5;
        $value = 5;

        $value = 5;
    }
}

?>
