<?php

namespace hipanel\modules\finance\models;

use hipanel\modules\finance\models\decorators\DecoratedInterface;
use hipanel\modules\finance\models\decorators\ResourceDecoratorInterface;
use hipanel\modules\finance\models\decorators\target\TargetResourceDecoratorFactory;

class TargetResource extends Resource implements DecoratedInterface
{
    public function decorator(): ResourceDecoratorInterface
    {
        if (empty($this->decorator)) {
            $this->decorator = TargetResourceDecoratorFactory::createFromResource($this);
        }

        return $this->decorator;
    }
}
