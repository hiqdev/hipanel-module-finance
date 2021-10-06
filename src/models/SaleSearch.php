<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use hipanel\base\SearchModelTrait;
use Yii;
use yii\helpers\ArrayHelper;

class SaleSearch extends Sale
{
    private const SALE_CONDITION_OPEN = 'open';
    private const SALE_CONDITION_CLOSE = 'close';

    use SearchModelTrait {
        searchAttributes as defaultSearchAttributes;
    }

    public function searchAttributes()
    {
        return ArrayHelper::merge($this->defaultSearchAttributes(), [
            'object_inilike',
            'object_label_ilike',
            'open_time_from', 'open_time_till',
            'close_time_from', 'close_time_till',
            'sale_condition',
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'object_inilike' => Yii::t('hipanel', 'Object'),
            'object_label_ilike' => Yii::t('hipanel', 'Description'),
            'sale_condition' => Yii::t('hipanel:finance:sale', 'Sale condition'),
        ]);
    }

    public function getConditions(): array
    {
        return [
            self::SALE_CONDITION_OPEN => Yii::t('hipanel:finance:sale', 'Show opened'),
            self::SALE_CONDITION_CLOSE => Yii::t('hipanel:finance:sale', 'Show closed'),
        ];
    }
}
