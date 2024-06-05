<?php declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\logic\bill;

use hipanel\modules\finance\logic\bill\QuantityFormatterFactory;
use hipanel\modules\finance\logic\bill\QuantityFormatterInterface;
use hipanel\modules\finance\models\Bill;
use hipanel\modules\finance\providers\BillTypesProvider;
use hipanel\modules\finance\tests\fixtures\BillFixture;
use hipanel\modules\finance\tests\unit\TestCase;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\yii2\formatters\IntlFormatter;
use hiqdev\yii\compat\yii;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use yii\base\InvalidConfigException;
use yii\di\NotInstantiableException;
use yii\helpers\ArrayHelper;

class QuantityFormatterFactoryTest extends TestCase
{
    /**
     * @dataProvider createByTypeDataProvider
     * @param string $type
     * @param Quantity $qty
     * @param string $expected
     * @return void
     * @throws ContainerExceptionInterface|NotFoundExceptionInterface|InvalidConfigException|NotInstantiableException
     */
    public function testCreateByType(string $type, Quantity $qty, string $expected): void
    {
        $factory = $this->di()->get(QuantityFormatterFactory::class);
        $context = new \stdClass();
        $context->id = 123;
        $quantityFormatter = $factory->createByType($type, $qty, $context);
        $this->assertEquals($expected, $quantityFormatter->format());
    }

    public function createByTypeDataProvider(): iterable
    {
        yield [
            'other_deposit',
            Quantity::create('items', 1),
            $expected = '1 item',
        ];
        yield [
            'other_deposit',
            Quantity::create('items', 2),
            $expected = '2 items',
        ];
        yield [
            'other_deposit',
            Quantity::create('unit', 3),
            $expected = '3 unit',
        ];
//        yield [
//            'monthly',
//            Quantity::create('hour', 6),
//            $expected = '6 hour',
//        ];
//        yield [
//            'monthly,rack_unit',
//            Quantity::create('hour', 10),
//            $expected = '10 hour',
//        ];
        yield [
            'ip_num',
            Quantity::create('1111.0001.1111.1111', 10),
            $expected = '10 IP',
        ];
        yield [
            'support_time',
            Quantity::create('1111.0001.1111.1111', 10),
            $expected = '10:00',
        ];
    }

    /*public function testCreateByType2(): void
    {
        $billTypesProvider = $this->createMock(BillTypesProvider::class);
        $intlFormatter = $this->createMock(IntlFormatter::class);
        $factory = new QuantityFormatterFactory($intlFormatter, $billTypesProvider);

        $type = 'monthly';
        $quantity = new Quantity('unit', 10);
        $context = new \stdClass();
        $context->id = '123';
        $formatter = $this->createMock(QuantityFormatterInterface::class);
        $formatter->expects($this->once())
            ->method('setContext')
            ->with($context);
        $factory->types[$type] = get_class($formatter);

        $result = $factory->createByType($type, $quantity, $context);

        $this->assertSame($formatter, $result);
    }

    public function testCreateByTypeEmptyType(): void
    {
        $billTypesProvider = $this->createMock(BillTypesProvider::class);
        $intlFormatter = $this->createMock(IntlFormatter::class);
        $factory = new QuantityFormatterFactory($intlFormatter, $billTypesProvider);

        $type = null;
        $quantity = new Quantity('unit', 10);
        $context = new \stdClass();
        $context->id = '123';
        $billTypes = [
            ['id' => '123', 'name' => 'monthly'],
            ['id' => '456', 'name' => 'yearly'],
        ];
        $billTypesProvider->expects($this->once())
            ->method('getTypes')
            ->willReturn($billTypes);

        $formatter = $this->createMock(QuantityFormatterInterface::class);
        $factory->types['monthly'] = get_class($formatter);

        $result = $factory->createByType($type, $quantity, $context);

        $this->assertSame($formatter, $result);
    }

    public function testCreateByTypeInvalidType(): void
    {
        $billTypesProvider = $this->createMock(BillTypesProvider::class);
        $intlFormatter = $this->createMock(IntlFormatter::class);
        $factory = new QuantityFormatterFactory($intlFormatter, $billTypesProvider);

        $type = 'invalid';
        $quantity = new Quantity('unit', 10);
        $context = new \stdClass();
        $context->id = '123';

        $result = $factory->createByType($type, $quantity, $context);

        $this->assertNull($result);
    }

    public function testCreateByTypeInvalidTypeWithPrefix(): void
    {
        $billTypesProvider = $this->createMock(BillTypesProvider::class);
        $intlFormatter = $this->createMock(IntlFormatter::class);
        $factory = new QuantityFormatterFactory($intlFormatter, $billTypesProvider);

        $type = 'monthly,invalid';
        $quantity = new Quantity('unit', 10);
        $context = new \stdClass();
        $context->id = '123';

        $result = $factory->createByType($type, $quantity, $context);

        $this->assertNull($result);
    }

    public function testCreateByTypeInvalidTypeWithFix(): void
    {
        $billTypesProvider = $this->createMock(BillTypesProvider::class);
        $intlFormatter = $this->createMock(IntlFormatter::class);
        $factory = new QuantityFormatterFactory($intlFormatter, $billTypesProvider);

        $type = 'invalid-type';
        $quantity = new Quantity('unit', 10);
        $context = new \stdClass();
        $context->id = '123';

        $formatter = $this->createMock(QuantityFormatterInterface::class);
        $factory->types['fixed-type'] = get_class($formatter);

        $result = $factory->createByType($type, $quantity, $context);

        $this->assertSame($formatter, $result);
    }*/
}
