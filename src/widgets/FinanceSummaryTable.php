<?php

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\helpers\CurrencyFilter;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\models\HasSumAndCurrencyAttributesInterface;
use Yii;
use yii\base\Widget;
use yii\helpers\Html;

class FinanceSummaryTable extends Widget
{
    /** @var HasSumAndCurrencyAttributesInterface[] */
    public array $onPageModels = [];

    /** @var HasSumAndCurrencyAttributesInterface[] */
    public array $allModels = [];

    public array $currencies = [];

    private array $rows = [];

    private string $tableName;

    private array $displayModels = [];

    public function init(): void
    {
        parent::init();

        $this->tableName = empty($this->onPageModels) ? 'Summary' : 'Page summary';
        $this->rows = [
            'positive' => Yii::t('hipanel:finance', 'Positive'),
            'negative' => Yii::t('hipanel:finance', 'Negative'),
            'openingBalance' => Yii::t('hipanel:finance', 'Opening balance'),
            'total' => Yii::t('hipanel:finance', 'Total'),
            'closingBalance' => Yii::t('hipanel:finance', 'Closing balance'),
        ];
        $this->displayModels = empty($this->onPageModels) ? $this->allModels : $this->onPageModels;
    }

    public function run(): string
    {
        if (empty($this->displayModels)) {
            return Html::tag('div', '', ['class' => 'summary']);
        }

        $values =  $this->calculate();
        return $this->render('FinanceSummaryTable', [
            'currencies' => $this->filterCurrencies($values),
            'tableName' => $this->tableName,
            'rows' => $this->rows,
            'values' => $values,
        ]);
    }

    private function filterCurrencies(array $tableValues): array
    {
        return array_filter(
            CurrencyFilter::addSymbolAndFilter($this->currencies),
            function ($curr) use ($tableValues) {
                foreach ($tableValues as $rowValues) {
                     if (!empty($rowValues[$curr])) {
                         return true;
                     }
                 }
                 return false;
            },
            ARRAY_FILTER_USE_KEY,
        );
    }

    private function calculate(): array
    {
        $positive = $negative = $total = $openingBalance = $closingBalance = [];
        foreach ($this->displayModels as $bill) {
            if ($bill instanceof Bill && $this->isGrouped($bill)) {
                $positive[$bill->currency] = $bill->positive;
                $negative[$bill->currency] = $bill->negative;
                $total[$bill->currency] = $bill->sum;
                $openingBalance[$bill->currency] = $bill->opening_balance;
                $closingBalance[$bill->currency] = $bill->closing_balance;
            } else if ($bill->sum >= 0) {
                $positive[$bill->currency] += abs($bill->sum);
                $total[$bill->currency] += abs($bill->sum);
            } else {
                $negative[$bill->currency] -= abs($bill->sum);
                $total[$bill->currency] -= abs($bill->sum);
            }
        }

        return [
            'positive' => $positive,
            'negative' => $negative,
            'total' => $total,
            'openingBalance' => $openingBalance,
            'closingBalance' => $closingBalance,
        ];
    }

    private function isGrouped(Bill $bill): bool
    {
        return $bill->negative || $bill->positive || $bill->opening_balance || $bill->closing_balance;
    }
}
