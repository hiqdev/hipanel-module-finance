<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models\decorators\server;

use hipanel\inputs\TextInput;
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
        return Yii::t('hipanel', $this->resource->unit);
    }

    public function displayOverusePrice()
    {
        return Yii::$app->formatter->asCurrency($this->getOverusePrice(), $this->resource->currency);
    }

    public function displayPrepaidAmount()
    {
        return \Yii::t('hipanel:finance:tariff', '{amount} {unit}', [
            'amount' => $this->getPrepaidQuantity(),
            'unit' => $this->displayUnit(),
        ]);
    }

    public function prepaidAmountType()
    {
        return new TextInput();
    }
}
