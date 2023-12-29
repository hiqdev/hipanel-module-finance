<?php

declare(strict_types=1);

namespace hipanel\modules\finance\models;

use Yii;
use yii\base\Model;

class Costprice extends Model
{
    use \hipanel\base\ModelTrait;

    public function rules()
    {
        return [
            [['mask', 'month','type'], 'safe'],
        ];
    }

    public static function getAvailableType(): array
    {
        return [
            'all' => Yii::t('hipanel:finance', 'All'),
            CostpriceType::admin->value => Yii::t('hipanel:finance', 'Admin'),
            CostpriceType::colocation->value => Yii::t('hipanel:finance', 'Colocation'),
            CostpriceType::ip->value => Yii::t('hipanel:finance', 'IP'),
            CostpriceType::hw->value => Yii::t('hipanel:finance', 'HW'),
            CostpriceType::nrc->value => Yii::t('hipanel:finance', 'NRC'),
            CostpriceType::salaries->value => Yii::t('hipanel:finance', 'Salaries'),
            CostpriceType::salaries->value => Yii::t('hipanel:finance', 'Traff'),
        ];
    }

}