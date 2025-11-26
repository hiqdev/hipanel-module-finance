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

        // Assert
        $this->assertStringContainsString('First 100 GB', $result);
        $this->assertStringContainsString('$3.80', $result);

        // Check prices are present
        $this->assertStringContainsString('Next 100 GB', $result);
        $this->assertStringContainsString('$3.40', $result);
        $this->assertStringContainsString('Next 200 GB', $result);
        $this->assertStringContainsString('$3.00', $result);
        $this->assertStringContainsString('Next 600 GB', $result);
        $this->assertStringContainsString('$2.50', $result);
        $this->assertStringContainsString('Next 5000 GB', $result);
        $this->assertStringContainsString('$2.00', $result);
        $this->assertStringContainsString('Over 6000 GB', $result);

        // Check discounts are present (but not on the first line)
        $this->assertStringNotContainsString('First 100 GB $3.80 (-', $result); // No discount on first
        $this->assertStringContainsString('(-', $result); // Has discounts in output
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

        // Assert
        // First tier: 0 to 50 GB
        $this->assertStringContainsString('First 50 GB', $result);
        $this->assertStringContainsString('$5.00', $result);

        // Second tier: 50 to 150 GB (100 GB range)
        $this->assertStringContainsString('Next 100 GB', $result);
        $this->assertStringContainsString('$4.00', $result);

        // Third tier: 150 to 500 GB (350 GB range)
        $this->assertStringContainsString('Next 350 GB', $result);
        $this->assertStringContainsString('$3.00', $result);

        // Last tier
        $this->assertStringContainsString('Over 500 GB', $result);
        $this->assertStringContainsString('$3.00', $result);
    }

    public function testRenderPriceWithSingleThreshold(): void
    {
        // Arrange
        $threshold = $this->createThreshold('5.00', '100', 'GB', 'usd');
        $thresholds = [$threshold];
        $price = $this->createPrice(price: '10.00', thresholds: $thresholds);

        // Act
        $result = $this->presenter->renderPrice($price);

        // Assert
        $this->assertStringContainsString('First 100 GB', $result);
        $this->assertStringContainsString('$10.00', $result);
        $this->assertStringContainsString('Over 100 GB', $result);
        $this->assertStringContainsString('$10.00', $result);
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

        // Assert
        $this->assertStringContainsString('First 10 GB Included', strip_tags($result));
        $this->assertStringNotContainsString('First 10 GB $', $result); // Should not have a price for prepaid
        $this->assertStringContainsString('Next 90 GB', $result); // 100 - 10 = 90
        $this->assertStringContainsString('$3.40', $result);
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

        // Assert
        $this->assertStringContainsString('First 100 GB Included', strip_tags($result));
        $this->assertStringContainsString('Next 100 GB', $result); // 200 - 100 = 100
        $this->assertStringContainsString('$3.00', $result);
        $this->assertStringContainsString('Next 200 GB', $result); // 400 - 200 = 200
        $this->assertStringContainsString('$2.50', $result);
        // Should NOT contain the first threshold (50 GB) since it's covered by prepaid
        $this->assertStringNotContainsString('Next 50 GB', $result);
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

        // Assert
        $this->assertStringContainsString('First 100 GB Included', strip_tags($result));
        // The next tier should start from 100 to 200
        $this->assertStringContainsString('Next 100 GB', $result); // 200 - 100
        $this->assertStringContainsString('$3.00', $result);
        $this->assertStringContainsString('Over 500 GB', $result);
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

        // Assert - First line has no discount
        $lines = explode("\n", strip_tags($result));
        $this->assertStringNotContainsString('(-', $lines[0] ?? '');

        // Assert - Check that discounts are present in subsequent lines
        $this->assertStringContainsString('(-11%)', $result); // 3.80 -> 3.40
        $this->assertStringContainsString('(-12%)', $result); // 3.40 -> 3.00
        $this->assertStringContainsString('(-17%)', $result); // 3.00 -> 2.50
        $this->assertStringContainsString('(-20%)', $result); // 2.50 -> 2.00
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

        // Assert - Prepaid line should not have a discount
        $this->assertStringContainsString('First 10 GB Included', strip_tags($result));
        $this->assertStringNotContainsString('First 10 GB Included (-', strip_tags($result));

        // Assert - First paid line (Next 90 GB) should not have discount
        // because it's the first line with price after prepaid
        $lines = explode('<br />', $result);
        $secondLine = $lines[1] ?? '';
        $this->assertStringContainsString('Next 90 GB', $secondLine);
        $this->assertStringNotContainsString('(-', strip_tags($secondLine));
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

        // Assert
        $this->assertStringContainsString('(-8%)', $result); // 3.00 -> 2.75 = 8.33% rounds to 8
        $this->assertStringContainsString('(-9%)', $result); // 2.75 -> 2.50 = 9.09% rounds to 9
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
                // TODO: Implement getSubtype() method.
            }

            public function isOveruse(): bool
            {
                // TODO: Implement isOveruse() method.
            }

            public function isQuantityPredefined(): bool
            {
                // TODO: Implement isQuantityPredefined() method.
            }
        };
    }
}
