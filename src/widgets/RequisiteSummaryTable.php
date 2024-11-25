<?php declare(strict_types=1);

namespace hipanel\modules\finance\widgets;

use hipanel\modules\finance\models\Requisite;

class RequisiteSummaryTable extends ChargeFinanceSummaryTable
{
    protected function calculate(): array
    {
        $positive = $negative = $eurAmount = $total = [];
        /** @var Requisite $model */
        foreach ($this->displayModels as $model) {
            foreach ($model->balances as $currency => $balance) {
                $positive[$currency] = $balance['debit'];
                $negative[$currency] = $balance['credit'];
                $total[$currency] = $balance['balance'];
                $eurAmount['eur'] += $balance['eur_sum'];
            }
        }

        return [
            'positive' => $positive,
            'negative' => $negative,
            'eurAmount' => $eurAmount,
            'total' => $total,
        ];
    }
}
