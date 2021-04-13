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
            [['currency'], 'safe'],
        ]);
    }

    public function searchAttributes()
    {
        return ArrayHelper::merge($this->defaultSearchAttributes(), [
            'limit',
            'balance_time',
            'currency',
        ]);
    }
}
