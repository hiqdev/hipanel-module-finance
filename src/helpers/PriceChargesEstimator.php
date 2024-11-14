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
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Yii;
use yii\i18n\Formatter;

/**
 * Class PriceChargesEstimator.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PriceChargesEstimator
{
    protected Formatter $yiiFormatter;
    protected DecimalMoneyFormatter $moneyFormatter;
    protected array $calculations = [];

    /**
     * @var string[] array of strings compatible with `strtotime()`, e.g. `first day of next month`
     */
    protected array $periods = [];

    public function __construct(array $calculations)
    {
        $this->calculations = $calculations;
        $this->yiiFormatter = Yii::$app->formatter;
        $this->moneyFormatter = new DecimalMoneyFormatter(new ISOCurrencies());
    }

    public function __invoke($periods): void
    {
        $this->calculateForPeriods($periods);
    }

    /**
     * @return array
     * @var string[] $periods array of strings compatible with `strtotime()`, e.g. `first day of next month`
     */
    public function calculateForPeriods(array $periods): array
    {
        $this->periods = $periods;

        return $this->groupCalculationsByTarget();
    }

    private function groupCalculationsByTarget(): array
    {
        $result = [];

        foreach ($this->calculations as $period => $charges) {
            $chargesByTargetAndAction = [];

            foreach ($charges as $charge) {
                $action = $charge['action'];

                $targetId = $action['target']['id'];
                $actionType = $action['type']['name'];
                $priceType = $charge['price']['type']['name'];
                $sum = $charge['sum'];

                $money = new Money($sum['amount'], new Currency($sum['currency']));
                $price = $this->moneyFormatter->format($money);

                $chargesByTargetAndAction['targets'][$targetId][$actionType]['charges'][] = [
                    'type' => $priceType,
                    'price' => $price,
                    'currency' => $sum['currency'],
                    'comment' => $charge['comment'],
                    'formattedPrice' => $this->yiiFormatter->asCurrency($price, $sum['currency']),
                ];

                $chargesByTargetAndAction['sum'] += $price;
                $chargesByTargetAndAction['targets'][$targetId][$actionType]['quantity'] = max(
                    $charge['action']['quantity']['quantity'],
                    $chargesByTargetAndAction['targets'][$targetId][$actionType]['quantity'] ?? 0
                );
                $chargesByTargetAndAction['sumFormatted'] = $this->yiiFormatter->asCurrency(
                    $chargesByTargetAndAction['sum'],
                    $sum['currency']
                );
            }
            unset($action);

            if (!empty($chargesByTargetAndAction['targets'])) {
                foreach ($chargesByTargetAndAction['targets'] as &$actions) {
                    foreach ($actions as &$action) {
                        $this->decorateAction($action);
                    }
                }
            }
            unset($action, $actions);

            $result[$this->yiiFormatter->asDate(strtotime($period), 'php:M Y')] = $chargesByTargetAndAction;
        }

        return $result;
    }

    private function decorateAction(&$action): void
    {
        $action['sum'] = array_sum(array_column($action['charges'], 'price'));
        $action['currency'] = reset($action['charges'])['currency'];
        $action['sumFormatted'] = $this->yiiFormatter->asCurrency($action['sum'], $action['currency']);
        $action['detailsTable'] = PriceChargesEstimationTable::widget(['charges' => $action['charges']]);
    }
}
