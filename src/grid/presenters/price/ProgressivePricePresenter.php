<?php declare(strict_types=1);

namespace hipanel\modules\finance\grid\presenters\price;

use hipanel\modules\finance\models\RepresentablePrice;
use NumberFormatter;
use Yii;
use yii\helpers\Html;

class ProgressivePricePresenter extends PricePresenter
{
    public function renderPrice(RepresentablePrice $price): string
    {
        $thresholds = $price->getThresholds();
        if ($this->shouldRenderAsStandardPrice($thresholds)) {
            return parent::renderPrice($price);
        }
        $result = $this->buildProgressivePriceLines($price, $thresholds);

        return nl2br(implode("\n", $result));
    }

    private function shouldRenderAsStandardPrice(array $thresholds): bool
    {
        return count($thresholds) === 1 && $thresholds[0]->getIsNewRecord();
    }

    /**
     * Build progressive price lines in format:
     * First X TB $Y.YY
     * Next X TB $Y.YY
     * Over X TB
     */
    private function buildProgressivePriceLines(RepresentablePrice $price, array $thresholds): array
    {
        $result = [];
        $unit = $price->getUnitLabel();
        $currency = $price->currency;

        // First line: from 0 to the first threshold
        $firstThreshold = (int)$thresholds[0]->quantity;
        $result[] = Yii::t(
            'hipanel:finance',
            "First {quantity} {unit} {sum}",
            [
                'quantity' => $firstThreshold,
                'unit' => $unit,
                'sum' => Html::tag('b', $this->formatSumAsCurrency($price->price, $currency)),
            ]
        );

        // Middle lines: from threshold[i] to threshold[i+1]
        for ($i = 0; $i < count($thresholds) - 1; $i++) {
            $currentQuantity = (int)$thresholds[$i]->quantity;
            $nextQuantity = (int)$thresholds[$i + 1]->quantity;
            $range = $nextQuantity - $currentQuantity;

            $result[] = Yii::t(
                'hipanel:finance',
                "Next {quantity} {unit} {sum}",
                [
                    'quantity' => $range,
                    'unit' => $unit,
                    'sum' => Html::tag('b', $this->formatSumAsCurrency($thresholds[$i]->price, $currency)),
                ]
            );
        }

        // Last line: "Over X TB"
        $lastThreshold = $thresholds[count($thresholds) - 1];
        $result[] = Yii::t(
            'hipanel:finance',
            "Over {quantity} {unit} {sum}",
            [
                'quantity' => $lastThreshold->quantity,
                'unit' => $unit,
                'sum' => Html::tag('b', $this->formatSumAsCurrency($lastThreshold->price, $currency)),
            ]
        );

        return $result;
    }

    private function formatSumAsCurrency(?string $price, string $currency): string
    {
        return $this->formatter->asCurrency($price, $currency, [
            NumberFormatter::MIN_FRACTION_DIGITS => 2,
            NumberFormatter::MAX_FRACTION_DIGITS => 6,
        ]);
    }
}
