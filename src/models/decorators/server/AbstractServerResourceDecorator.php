<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models\decorators\server;

use hipanel\inputs\TextInput;
use hipanel\modules\finance\helpers\ResourceHelper;
use hipanel\modules\finance\models\decorators\AbstractResourceDecorator;
use hipanel\modules\finance\models\ServerResource;
use Yii;

abstract class AbstractServerResourceDecorator extends AbstractResourceDecorator
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

    public function displayTitleWithDirection(string $title): string
    {
        $direction = Yii::t('hipanel', 'OUT');
        if (str_contains($this->resource->type, '_in')) {
            $direction = Yii::t('hipanel', 'IN');
        }
        if (str_contains($this->resource->type, '_max')) {
            $direction = '';
        }

        return Yii::t('hipanel:finance', '{title} {direction}', ['title' => $title, 'direction' => $direction]);
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

    public function toUnit(): string
    {
        return $this->displayUnit();
    }

    public function displayOverusePrice()
    {
        return Yii::$app->formatter->asCurrency($this->getOverusePrice(), $this->resource->currency);
    }

    public function displayPrepaidAmount()
    {
        return Yii::t('hipanel:finance:tariff', '{amount} {unit}', [
            'amount' => $this->getPrepaidQuantity(),
            'unit' => $this->displayUnit(),
        ]);
    }

    public function prepaidAmountType()
    {
        return new TextInput();
    }

    public function displayAmountWithUnit(): string
    {
        return Yii::t('hipanel:finance:tariff', '{amount} {unit}', [
            'amount' => number_format(ResourceHelper::convertAmount($this), 3),
            'unit' => $this->displayUnit(),
        ]);
    }
}
