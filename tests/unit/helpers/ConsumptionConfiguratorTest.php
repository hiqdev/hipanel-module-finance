<?php declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\helpers;

use hipanel\modules\finance\helpers\ConsumptionConfigurator;
use hipanel\modules\finance\models\Target;
use hipanel\modules\finance\tests\unit\TestCase;
use hiqdev\billing\registry\behavior\ConsumptionConfigurationBehaviour;
use hiqdev\billing\registry\TariffDefinitions\TariffTypeDefinitionFacade;
use hiqdev\php\billing\product\BillingRegistry;
use hiqdev\php\billing\product\BillingRegistryInterface;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;

class ConsumptionConfiguratorTest extends TestCase
{
    private ConsumptionConfigurator $configurator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->di()->set(BillingRegistryInterface::class, self::createBillingRegistry());
        $this->configurator = $this->di()->get(ConsumptionConfigurator::class);
    }

    private static function createBillingRegistry(): BillingRegistryInterface
    {
        $billingRegistry = new BillingRegistry();

        $billingRegistry->addTariffType(self::createTariffTypeDefinition());

        return $billingRegistry;
    }

    private static function createTariffTypeDefinition(): TariffTypeDefinitionInterface
    {
        return (new TariffTypeDefinitionFacade(new MockTariffType()))
            ->withBehaviors()
                ->attach(new ConsumptionConfigurationBehaviour(
                    'Test Tariff Type',
                    ['col1', 'col2', 'col3'],
                    [['col1', 'col2']],
                    Target::class,
                    MockResource::class,
                ))
            ->end();
    }

    public function testGetColumns(): void
    {
        $columns = $this->configurator->getColumns('test_tariff_type');

        $this->assertSame(['col1', 'col2', 'col3'], $columns);
    }

//    public function testGetGroups(): void
//    {
//        $groups = $this->configurator->getGroups('test_tariff_type');
//
//        $this->assertSame([['col1', 'col2'], ['col3']], $groups);
//    }

    public function testGetGroupsWithLabels()
    {
        $groups = $this->configurator->getGroupsWithLabels('test_tariff_type');
        $expected = [
            [
                'col1' => 'Mock',
                'col2' => 'Mock',
            ],
            [
                'col3' => 'Mock',
            ],
        ];

        $this->assertSame($expected, $groups);
    }

    public function testGetFirstAvailableClass(): void
    {
        $this->assertSame('test_tariff_type', $this->configurator->getFirstAvailableClass());
    }

    public function testGetClassesDropDownOptions(): void
    {
        $options = $this->configurator->getClassesDropDownOptions();
        $this->assertSame(['test_tariff_type' => 'Test Tariff Type'], $options);
    }

    public function testGetAllPossibleColumns(): void
    {
        $columns = $this->configurator->getAllPossibleColumns();

        $this->assertSame(['col1', 'col2', 'col3'], $columns);
    }

    public function testGetColumnsWithLabels(): void
    {
        $columns = $this->configurator->getColumnsWithLabels('test_tariff_type');
        $expected = [
            'col1' => 'Mock',
            'col2' => 'Mock',
            'col3' => 'Mock',
        ];

        $this->assertSame($expected, $columns);
    }

//    public function testGetDecorator(): void
//    {
//        $decorator = $this->configurator->getDecorator('test_tariff_type', 'col1');
//        $this->assertInstanceOf(MockResourceDecorator::class, $decorator);
//    }
}
