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
 * RequisiteSearch represents the model behind the search form about `hipanel\modules\finance\models\Requisite`.
 */
class RequisiteSearch extends Requisite
{
    use SearchModelTrait {
        searchAttributes as defaultSearchAttributes;
    }

    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['balance_time'], 'date', 'format' => 'php:Y-m-d'],
            [['currency', 'name_in'], 'safe'],
            [['name_ilike'], 'string'],
            [['name_insubstri'], 'safe'],
        ]);
    }

    public function searchAttributes()
    {
        return ArrayHelper::merge($this->defaultSearchAttributes(), [
            'limit',
            'balance_time',
            'currency',
            'name_insubstri',
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'balance_time' => Yii::t('hipanel:finance', 'Balance time'),
        ]);
    }
}
