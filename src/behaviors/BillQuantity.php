<?php

namespace hipanel\modules\finance\behaviors;

use hipanel\modules\finance\logic\bill\BillQuantityFactory;
use hipanel\modules\finance\logic\bill\BillQuantityInterface;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

class BillQuantity extends AttributeBehavior
{
    public $quantityAttribute = 'quantity';

    public $billTypeAttribute = 'type';

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'transformQuantity',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'transformQuantity',
        ];
    }

    public function transformQuantity()
    {
        $billQty = (new BillQuantityFactory())->createByType($this->owner->{$this->billTypeAttribute}, $this->owner);

        if ($billQty and $billQty instanceof BillQuantityInterface) {
            $this->owner->{$this->quantityAttribute} = $billQty->getValue();
        }
    }
}
