<?php

namespace hipanel\modules\finance\helpers;

use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\widgets\PriceChargesEstimationTable;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Yii;

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
    private $periods;

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
            'times' => [
                'now',
                'first day of +1 month',
                'first day of +2 month',
            ],
        ]);
    }

    private function groupCalculationsByTarget()
    {
        $result = [];
        $response = $this->calculations;

        foreach ($response as $period => $charges) {
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
