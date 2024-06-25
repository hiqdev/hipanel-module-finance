<?php declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\logic\bill;

use hipanel\modules\finance\logic\bill\MoneyQuantity;
use hipanel\modules\finance\tests\unit\TestCase;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\yii2\formatters\IntlFormatter;

class MoneyQuantityTest extends TestCase
{
    private IntlFormatter $intlFormatter;

    protected function setUp(): void
    {
        $this->intlFormatter = $this->di()->get(IntlFormatter::class);
    }

    /**
     * @dataProvider moneyQuantityDataProvider
     */
    public function testFormat(float $quantity, string $currency, string $expectedFormat): void
    {
        $qty = Quantity::create($currency, $quantity);

        $moneyQuantity = new MoneyQuantity($qty, $this->intlFormatter);

        $this->assertEquals($expectedFormat, $moneyQuantity->format());
    }

    /**
     * @dataProvider moneyQuantityDataProvider
     */
    public function testGetAmount(
        float $quantity,
        string $currency,
        string $expectedFormat,
        string $expectedAmount
    ): void {
        $qty = Quantity::create($currency, $quantity);
        $moneyQuantity = new MoneyQuantity($qty, $this->intlFormatter);

        $amount = $moneyQuantity->getAmount();
        $this->assertEquals($expectedAmount, $amount->getAmount());
        $this->assertEquals($currency, $amount->getCurrency()->getCode());
    }

    /**
     * @dataProvider moneyQuantityDataProvider
     */
    public function testGetCurrency(
        float $quantity,
        string $currency,
        string $expectedFormat,
        string $expectedAmount,
        string $expectedCurrency
    ): void {
        $qty = Quantity::create($currency, $quantity);
        $moneyQuantity = new MoneyQuantity($qty, $this->intlFormatter);

        $this->assertEquals($expectedCurrency, $moneyQuantity->getCurrency());
    }

    public function moneyQuantityDataProvider(): array
    {
        return [
            'basic scenario' => [
                'quantity' => 1000.5,
                'currency' => 'USD',
                'expectedFormat' => '$1,000.50', // Assuming default Yii currency formatter
                'expectedAmount' => '100050',
                'expectedCurrency' => 'USD',
            ],
            'zero quantity' => [
                'quantity' => 0.0,
                'currency' => 'EUR',
                'expectedFormat' => '€0.00',
                'expectedAmount' => '0',
                'expectedCurrency' => 'EUR',
            ],
            'negative quantity' => [
                'quantity' => -550.5,
                'currency' => 'GBP',
                'expectedFormat' => '-£550.50',
                'expectedAmount' => '-55050',
                'expectedCurrency' => 'GBP',
            ],
            'fractional quantity' => [
                'quantity' => 275.75,
                'currency' => 'USD',
                'expectedFormat' => '$275.75',
                'expectedAmount' => '27575',
                'expectedCurrency' => 'USD',
            ],
        ];
    }
}
