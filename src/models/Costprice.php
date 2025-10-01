<?php

declare(strict_types=1);

namespace hipanel\modules\finance\models;

use Yii;
use hipanel\base\Model;
use hipanel\base\ModelTrait;

class Costprice extends Model
{
    public const DEFAULT_PERIOD = 12;

    use ModelTrait;

    public function rules()
    {
        return [
            [['mask', 'month', 'type'], 'safe'],

            [['id', 'bill_id', 'charge_id', 'type_id'], 'integer'],
            [['amount', 'sum'], 'number'],
            [['description', 'note', 'unit', 'type', 'currency', 'object'], 'string'],
            [['month', 'update_time'], 'datetime'],
            [['rawCharge'], 'safe'],
        ];
    }

    public static function getAvailableType(): array
    {
        return [
            'all' => Yii::t('hipanel:finance', 'All'),
            CostpriceType::hw->value => Yii::t('hipanel:finance', 'HW'),
            CostpriceType::traff->value => Yii::t('hipanel:finance', 'Traff'),
        ];
    }

    public static function getAvailableReports(): array
    {
        return [
            CostpriceReport::traff->value => Yii::t('hipanel:finance', 'Traff'),
        ];
    }
}
