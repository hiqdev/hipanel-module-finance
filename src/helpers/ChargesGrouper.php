<?php

namespace hipanel\modules\finance\helpers;

use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;

/**
 * Class ChargesGrouper can be used to group charges inside $charge by common_object_id
 *
 */
class ChargesGrouper
{
    /**
     * @var Bill
     */
    private $charge;

    public function __construct(Bill $charge)
    {
        $this->charge = $charge;
    }

    /**
     * @return array of two elements:
     * 0: common_object_id => common_object_id, common_object_name
     * 1: common_object_id => array Charge[][] by common_object_id
     */
    public function group()
    {
        $model = $this->charge;
        /** @var Charge[] $idToNameObject */
        $idToNameObject = [];
        /** @var Charge[][] $chargesByMainObject */
        $chargesByMainObject = [];
        foreach ($model->charges as $charge) {
            $chargesByMainObject[$charge->common_object_id][$charge->id] = $charge;
        }
        foreach ($model->charges as $charge) {
            $idToNameObject[$charge->common_object_id] = $charge;
        }
        return [$idToNameObject, $chargesByMainObject];
    }
}

