<?php

namespace hipanel\modules\finance\behaviors;

use hipanel\modules\finance\models\Bill;
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
        if (in_array($this->owner->{$this->typeAttribute}, $this->negativeTypes)) {
            $this->owner->{$this->sumAttribute} = $this->owner->{$this->sumAttribute} * -1;
        }
    }

}
