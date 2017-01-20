<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\behaviors;

use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

class BillNegation extends AttributeBehavior
{
    public $sumAttribute = 'sum';

    public $typeAttribute = 'type';

    public $negativeTypes = [];

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'calculateSum',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'calculateSum',
        ];
    }

    public function calculateSum()
    {
        if (in_array($this->owner->{$this->typeAttribute}, $this->negativeTypes, true)) {
            $this->owner->{$this->sumAttribute} = $this->owner->{$this->sumAttribute} * -1;
        }
    }
}
