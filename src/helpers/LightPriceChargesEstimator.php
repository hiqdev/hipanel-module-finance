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
                    foreach ($action['charges'] as &$charge) {
                        $money = new Money($charge['sum'], new Currency($charge['currency']));
                        $charge['price'] = $this->moneyFormatter->format($money);
                        $charge['formattedPrice'] = $this->yiiFormatter->asCurrency($charge['price'], $charge['currency']);
                        if (!isset($chargesByTargetAndAction['sum'])) {
                            $chargesByTargetAndAction['sum'] = 0;
                        }
                        $chargesByTargetAndAction['sum'] += $charge['price'];
                    }
                    $this->decorateAction($action);
                }
            }

            $chargesByTargetAndAction['sumFormatted'] = $this->yiiFormatter->asCurrency($chargesByTargetAndAction['sum'], $chargesByTargetAndAction['currency']);
            $result[$this->yiiFormatter->asDate(strtotime($period), 'php:M Y')] = $chargesByTargetAndAction;
        }

        return $result;
    }

    private function decorateAction(&$action)
    {
        $action['sum'] = array_sum(array_column($action['charges'], 'price'));
        $action['sumFormatted'] = $this->yiiFormatter->asCurrency($action['sum'], $action['currency']);
        $action['detailsTable'] = PriceChargesEstimationTable::widget(['charges' => $action['charges']]);
    }
}
