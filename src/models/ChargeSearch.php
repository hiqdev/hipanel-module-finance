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

/**
 * Class ChargeSearch
 * @package hipanel\modules\finance\models
 */
class ChargeSearch extends Charge
{
    use SearchModelTrait {
        searchAttributes as defaultSearchAttributes;
    }

    /**
     * @inheritDoc
     */
    public function searchAttributes(): array
    {
        return ArrayHelper::merge($this->defaultSearchAttributes(), [
            'time_from',
            'time_till',
            'object_ids',
            'label_ilike',
            'tariff_id',
            'is_payed',
            'type_in',
        ]);
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'name_ilike' => Yii::t('hipanel', 'Object'),
            'ftype' => Yii::t('hipanel', 'Type'),
            'type_in' => Yii::t('hipanel', 'Type'),
            'tariff_id' => Yii::t('hipanel', 'Plan'),
        ]);
    }
}
