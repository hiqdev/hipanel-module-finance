<?php

namespace hipanel\modules\finance\models;

use hipanel\modules\finance\models\decorators\client\ClientResourceDecoratorFactory;
use hipanel\modules\finance\models\decorators\DecoratedInterface;
use hipanel\modules\finance\models\decorators\ResourceDecoratorInterface;

class ClientResource extends Resource implements DecoratedInterface
{
    public function decorator(): ResourceDecoratorInterface
    {
        if (empty($this->decorator)) {
            $this->decorator = ClientResourceDecoratorFactory::createFromResource($this);
        }

        return $this->decorator;
    }
}
