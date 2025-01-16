<?php declare(strict_types=1);

namespace hipanel\modules\finance\grid\presenters\price;

use hipanel\modules\finance\models\Price;
use NumberFormatter;
use Yii;
use yii\helpers\Html;

class ProgressivePricePresenter extends PricePresenter
{
    public function renderPrice(Price $price): string
    {
        $thresholds = $price->getThresholds();
        if (count($thresholds) === 1 && $thresholds[0]->getIsNewRecord()) {
            return parent::renderPrice($price);
        }
        $result = [];
        $result[] = $this->toLine($price->price, $price->currency, $price->getUnitLabel(), $price->quantity);
        foreach ($thresholds as $threshold) {
            $result[] = $this->toLine($threshold->price, $threshold->currency, $threshold->getUnitLabel(), $threshold->quantity);
        }

        return nl2br(implode("\n", $result));
    }

    private function toLine(?string $price, string $currency, ?string $unit, string $quantity): string
    {
        return Yii::t('hipanel:finance',
            "{sum} per {unit} over {quantity} {unit}",
            [
                'sum' => Html::tag('b', $this->formatSumAsCurrency($price, $currency)),
                'unit' => $unit,
                'currency' => $currency,
                'quantity' => $quantity,
            ]
        );
    }

    private function formatSumAsCurrency(?string $price, string $currency): string
    {
        return $this->formatter->asCurrency($price, $currency, [
            NumberFormatter::MIN_FRACTION_DIGITS => 2,
            NumberFormatter::MAX_FRACTION_DIGITS => 6,
        ]);
    }
}
