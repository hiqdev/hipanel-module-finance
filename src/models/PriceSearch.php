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

class PriceSearch extends Price
{
    use SearchModelTrait {
        searchAttributes as defaultSearchAttributes;
    }

    protected function searchAttributes()
    {
        return array_merge(self::defaultSearchAttributes(), [
            'object_name_ilike',
            'buyer_ilike',
            'plan_owner_ilike',
            'model_group_name_ilike',
            'model_partno_ilike',
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'currency_in' => Yii::t('hipanel', 'Currency'),
            'object_name_ilike' => Yii::t('hipanel.finance.price', 'Object name'),
            'plan_name_ilike' => Yii::t('hipanel.finance.price', 'Tariff plan name'),
            'buyer_ilike' => Yii::t('hipanel.finance.price', 'Tariff plan buyer'),
            'plan_owner_ilike' => Yii::t('hipanel.finance.price', 'Tariff plan owner'),
            'model_group_name_ilike' => Yii::t('hipanel.finance.price', 'Group model name'),
            'model_partno_ilike' => Yii::t('hipanel.finance.price', 'Model partno'),
        ]);
    }
}
