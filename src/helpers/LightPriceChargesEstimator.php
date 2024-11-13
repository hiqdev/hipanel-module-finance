<?php declare(strict_types=1);
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
use Money\Currency;
use Money\Money;

/**
 * Class LightPriceChargesEstimator.
 *
 * @author Yurii Myronchuk <bladeroot@gmail.com>
 */
class LightPriceChargesEstimator extends PriceChargesEstimator
{
    /** @inerhitDoc */
    public function calculateForPeriods($periods): array
    {
        $this->periods = $periods;

        return $this->groupCalculationsByTarget();
    }

    private function groupCalculationsByTarget(): array
    {
        $result = [];

        foreach ($this->calculations as $period => &$chargesByTargetAndAction) {
            $resultKey = $this->yiiFormatter->asDate(strtotime($period), 'php:M Y');
            if (empty($chargesByTargetAndAction['targets'])) {
                $tryToGetCurrency = array_unique(array_column($this->calculations, 'currency'));
                $result[$resultKey] = [
                    'targets' => [],
                    'currency' => reset($tryToGetCurrency),
                    'sum' => 0,
                    'sumFormatted' => null,
                ];
                continue;
            }
            foreach ($chargesByTargetAndAction['targets'] as &$actions) {
                foreach ($actions as &$action) {
                    foreach ($action['charges'] as &$charge) {
                        $money = new Money($charge['sum'], new Currency($charge['currency']));
                        $charge['price'] = $this->moneyFormatter->format($money);
                        $charge['formattedPrice'] = $this->yiiFormatter->asCurrency($charge['price'], $charge['currency']);
                        $chargesByTargetAndAction['sum'] ??= 0;

                        $chargesByTargetAndAction['sum'] += (int)$charge['price'];
                    }
                    $this->decorateAction($action);
                }
            }

            $chargesByTargetAndAction['sumFormatted'] = $this->yiiFormatter->asCurrency(
                $chargesByTargetAndAction['sum'],
                $chargesByTargetAndAction['currency']
            );
            $result[$resultKey] = $chargesByTargetAndAction;
        }

        return $result;
    }

    private function decorateAction(&$action): void
    {
        $action['sum'] = array_sum(array_column($action['charges'], 'price'));
        $action['sumFormatted'] = $this->yiiFormatter->asCurrency($action['sum'], $action['currency']);
        $action['detailsTable'] = PriceChargesEstimationTable::widget(['charges' => $action['charges']]);
    }
}
