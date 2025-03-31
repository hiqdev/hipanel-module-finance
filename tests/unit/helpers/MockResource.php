<?php declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\helpers;

use hipanel\modules\finance\models\decorators\ResourceDecoratorFactory;
use hipanel\modules\finance\models\Resource;
use hiqdev\billing\registry\ResourceDecorator\DecoratedInterface;
use hiqdev\billing\registry\ResourceDecorator\ResourceDecoratorInterface;

class MockResource extends Resource implements DecoratedInterface
{
    private ?ResourceDecoratorInterface $decorator = null;

    public function decorator(): ResourceDecoratorInterface
    {
        if ($this->decorator === null) {
            $this->decorator = ResourceDecoratorFactory::createFromResource($this);
        }

        return $this->decorator;
    }
}
