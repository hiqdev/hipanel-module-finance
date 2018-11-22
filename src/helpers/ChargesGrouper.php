<?php

namespace hipanel\modules\finance\helpers;

use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\Charge;
use Tuck\Sort\Sort;
use Tuck\Sort\SortChain;

/**
 * Class ChargesGrouper can be used to group charges inside $charge by common_object_id
 *
 */
class ChargesGrouper
{
    /**
     * @var Bill
     */
    private $charges;

    public function __construct(Bill $bill)
    {
        $this->charges = $bill->charges;
    }

    /**
     * @return array of two elements:
     * 0: common_object_id => common_object_id, common_object_name
     * 1: common_object_id => array Charge[][] by common_object_id
     */
    public function group()
    {
        $model = $this->charges;
        /** @var Charge[] $idToNameObject */
        $idToNameObject = [];
        /** @var Charge[][] $chargesByMainObject */
        $chargesByMainObject = [];
        foreach ($model as $charge) {
            $chargesByMainObject[$charge->common_object_id][$charge->id] = $charge;
        }
        foreach ($model as $charge) {
            $idToNameObject[$charge->common_object_id] = $charge;
        }
        $idToNameObject = $this->sortByServerName()->values($idToNameObject);
        return [$idToNameObject, $chargesByMainObject];
    }

    private function sortByServerName(): SortChain
    {
        return Sort::chain()->compare(function (Charge $a, Charge $b) {
            return strnatcasecmp($a->common_object_name, $b->common_object_name);
        });
    }
}

