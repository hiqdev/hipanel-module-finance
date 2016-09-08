<?php

namespace hipanel\modules\finance\models\stubs;

use hipanel\modules\finance\models\decorators\AbstractResourceDecorator;
use hipanel\modules\finance\models\decorators\server\AbstractServerResourceDecorator;
use hipanel\modules\finance\models\decorators\server\ServerResourceDecoratorFactory;
use yii\base\InvalidConfigException;
use yii\base\Model;

class ServerResourceStub extends AbstractResourceStub
{
    /**
     * @var string
     */
    public $model_type;

    /**
     * @return AbstractServerResourceDecorator
     */
    public function decorator()
    {
        if (empty($this->decorator)) {
            $this->decorator = ServerResourceDecoratorFactory::createFromResource($this);
        }

        return $this->decorator;
    }
}
