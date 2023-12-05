<?php

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\models\Charge;
use Yii;
use yii\helpers\Html;

class ChargeFinanceSummaryTable extends FinanceSummaryTable
{
    public function init(): void
    {
        parent::init();

        $this->tableName = empty($this->onPageModels) ? 'Summary' : 'Page summary';
        $this->rows = [
            'positive' => Yii::t('hipanel:finance', 'Positive'),
            'negative' => Yii::t('hipanel:finance', 'Negative'),
            'total' => Yii::t('hipanel:finance', 'Total'),
            'eurAmount' => Yii::t('hipanel:finance', 'EUR Amount'),
        ];
        $this->displayModels = empty($this->onPageModels) ? $this->allModels : $this->onPageModels;
    }

    public function run(): string
    {
        if (empty($this->displayModels)) {
            return Html::tag('div', '', ['class' => 'summary']);
        }

        $values = $this->calculate();

        return $this->render('FinanceSummaryTable', [
            'currencies' => $this->filterCurrencies($values),
            'tableName' => $this->tableName,
            'rows' => $this->rows,
            'values' => $values,
        ]);
    }

    protected function calculate(): array
    {
        $positive = $negative = $discount = $netAmount = $eurAmount = $total = [];
        /** @var $charge Charge */
        foreach ($this->displayModels as $charge) {
            $positive[$charge->currency] = $charge->positive;
            $negative[$charge->currency] = $charge->negative;
            $total[$charge->currency] = $charge->sum;
            $eurAmount['eur'] += $charge->eur_amount;
        }

        return [
            'positive' => $positive,
            'negative' => $negative,
            'eurAmount' => $eurAmount,
            'total' => $total,
        ];
    }
}
