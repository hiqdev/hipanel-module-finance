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
        $this->assertStringContainsString('Next 100 GB', $result);
        $this->assertStringContainsString('$3.40', $result);
        $this->assertStringContainsString('Next 200 GB', $result);
        $this->assertStringContainsString('$3.00', $result);
        $this->assertStringContainsString('Next 600 GB', $result);
        $this->assertStringContainsString('$2.50', $result);
        $this->assertStringContainsString('Next 5000 GB', $result);
        $this->assertStringContainsString('$2.00', $result);
        $this->assertStringContainsString('Over 6000 GB', $result);
        $this->assertStringContainsString('$2.00', $result);
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
