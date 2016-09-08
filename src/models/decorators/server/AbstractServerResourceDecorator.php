<?php

namespace hipanel\modules\finance\models\decorators\server;

use hipanel\inputs\TextInput;
use hipanel\modules\finance\models\decorators\AbstractResourceDecorator;
use hipanel\modules\finance\models\decorators\ResourceDecoratorInterface;
use hipanel\modules\finance\models\ServerResource;
use Yii;
use yii\helpers\Inflector;

abstract class AbstractServerResourceDecorator extends AbstractResourceDecorator implements ResourceDecoratorInterface
{
    /**
     * @var ServerResource
     */
    public $resource;

    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    public function displayTitle()
    {
        return $this->resource->getTypes()[$this->resource->type];
    }

    public function getPrepaidQuantity()
    {
        return $this->resource->quantity;
    }

    public function getOverusePrice()
    {
        return $this->resource->price;
    }

    public function displayUnit()
    {
        return $this->resource->unit;
    }

    public function displayOverusePrice()
    {
        return Yii::$app->formatter->asCurrency($this->getOverusePrice(), $this->resource->currency);
    }

    public function displayPrepaidAmount()
    {
        return \Yii::t('hipanel/finance/tariff', '{amount} {unit}', [
            'amount' => $this->getPrepaidQuantity(),
            'unit' => $this->displayUnit()
        ]);
    }

    public function prepaidAmountType()
    {
        return new TextInput();
    }

    public function realObjectId()
    {
        $resource = $this->resource;

        if (!$resource->isPeriodic()) {
            return $resource->object_id;
        }

        if (!$resource->tariff->is_personal && isset($resource->r_object_id)) {
            return $resource->r_object_id;
        }

        return $resource->object_id;
    }
}
