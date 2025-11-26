<?php declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\grid\presenters\price;

use hipanel\modules\finance\grid\presenters\price\ProgressivePricePresenter;
use hipanel\modules\finance\models\RepresentablePrice;
use hipanel\modules\finance\models\Threshold;
use hipanel\modules\finance\tests\unit\TestCase;
use yii\i18n\Formatter;
use yii\web\User;

class ProgressivePricePresenterTest extends TestCase
{
    private ProgressivePricePresenter $presenter;

    protected function setUp(): void
    {
        parent::setUp();

        $formatter = $this->createMock(Formatter::class);
        $formatter->method('asCurrency')->willReturnCallback(fn($value, $currency) => '$' . number_format((float)$value, 2));
        $user = $this->createMock(User::class);
        $user->method('can')->willReturn(true);

        $this->presenter = new ProgressivePricePresenter($formatter, $user);
    }

    /**
     * Helper method to parse a result into clean lines and assert they contain expected strings in order
     *
     * @param string $result HTML result from renderPrice()
     * @param array $expectedStrings Array of strings that should appear in order in the lines
     * @param string $message Optional assertion message
     */
    private function assertLinesContain(string $result, array $expectedStrings, string $message = ''): void
    {
        // Strip HTML tags and split by line breaks
        $lines = $this->getCleanLines($result);

        // Check that we have enough lines
        $this->assertGreaterThanOrEqual(
            count($expectedStrings),
            count($lines),
            'Result does not have enough lines for all expected strings. ' . $message
        );

        // Assert each expected string appears in the corresponding line
        foreach ($expectedStrings as $index => $expectedString) {
            $this->assertStringContainsString(
                $expectedString,
                $lines[$index],
                sprintf(
                    'Line %d does not contain expected string "%s". Line content: "%s". %s',
                    $index,
                    $expectedString,
                    $lines[$index] ?? '(line not found)',
                    $message
                )
            );
        }
    }

    /**
     * Helper method to get clean lines from the result
     *
     * @param string $result HTML result from renderPrice()
     * @return array Array of clean text lines
     */
    private function getCleanLines(string $result): array
    {
        $cleanResult = strip_tags($result);
        $lines = array_filter(explode("\n", $cleanResult), fn($line) => trim($line) !== '');

        return array_values($lines); // Re-index array
    }

    public function testRenderPriceWithProgressiveTiers(): void
    {
        // Arrange
        $thresholds = [
            $this->createThreshold('3.40', '100', 'GB', 'usd'),
            $this->createThreshold('3.00', '200', 'GB', 'usd'),
            $this->createThreshold('2.50', '400', 'GB', 'usd'),
            $this->createThreshold('2.00', '1000', 'GB', 'usd'),
            $this->createThreshold('2.00', '6000', 'GB', 'usd'),
        ];
        $price = $this->createPrice(price: '3.80', thresholds: $thresholds);

        // Act
        $result = $this->presenter->renderPrice($price);

        // Assert - Check lines in order with prices and discounts
        $this->assertLinesContain($result, [
            'First 100 GB $3.80',           // Line 0: no discount
            'Next 100 GB $3.40 (-11%)',     // Line 1: 3.80 -> 3.40 = 10.5% ≈ 11%
            'Next 200 GB $3.00 (-12%)',     // Line 2: 3.40 -> 3.00 = 11.8% ≈ 12%
            'Next 600 GB $2.50 (-17%)',     // Line 3: 3.00 -> 2.50 = 16.7% ≈ 17%
            'Next 5000 GB $2.00 (-20%)',    // Line 4: 2.50 -> 2.00 = 20%
            'Over 6000 GB $2.00',           // Line 5: last threshold, no discount (same price)
        ]);
    }

    public function testRenderPriceCalculatesRangesCorrectly(): void
    {
        // Arrange
        $thresholds = [
            $this->createThreshold('4.00', '50', 'GB', 'usd'),
            $this->createThreshold('3.00', '150', 'GB', 'usd'),
            $this->createThreshold('2.00', '500', 'GB', 'usd'),
        ];
        $price = $this->createPrice(price: '5.00', thresholds: $thresholds);

        // Act
        $result = $this->presenter->renderPrice($price);

        // Assert - Check lines in order
        $this->assertLinesContain($result, [
            'First 50 GB $5.00',         // 0 to 50 GB
            'Next 100 GB $4.00 (-20%)',  // 50 to 150 GB (100 GB range), 5.00 -> 4.00 = 20%
            'Next 350 GB $3.00 (-25%)',  // 150 to 500 GB (350 GB range), 4.00 -> 3.00 = 25%
            'Over 500 GB $2.00 (-33%)',  // Last tier, 3.00 -> 2.00 = 33.3% ≈ 33%
        ]);
    }

    public function testRenderPriceWithSingleThreshold(): void
    {
        // Arrange
        $threshold = $this->createThreshold('5.00', '100', 'GB', 'usd');
        $thresholds = [$threshold];
        $price = $this->createPrice(price: '10.00', thresholds: $thresholds);

        // Act
        $result = $this->presenter->renderPrice($price);

        // Assert - Check lines in order
        $this->assertLinesContain($result, [
            'First 100 GB $10.00',       // 0 to 100 GB
            'Over 100 GB $5.00 (-50%)',  // Last tier, 10.00 -> 5.00 = 50%
        ]);
    }

    public function testRenderPriceFallsBackToParentForStandardPrice(): void
    {
        // Arrange
        // a single threshold that is a new record (empty price)
        $threshold = $this->createMock(Threshold::class);
        $threshold->method('getIsNewRecord')->willReturn(true);
        $threshold->method('getUnitLabel')->willReturn('GB');

        $price = $this->createPrice(price: '5.00', thresholds: [$threshold]);

        // Act
        $result = $this->presenter->renderPrice($price);

        // Assert - should use a parent's renderPrice method
        $this->assertStringContainsString('$5.00', $result);
        $this->assertStringContainsString('per GB', $result);
    }

    public function testRenderPriceWithPrepaidQuantity(): void
    {
        // Arrange
        $thresholds = [
            $this->createThreshold('3.40', '100', 'GB', 'usd'),
            $this->createThreshold('3.00', '200', 'GB', 'usd'),
            $this->createThreshold('2.50', '400', 'GB', 'usd'),
            $this->createThreshold('2.00', '6000', 'GB', 'usd'),
        ];
        $price = $this->createPrice(price: '3.80', thresholds: $thresholds, quantity: '10');

        // Act
        $result = $this->presenter->renderPrice($price);

        // assert - check lines in order (no discount after prepaid line)
        $this->assertLinesContain($result, [
            'First 10 GB Included',
            'Next 90 GB $3.80',
            'Next 100 GB $3.40 (-11%)',
            'Next 200 GB $3.00 (-12%)',
            'Next 5600 GB $2.50 (-17%)',
            'Over 6000 GB $2.00 (-20%)',
        ]);
    }

    public function testRenderPriceWithPrepaidQuantityCoveringFirstThreshold(): void
    {
        // Arrange
        $thresholds = [
            $this->createThreshold('3.40', '50', 'GB', 'usd'),
            $this->createThreshold('3.00', '200', 'GB', 'usd'),
            $this->createThreshold('2.50', '400', 'GB', 'usd'),
            $this->createThreshold('2.00', '6000', 'GB', 'usd'),
        ];
        // Prepaid quantity = 100, which covers the first threshold (50)
        $price = $this->createPrice(price: '3.80', thresholds: $thresholds, quantity: '100');

        // Act
        $result = $this->presenter->renderPrice($price);

        // Assert - First threshold (50) is skipped because prepaid covers it
        $this->assertLinesContain($result, [
            'First 100 GB Included',         // Prepaid
            'Next 100 GB $3.40',             // 100 to 200 GB - no discount (first after prepaid)
            'Next 200 GB $3.00 (-12%)',      // 200 to 5600 GB
            'Next 5600 GB $2.50 (-17%)',     // from 5600 GB
            'Over 6000 GB $2.00 (-20%)',     // Last tier
        ]);

        // Verify that a skipped threshold doesn't appear
        $lines = $this->getCleanLines($result);
        foreach ($lines as $line) {
            $this->assertStringNotContainsString('Next 50 GB', $line);
        }
    }

    public function testRenderPriceWithPrepaidQuantityEqualToFirstThreshold(): void
    {
        // Arrange
        $thresholds = [
            $this->createThreshold('3.40', '100', 'GB', 'usd'),
            $this->createThreshold('3.00', '200', 'GB', 'usd'),
            $this->createThreshold('2.00', '500', 'GB', 'usd'),
        ];
        // Prepaid quantity exactly matches the first threshold
        $price = $this->createPrice(price: '3.80', thresholds: $thresholds, quantity: '100');

        // Act
        $result = $this->presenter->renderPrice($price);

        // Assert - Check lines in order
        $this->assertLinesContain($result, [
            'First 100 GB Included',
            'Next 100 GB $3.40',
            'Next 300 GB $3.00 (-12%)',
            'Over 500 GB $2.00 (-33%)',
        ]);
    }

    public function testRenderPriceWithDiscounts(): void
    {
        // Arrange
        $thresholds = [
            $this->createThreshold('3.40', '100', 'GB', 'usd'), // -11% from 3.80
            $this->createThreshold('3.00', '200', 'GB', 'usd'), // -12% from 3.40
            $this->createThreshold('2.50', '400', 'GB', 'usd'), // -17% from 3.00
            $this->createThreshold('2.00', '1000', 'GB', 'usd'), // -20% from 2.50
        ];
        $price = $this->createPrice(price: '3.80', thresholds: $thresholds);

        // Act
        $result = $this->presenter->renderPrice($price);

        // Assert - Check lines in order with correct discounts
        $this->assertLinesContain($result, [
            'First 100 GB $3.80',           // No discount
            'Next 100 GB $3.40 (-11%)',     // 3.80 -> 3.40 = 10.5% ≈ 11%
            'Next 200 GB $3.00 (-12%)',     // 3.40 -> 3.00 = 11.8% ≈ 12%
            'Next 600 GB $2.50 (-17%)',     // 3.00 -> 2.50 = 16.7% ≈ 17%
            'Over 1000 GB $2.00 (-20%)',    // 2.50 -> 2.00 = 20%
        ]);
    }

    public function testRenderPriceWithPrepaidHasNoDiscountOnFirstLine(): void
    {
        // Arrange
        $thresholds = [
            $this->createThreshold('3.40', '100', 'GB', 'usd'),
            $this->createThreshold('3.00', '200', 'GB', 'usd'),
            $this->createThreshold('2.00', '500', 'GB', 'usd'),
        ];
        $price = $this->createPrice(price: '3.80', thresholds: $thresholds, quantity: '10');

        // Act
        $result = $this->presenter->renderPrice($price);

        // Assert - Check lines in order, prepaid and first paid line have no discount
        $this->assertLinesContain($result, [
            'First 10 GB Included',         // Prepaid - no discount
            'Next 90 GB $3.80',             // First paid line - no discount
            'Next 100 GB $3.40 (-11%)',     // Has discount
            'Next 300 GB $3.00 (-12%)',     // Has discount
            'Over 500 GB $2.00 (-33%)',     // Has discount
        ]);
    }

    public function testDiscountCalculationRounding(): void
    {
        // Arrange
        $thresholds = [
            $this->createThreshold('2.75', '100', 'GB', 'usd'), // Should be -8% from 3.00 (8.33% rounds to 8)
            $this->createThreshold('2.50', '200', 'GB', 'usd'), // Should be -9% from 2.75 (9.09% rounds to 9)
        ];
        $price = $this->createPrice(price: '3.00', thresholds: $thresholds);

        // Act
        $result = $this->presenter->renderPrice($price);

        // Assert - Check rounding is correct
        $this->assertLinesContain($result, [
            'First 100 GB $3.00',           // No discount
            'Next 100 GB $2.75 (-8%)',      // 3.00 -> 2.75 = 8.33% rounds to 8
            'Over 200 GB $2.50 (-9%)',      // 2.75 -> 2.50 = 9.09% rounds to 9
        ]);
    }

    private function createThreshold(string $price, string $quantity, string $unit, string $currency): Threshold
    {
        $threshold = $this->createMock(Threshold::class);
        $threshold->price = $price;
        $threshold->quantity = $quantity;
        $threshold->unit = $unit;
        $threshold->currency = $currency;
        $threshold->method('getUnitLabel')->willReturn($unit);
        $threshold->method('getIsNewRecord')->willReturn(false);

        return $threshold;
    }

    private function createPrice(string $price, array $thresholds = [], string $quantity = '0'): RepresentablePrice
    {
        return new readonly class($price, $thresholds, $quantity) implements RepresentablePrice {
            public function __construct(
                public string $price,
                public array $thresholds,
                public string $quantity = '0',
                public string $currency = 'usd',
            )
            {
            }

            public function getUnitLabel(): string
            {
                return 'GB';
            }

            public function getFormulaLines(): array
            {
                return [];
            }

            public function getThresholds(): array
            {
                return $this->thresholds;
            }

            public function getSubtype(): string
            {
                return '';
            }

            public function isOveruse(): bool
            {
                return false;
            }

            public function isQuantityPredefined(): bool
            {
                return false;
            }
        };
    }
}
