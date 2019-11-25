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

use hipanel\base\Model;
use hipanel\base\ModelTrait;

/**
 * Class PlanHistory
 * @package hipanel\modules\finance\models
 *
 * @property int $tariff_id
 * @property int $type_id
 * @property int|float $old_price
 * @property string $type_name
 * @property \DateTime $time
 */
class PlanHistory extends Model
{
    use ModelTrait;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'tariff_id', 'type_id'], 'integer'],
            [['old_price'], 'number'],
            [['type_name'], 'string'],
            [['time'], 'date'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [

        ]);
    }
}
