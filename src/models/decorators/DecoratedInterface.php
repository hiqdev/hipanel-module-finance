<?php

namespace hipanel\modules\finance\models\decorators;

interface DecoratedInterface
{
    public function decorator(): ResourceDecoratorInterface;
}
