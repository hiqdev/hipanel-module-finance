<?php

namespace hipanel\modules\finance\helpers;

use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\widgets\PriceChargesEstimationTable;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Yii;

/**
 * Class PriceChargesEstimator
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class PriceChargesEstimator
{
    /**
     * @var \yii\i18n\Formatter
     */
    protected $yiiFormatter;
    /**
     * @var DecimalMoneyFormatter
     */
    protected $moneyFormatter;
    /**
     * @var array
     */
    protected $calculations = [];

    private $actions;
    private $prices;
    /**
     * @var string[] array of strings compatible with `strtotime()`, e.g. `first day of next month`
     */
    private $periods = [];

    public function __construct($actions, $prices)
    {
        $this->actions = $actions;
        $this->prices = $prices;
        $this->yiiFormatter = Yii::$app->formatter;
        $this->moneyFormatter = new DecimalMoneyFormatter(new ISOCurrencies());
    }

    public function __invoke($periods)
    {
        $this->calculateForPeriods($periods);
    }

    /**
     * @var string[] array of strings compatible with `strtotime()`, e.g. `first day of next month`
     * @return array
     */
    public function calculateForPeriods($periods): array
    {
        $this->periods = $periods;
        $this->calculations = $this->fetchCalculations();

        return $this->groupCalculationsByTarget();
    }

    private function fetchCalculations(): array
    {
        return Plan::perform('calculate-charges', [
            'actions' => $this->actions,
            'prices' => $this->prices,
            'times' => $this->periods,
        ]);
    }

    private function groupCalculationsByTarget()
    {
        $result = [];

        foreach ($this->calculations as $period => $charges) {
            $actionsByTarget = [];

            foreach ($charges as $charge) {
                $action = $charge['action'];

                $targetId = $action['target']['id'];
                $actionType = $action['type']['name'];
                $priceType = $charge['price']['type']['name'];
                $sum = $charge['sum'];

                $money = new Money($sum['amount'], new Currency($sum['currency']));
                $price = $this->moneyFormatter->format($money);

                $actionsByTarget[$targetId][$actionType]['charges'][] = [
                    'type' => $priceType,
                    'price' => $price,
                    'currency' => $sum['currency'],
                    'comment' => $charge['comment'],
                    'formattedPrice' => $this->yiiFormatter->asCurrency($price, $sum['currency']),
                ];
            }

            foreach ($actionsByTarget as &$actions) {
                foreach ($actions as &$action) {
                    $this->decorateAction($action);
                }
            }
            unset($action, $actions);

            $result[$this->yiiFormatter->asDate(strtotime($period), 'php:M Y')] = $actionsByTarget;
        }

        return $result;
    }

    private function decorateAction(&$action)
    {
        $action['sum'] = array_sum(array_column($action['charges'], 'price'));
        $action['sumFormatted'] = $this->yiiFormatter->asCurrency($action['sum'],
            reset($action['charges'])['currency']);
        $action['detailsTable'] = PriceChargesEstimationTable::widget(['charges' => $action['charges']]);
    }
}
