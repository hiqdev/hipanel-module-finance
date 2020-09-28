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
use hipanel\modules\finance\models\decorators\AbstractResourceDecorator;
use hipanel\modules\finance\models\ServerResource;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\Unit;
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
        $amount = $this->getPrepaidQuantity();
        $convertibleTypes = [
            'backup_du',
            'hdd',
            'ram',
            'speed',
            'server_traf95_max',
            'server_traf95_in',
            'server_traf95',
            'server_traf_max',
            'server_traf_in',
            'server_traf',
            'server_du',
        ];
        if (in_array($this->resource->type, $convertibleTypes, true)) {
            $amount = Quantity::create(Unit::create($this->resource->unit), $amount)
                ->convert(Unit::create($this->toUnit()))
                ->getQuantity();
            $amount = number_format($amount, 3);
        }

        return Yii::t('hipanel:finance:tariff', '{amount} {unit}', [
            'amount' => $amount,
            'unit' => $this->displayUnit(),
        ]);
    }
}
