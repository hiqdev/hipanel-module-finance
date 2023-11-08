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

class PlanSearch extends Plan
{
    use SearchModelTrait {
        SearchModelTrait::searchAttributes as defaultSearchAttributes;
    }

    public function searchAttributes()
    {
        return ArrayHelper::merge($this->defaultSearchAttributes(), [
            'states', 'buyer_in', 'object_id_in', 'object_inilike',
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'type_in'             => Yii::t('hipanel', 'Type'),
            'buyer_in'            => Yii::t('hipanel:finance:sale', 'Buyer'),
            'name_ilike'          => Yii::t('hipanel:finance', 'Name'),
            'plan_ilike'          => Yii::t('hipanel:finance', 'Name'),
            'note_ilike'          => Yii::t('hipanel', 'Note'),
            'object_id_in'        => Yii::t('hipanel:finance', 'Object name'),
            'object_inilike'      => Yii::t('hipanel:finance', 'Object name'),
            'fee_ge'              => Yii::t('hipanel:finance', 'Subscription fee from'),
            'fee_le'              => Yii::t('hipanel:finance', 'Subscription fee to'),
            'currency_in'         => Yii::t('hipanel:finance', 'Currency'),
        ]);
    }
}
