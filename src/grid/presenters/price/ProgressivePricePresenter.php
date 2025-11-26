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
     * First X TB Included (if prepaid > 0)
     * First X TB $Y.YY (if prepaid = 0)
     * Next X TB $Y.YY
     * Over X TB
     */
    private function buildProgressivePriceLines(RepresentablePrice $price, array $thresholds): array
    {
        $result = [];
        $unit = $price->getUnitLabel();
        $currency = $price->currency;
        $prepaidQuantity = (int)$price->quantity;

        // First line: prepaid quantity (if > 0) or first tier
        if ($prepaidQuantity > 0) {
            // Prepaid line: "First X TB Included" - no discount
            $result[] = Yii::t(
                'hipanel:finance',
                "First {quantity} {unit} {included}",
                [
                    'quantity' => $prepaidQuantity,
                    'unit' => $unit,
                    'included' => Html::tag('b', Yii::t('hipanel:finance', 'Included')),
                ]
            );
        }

        $previousQuantity = $prepaidQuantity;
        $previousThresholdIndex = null; // Track the last processed threshold index
        $previousPrice = null; // Track previous price for discount calculation
        $isFirstLine = ($prepaidQuantity === 0); // Track if we need to render "First" line

        // Process all thresholds
        for ($i = 0; $i < count($thresholds); $i++) {
            $currentThreshold = (int)$thresholds[$i]->quantity;

            // Skip thresholds that are within the prepaid range
            if ($currentThreshold <= $prepaidQuantity) {
                $previousThresholdIndex = $i; // Track skipped thresholds
                continue;
            }

            $range = $currentThreshold - $previousQuantity;
            $isLast = ($i === count($thresholds) - 1);

            if ($isFirstLine) {
                // First line without prepaid: "First X TB $Y.YY" - no discount
                $result[] = Yii::t(
                    'hipanel:finance',
                    "First {quantity} {unit} {sum}",
                    [
                        'quantity' => $range,
                        'unit' => $unit,
                        'sum' => Html::tag('b', $this->formatSumAsCurrency($price->price, $currency)),
                    ]
                );
                $previousPrice = (float)$price->price;
                $isFirstLine = false;
            } else {
                // Middle lines: "Next X TB $Y.YY" - with discount
                // Determine which price to use based on the previous threshold
                if ($previousThresholdIndex !== null) {
                    // Use the price from the previous threshold
                    $priceToUse = $thresholds[$previousThresholdIndex]->price;
                } else {
                    // No previous threshold processed, use base price
                    $priceToUse = $price->price;
                }

                $currentPriceValue = (float)$priceToUse;
                $discountLabel = '';

                // Calculate discount if we have previous price and no prepaid at start
                if ($previousPrice !== null) {
                    $discount = $this->calculateDiscount($previousPrice, $currentPriceValue);
                    $discountLabel = $this->formatDiscountLabel($discount);
                }

                $result[] = Yii::t(
                    'hipanel:finance',
                    "Next {quantity} {unit} {sum}{discount}",
                    [
                        'quantity' => $range,
                        'unit' => $unit,
                        'sum' => Html::tag('b', $this->formatSumAsCurrency($priceToUse, $currency)),
                        'discount' => $discountLabel,
                    ]
                );

                $previousPrice = $currentPriceValue;
            }

            if ($isLast) {
                // Last line: "Over X TB $Y.YY" - with discount
                $lastPrice = (float)$thresholds[$i]->price;
                $discountLabel = '';

                if ($previousPrice !== null) {
                    $discount = $this->calculateDiscount($previousPrice, $lastPrice);
                    $discountLabel = $this->formatDiscountLabel($discount);
                }

                $result[] = Yii::t(
                    'hipanel:finance',
                    "Over {quantity} {unit} {sum}{discount}",
                    [
                        'quantity' => $currentThreshold,
                        'unit' => $unit,
                        'sum' => Html::tag('b', $this->formatSumAsCurrency($thresholds[$i]->price, $currency)),
                        'discount' => $discountLabel,
                    ]
                );
            }

            $previousQuantity = $currentThreshold;
            $previousThresholdIndex = $i;
        }

        return $result;
    }

    private function formatSumAsCurrency(?string $price, string $currency): string
    {
        return $this->formatter->asCurrency($price, $currency, [
            NumberFormatter::MIN_FRACTION_DIGITS => 2,
            NumberFormatter::MAX_FRACTION_DIGITS => 6,
        ]);
    }

    /**
     * Calculate discount percentage between previous and current price
     *
     * @param float $previousPrice
     * @param float $currentPrice
     * @return int Rounded discount percentage
     */
    private function calculateDiscount(float $previousPrice, float $currentPrice): int
    {
        if ($previousPrice <= 0) {
            return 0;
        }

        $discount = (($previousPrice - $currentPrice) / $previousPrice) * 100;

        return (int)round($discount);
    }

    /**
     * Format discount label for display
     *
     * @param int $discount
     * @return string Formatted discount label or empty string
     */
    private function formatDiscountLabel(int $discount): string
    {
        if ($discount <= 0) {
            return '';
        }

        return ' ' . Html::tag('span', "(-$discount%)", ['class' => 'label label-success']);
    }
}
