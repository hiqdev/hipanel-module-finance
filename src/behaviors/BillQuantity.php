<?php

namespace hipanel\modules\finance\behaviors;

use hipanel\modules\finance\logic\bill\QuantityFormatterFactory;
use hipanel\modules\finance\models\Charge;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

class BillQuantity extends AttributeBehavior
{
    public $quantityAttribute = 'quantity';

    /**
     * @var QuantityFormatterFactory
     */
    private $qtyFactory;

    public function __construct($config = [], QuantityFormatterFactory $qtyFactory)
    {
        parent::__construct($config);
        $this->qtyFactory = $qtyFactory;
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'transformQuantity',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'transformQuantity',
        ];
    }

    public function transformQuantity()
    {
        // For Bills
        if ($billQty = $this->transform($this->owner)) {
            $this->owner->{$this->quantityAttribute} = $billQty->getValue();
        }

        // For Charges
        if (isset($this->owner->charges) && !empty($this->owner->charges)) {
            foreach ($this->owner->charges as $k => $data) {
                $charge = new Charge($data);
                if ($chargeQty = $this->transform($charge)) {
                    $this->owner->charges[$k][$this->quantityAttribute] = $chargeQty->getValue();
                }
            }
        }
    }

    protected function transform($model)
    {
        return $this->qtyFactory->create($model);
    }
}
