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
        $toLine = fn($price, $currency, $unit, $quantity): string => Yii::t('hipanel:finance',
            "{sum} per {unit} over {quantity} {unit}",
            [
                'sum' => Html::tag('b', $this->formatter->asCurrency($price, $currency, [
                    NumberFormatter::MIN_FRACTION_DIGITS => 2,
                    NumberFormatter::MAX_FRACTION_DIGITS => 6,
                ])),
                'currency' => $currency,
                'quantity' => $quantity,
                'unit' => $unit,
            ]
        );
        $result[] = $toLine($price->price, $price->currency, $price->getUnitLabel(), $price->quantity);
        foreach ($thresholds as $threshold) {
            $result[] = $toLine($threshold->price, $threshold->currency, $threshold->getUnitLabel(), $threshold->quantity);
        }

        return nl2br(implode("\n", $result));
    }
}
