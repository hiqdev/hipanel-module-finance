<?php declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\logic\bill;

use hipanel\modules\finance\logic\bill\QuantityFormatterFactory;
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
     * @param string $expected
     * @param $context
     * @return void
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|InvalidConfigException|NotInstantiableException
     */
    public function testCreateByType(string $type, Quantity $qty, string $expected, $context): void
    {
        $factory = $this->di()->get(QuantityFormatterFactory::class);
        $quantityFormatter = $factory->createByType($type, $qty, $context);
        $this->assertEquals($expected, $quantityFormatter->format());
        $this->assertEquals($expected, $quantityFormatter->getClientValue());
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

        yield [
            'other_deposit',
            Quantity::create('items', 1),
            $expected = '1 item',
            $context,
        ];
        yield [
            'other_deposit',
            Quantity::create('items', 2),
            $expected = '2 items',
            $context,
        ];
        yield [
            'other_deposit',
            Quantity::create('unit', 3),
            $expected = '3 unit',
            $context,
        ];
        yield [
            'monthly',
            Quantity::create('hour', 6),
            $expected = '6 hours',
            $timeContext,
        ];
        yield [
            'monthly',
            Quantity::create('day', 1),
            $expected = '31 days',
            $timeContext,
        ];
//        yield [
//            'monthly,rack_unit',
//            Quantity::create('hour', 10),
//            $expected = '10 hour',
//        ];
        yield [
            'ip_num',
            Quantity::create('172.0.0.1', 10),
            $expected = '10 IP',
            $context,
        ];
        yield [
            'support_time',
            Quantity::create('172.0.0.1', 10),
            $expected = '10:00',
            $context,
        ];
    }
}
