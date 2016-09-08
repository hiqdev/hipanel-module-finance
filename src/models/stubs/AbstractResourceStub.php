<?php

namespace hipanel\modules\finance\models\stubs;

use hipanel\modules\finance\models\decorators\AbstractResourceDecorator;
use hipanel\modules\finance\models\Tariff;
use yii\base\InvalidConfigException;
use yii\base\Model;

abstract class AbstractResourceStub extends Model
{
    /**
     * @var Tariff
     */
    public $tariff;

    /**
     * @var string
     */
    public $type;

    /**
     * @var AbstractResourceDecorator
     */
    protected $decorator;

    /**
     * @return AbstractResourceDecorator
     * @throws InvalidConfigException
     */
    public function decorator()
    {
        throw new InvalidConfigException('Method "decorator" is not available for class Resource');
    }
}
