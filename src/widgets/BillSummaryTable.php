<?php

namespace hipanel\modules\finance\widgets;

use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\helpers\CurrencyFilter;
use hipanel\modules\finance\models\Bill;
use JetBrains\PhpStorm\Pure;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class BillSummaryTable extends Widget
{
    /** @var Bill[] */
    public array $onPageBills = [];

    /** @var Bill[] */
    public array $allBills = [];

    public array $currencies = [];

    public function init(): void
    {
        $this->currencies = CurrencyFilter::addSymbolAndFilter($this->currencies);
    }

    public function run(): string
    {
        $tables = '';
        if (!empty($this->onPageBills)) {
            $tables .= Html::tag('div', $this->buildTable(Yii::t('hipanel:finance', 'On the page summary'), $this->onPageBills), ['class' => 'table-responsive', 'style' => 'margin: 1rem']);
        }
        if (!empty($this->allBills)) {
            $tables .= Html::tag('div', $this->buildTable(Yii::t('hipanel:finance', 'Total summary'), $this->allBills), ['class' => 'table-responsive', 'style' => 'margin: 1rem']);
        }

        return Html::tag('div', Html::tag('div', $tables, ['style' => 'display: flex; flex-wrap: wrap; margin: -1rem']), ['class' => 'summary']);
    }

    private function buildTable(string $name, array $bills): string
    {
        $currencies = $this->currencies;
        $rows = [
            'positive' => Yii::t('hipanel:finance', 'Positive'),
            'negative' => Yii::t('hipanel:finance', 'Negative'),
            'total' => Yii::t('hipanel:finance', 'Total'),
        ];
        [$positive, $negative, $total] = $this->calculate($bills);
        $head = Html::beginTag('thead');
        $head .= Html::beginTag('tr');
        $head .= Html::tag('th', '&nbsp;');
        foreach ($currencies as $currency => $sign) {
            if (isset($positive[$currency]) || isset($negative[$currency])) {
                $head .= Html::tag('th', $sign, ['class' => 'text-right']);
            } else {
                unset($currencies[$currency]);
            }
        }
        $head .= Html::endTag('tr');
        $head .= Html::endTag('thead');
        $body = Html::beginTag('tbody');
        foreach ($rows as $type => $label) {
            $body .= Html::beginTag('tr');
            $body .= Html::tag('th', $label, ['class' => 'text-right']);
            foreach ($currencies as $currency => $sign) {
                $sum = ColoredBalance::widget([
                    'model' => new Bill(['sum' => $$type[$currency], 'currency' => $currency]),
                    'attribute' => 'sum',
                ]);
                $body .= Html::tag('td', $sum, ['class' => 'text-right']);
            }
            $head .= Html::endTag('tr');
        }
        $body .= Html::endTag('tbody');

        return Html::tag('table', Html::tag('caption', $name) . $head . $body, ['class' => 'table table-striped table-bordered table-condensed']);
    }

    private function calculate(array $bills): array
    {
        $positive = $negative = $total = [];
        foreach ($bills as $bill) {
            if ($this->isGrouped($bill)) {
                $positive[$bill->currency] = $bill->positive;
                $negative[$bill->currency] = $bill->negative;
                $total[$bill->currency] = $bill->sum;
            } else if ($bill->sum >= 0) {
                $positive[$bill->currency] += abs($bill->sum);
                $total[$bill->currency] += abs($bill->sum);
            } else {
                $negative[$bill->currency] -= abs($bill->sum);
                $total[$bill->currency] -= abs($bill->sum);
            }
        }

        return [$positive, $negative, $total];
    }

    private function isGrouped(Bill $bill): bool
    {
        return $bill->negative || $bill->positive;
    }
}
