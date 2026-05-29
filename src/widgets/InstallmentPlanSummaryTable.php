<?php

declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use Yii;
use yii\helpers\Html;

class InstallmentPlanSummaryTable extends FinanceSummaryTable
{
    public function init(): void
    {
        parent::init();

        $this->tableName = empty($this->onPageModels) ? Yii::t('hipanel:finance', 'Summary') : Yii::t('hipanel:finance', 'Page summary');
        $this->rows = [
            'expected_sum'         => Yii::t('hipanel:finance', 'Total sum'),
            'expected_monthly_sum' => Yii::t('hipanel:finance', 'Monthly sum'),
            'charged_sum'          => Yii::t('hipanel:finance', 'Charged sum'),
            'left_sum'             => Yii::t('hipanel:finance', 'Left sum'),
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
            'tableName'  => $this->tableName,
            'rows'       => $this->rows,
            'values'     => $values,
        ]);
    }

    protected function calculate(): array
    {
        $expectedSum = $expectedMonthlySum = $chargedSum = $leftSum = [];

        foreach ($this->displayModels as $row) {
            $currency = $row->currency;
            $expectedSum[$currency]        = (float)$row->expected_sum;
            $expectedMonthlySum[$currency] = (float)$row->expected_monthly_sum;
            $chargedSum[$currency]         = (float)$row->charged_sum;
            $leftSum[$currency]            = (float)$row->left_sum;
        }

        return [
            'expected_sum'         => $expectedSum,
            'expected_monthly_sum' => $expectedMonthlySum,
            'charged_sum'          => $chargedSum,
            'left_sum'             => $leftSum,
        ];
    }
}
