<?php declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\helpers;

use hipanel\modules\finance\models\HasDecorator;
use hipanel\modules\finance\models\Resource;
use hiqdev\billing\registry\ResourceDecorator\DecoratedInterface;

class MockResource extends Resource implements DecoratedInterface
{
    use HasDecorator;
}
