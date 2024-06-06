<?php declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\logic\bill;

use hipanel\modules\finance\logic\bill\QuantityFormatterFactory;
use hipanel\modules\finance\models\BillableTimeInterface;
use hipanel\modules\finance\models\HasTimeAttributeInterface;
use hipanel\modules\finance\tests\unit\TestCase;
use hiqdev\php\units\Quantity;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;

class QuantityFormatterFactoryTest extends TestCase
{
    /**
     * @dataProvider createByTypeDataProvider
     * @param string $type
     * @param Quantity $qty
     * @param $context
     * @param string $expected
     * @param string $expectedClientValue
     * @return void
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|InvalidConfigException|NotInstantiableException
     */
    public function testCreateByType(
        string $type,
        Quantity $qty,
        $context,
        string $expected,
        string $expectedClientValue
    ): void {
        $factory = $this->di()->get(QuantityFormatterFactory::class);
        $quantityFormatter = $factory->createByType($type, $qty, $context);
        $this->assertEquals($expected, $quantityFormatter->format());
        $this->assertEquals($expectedClientValue, $quantityFormatter->getClientValue());
    }

    public function createByTypeDataProvider(): iterable
    {
        $context = new \stdClass();
        $context->id = 123;

        $timeContext = new class implements HasTimeAttributeInterface
        {
            public function getTime(): ?string
            {
                return date('Y-m-d H:i:s', strtotime('2024 January'));
            }
        };

        $billableTimeContext = new class implements BillableTimeInterface
        {
            public function getQuantity()
            {
                return 2;
            }

            public function getBillQuantity(): ?int
            {
                return 1;
            }

            public function getTime(): ?string
            {
                return date('Y-m-d H:i:s', strtotime('2024 January'));
            }
        };

        yield 'other_deposit type with 1 item' => [
            'other_deposit',
            Quantity::create('items', 1),
            $context,
            $expected = '1 item',
            $expectedClientValue = '1',
        ];
        yield 'other_deposit type with 2 items' => [
            'other_deposit',
            Quantity::create('items', 2),
            $context,
            $expected = '2 items',
            $expectedClientValue = '2',
        ];
        yield 'other_deposit type with 3 unit' => [
            'other_deposit',
            Quantity::create('unit', 3),
            $context,
            $expected = '3 unit',
            $expectedClientValue = '3',
        ];
        yield 'monthly type with 6 hours' => [
            'monthly',
            Quantity::create('hour', 6),
            $timeContext,
            $expected = '6 hours',
            $expectedClientValue = '186',
        ];
        yield 'monthly type with 31 days' => [
            'monthly',
            Quantity::create('day', 1),
            $timeContext,
            $expected = '31 days',
            $expectedClientValue = '31',
        ];
        yield [
            'monthly,rack_unit',
            Quantity::create('', 2),
            $billableTimeContext,
            $expected = '2 units &times; 31 days',
            $expectedClientValue = '2',
        ];
        yield '10 IP addresses' => [
            'ip_num',
            Quantity::create('172.0.0.1', 10),
            $context,
            $expected = '10 IP',
            $expectedClientValue = '10',
        ];
        yield 'support_time type with 10 quantity' => [
            'support_time',
            Quantity::create('', 10),
            $context,
            $expected = '10:00',
            $expectedClientValue = '10',
        ];
        yield 'drenewal type with 10 quantity' => [
            'drenewal',
            Quantity::create('', 12),
            $context,
            $expected = '12 years',
            $expectedClientValue = '12',
        ];
    }
}
