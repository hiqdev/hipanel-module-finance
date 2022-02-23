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

use hipanel\modules\finance\widgets\PriceChargesEstimationTable;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Yii;

/**
 * Class LightPriceChargesEstimator.
 *
 * @author Yurii Myronchuk <bladeroot@gmail.com>
 */
class LightPriceChargesEstimator extends PriceChargesEstimator
{
    /**
     * @var string[] array of strings compatible with `strtotime()`, e.g. `first day of next month`
     */
    private $periods = [];

    /**
     * @var string[] array of strings compatible with `strtotime()`, e.g. `first day of next month`
     * @return array
     */
    public function calculateForPeriods($periods): array
    {
        $this->periods = $periods;

        return $this->groupCalculationsByTarget();
    }

    private function groupCalculationsByTarget()
    {
        $result = [];

        foreach ($this->calculations as $period => &$chargesByTargetAndAction) {
            foreach ($chargesByTargetAndAction['targets'] as $target => &$actions) {
                foreach ($actions as &$action) {
                    $this->decorateAction($action);
                }
            }

            $result[$period] = $chargesByTargetAndAction;
        }

        return $result;
    }

    private function decorateAction(&$action)
    {
        $action['detailsTable'] = PriceChargesEstimationTable::widget(['charges' => $action['charges']]);
    }
}
