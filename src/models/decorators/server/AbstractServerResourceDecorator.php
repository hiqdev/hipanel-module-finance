<?php

namespace hipanel\modules\finance\models\decorators\server;

use hipanel\modules\finance\models\decorators\AbstractResourceDecorator;
use hipanel\modules\finance\models\decorators\ResourceDecoratorInterface;
use hipanel\modules\finance\models\ServerResource;
use Yii;

abstract class AbstractServerResourceDecorator extends AbstractResourceDecorator implements ResourceDecoratorInterface
{
    /**
     * @var ServerResource
     */
    public $resource;

    public function __construct(ServerResource $resource)
    {
        parent::__construct($resource);
    }

    public function displayTitle()
    {
        return $this->resource->getAvailableTypes()[$this->resource->type];
    }

    public function getPrepaidAmount()
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
            'amount' => $this->getPrepaidAmount(),
            'unit' => $this->displayUnit()
        ]);
    }
}
