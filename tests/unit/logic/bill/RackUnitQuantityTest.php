<?php declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\logic\bill;

use hipanel\modules\finance\logic\bill\RackUnitQuantity;
use hipanel\modules\finance\models\BillableTimeInterface;
use hipanel\modules\finance\tests\unit\TestCase;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\yii2\formatters\IntlFormatter;
use OutOfBoundsException;

class RackUnitQuantityTest extends TestCase
{
    private IntlFormatter $intlFormatter;

    protected function setUp(): void
    {
        $this->intlFormatter = $this->di()->get(IntlFormatter::class);
    }

    /**
     * @dataProvider rackUnitQuantityDataProvider
     */
    public function testFormat(
        string $time,
        float $quantity,
        ?int $billQuantity,
        string $expectedFormat,
        string $expectedClientValue
    ): void {
        $context = $this->createContext($time, $quantity, $billQuantity);
        $qty = Quantity::create('unit', $quantity);

        $formatter = new RackUnitQuantity($qty, $this->intlFormatter);
        $formatter->setContext($context);

        $this->assertEquals($expectedFormat, $formatter->format());
        $this->assertEquals($expectedClientValue, $formatter->getClientValue());
    }

    public function rackUnitQuantityDataProvider(): array
    {
        return [
            'basic scenario' => [
                'time' => '2024-01-15',
                'quantity' => 2.0,
                'billQuantity' => 1,
                'expectedFormat' => '2 units &times; 31 days',
                'expectedClientValue' => '2',
            ],
            'zero billing quantity' => [
                'time' => '2024-02-15',
                'quantity' => 2.0,
                'billQuantity' => 0,
                'expectedFormat' => '2 units &times; 0 days',
                'expectedClientValue' => '2',
            ],
            'different months' => [
                'time' => '2024-02-15',
                'quantity' => 3.0,
                'billQuantity' => 1,
                'expectedFormat' => '3 units &times; 29 days',
                'expectedClientValue' => '3',
            ],
        ];
    }

    private function createContext(string $time, float $quantity, ?int $billQuantity): BillableTimeInterface
    {
        return new class($time, $quantity, $billQuantity) implements BillableTimeInterface
        {
            private $time;
            private $quantity;
            private $billQuantity;

            public function __construct(string $time, float $quantity, ?int $billQuantity)
            {
                $this->time = $time;
                $this->quantity = $quantity;
                $this->billQuantity = $billQuantity;
            }

            public function getQuantity(): float
            {
                return $this->quantity;
            }

            public function getBillQuantity(): ?int
            {
                return $this->billQuantity;
            }

            public function getTime(): ?string
            {
                return $this->time;
            }
        };
    }
}