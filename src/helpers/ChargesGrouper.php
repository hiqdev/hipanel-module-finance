<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\helpers;

use hipanel\modules\finance\models\Charge;
use Tuck\Sort\Sort;
use Tuck\Sort\SortChain;

/**
 * Class ChargesGrouper can be used to group charges inside $charge by commonObject->id.
 */
class ChargesGrouper
{
    /**
     * @var Charge[]
     */
    private $charges;

    /**
     * ChargesGrouper constructor.
     * @param Charge[] $charges
     */
    public function __construct(array $charges)
    {
        $this->charges = $charges;
    }

    /**
     * @return array of two elements:
     * 0: commonObject->id => commonObject->id, commonObject->name
     * 1: commonObject->id => array Charge[][] by commonObject->id
     */
    public function group(): array
    {
        /** @var Charge[] $idToNameObject */
        $idToNameObject = [];
        /** @var Charge[][] $chargesByMainObject */
        $chargesByMainObject = [];
        foreach ($this->charges as $charge) {
            $chargesByMainObject[$charge->commonObject->id][$charge->id] = $charge;
        }
        foreach ($this->charges as $charge) {
            $idToNameObject[$charge->commonObject->id] = $charge;
        }
        $idToNameObject = $this->sortByServerName()->values($idToNameObject);

        return [$idToNameObject, $chargesByMainObject];
    }

    private function sortByServerName(): SortChain
    {
        return Sort::chain()->compare(function (Charge $a, Charge $b) {
            return strnatcasecmp($a->commonObject->name ?? '', $b->commonObject->name ?? '');
        });
    }
}
