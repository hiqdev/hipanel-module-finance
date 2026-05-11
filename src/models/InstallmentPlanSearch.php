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
use hipanel\helpers\ArrayHelper;
use Yii;

class InstallmentPlanSearch extends InstallmentPlan
{
    use SearchModelTrait {
        searchAttributes as defaultSearchAttributes;
    }

    public function searchAttributes()
    {
        return ArrayHelper::merge($this->defaultSearchAttributes(), [
            'month', 'time_from', 'time_till',
            'seller_id',
            'serialno_inilike', 'partno_inilike', 'device_like',
            'since_ge', 'since_le', 'till_ge', 'till_le',
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'serialno_inilike' => Yii::t('hipanel:finance', 'Serial'),
            'partno_inilike'   => Yii::t('hipanel:finance', 'Part No.'),
            'device_like'      => Yii::t('hipanel:finance:sale', 'Device'),
            'since_ge'         => Yii::t('hipanel:finance', 'Installment start'),
            'since_le'         => Yii::t('hipanel:finance', 'Installment start'),
            'till_ge'          => Yii::t('hipanel:finance', 'Installment end'),
            'till_le'          => Yii::t('hipanel:finance', 'Installment end'),
        ]);
    }
}
