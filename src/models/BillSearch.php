<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\models;

use hipanel\base\SearchModelTrait;
use hipanel\helpers\ArrayHelper;
use Yii;

class BillSearch extends Bill
{
    use SearchModelTrait {
        searchAttributes as defaultSearchAttributes;
        rules as defaultRules;
    }

    public function rules()
    {
        return ArrayHelper::merge($this->defaultRules(), [
            [['time_from', 'time_till'], 'date', 'format' => 'php:Y-m-d'],
            [['servers', 'server_ids'], 'safe'],
        ]);
    }

    public function searchAttributes()
    {
        return ArrayHelper::merge($this->defaultSearchAttributes(), [
            'time_from', 'time_till',
            'servers', 'server_ids',
            'type',
        ]);
    }

    public function attributeLabels()
    {
        return $this->mergeAttributeLabels([
            'servers' => Yii::t('hipanel:finance', 'Servers'),
        ]);
    }
}
