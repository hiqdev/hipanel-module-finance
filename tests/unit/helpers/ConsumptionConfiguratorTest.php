<?php declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\helpers;

use hipanel\modules\finance\helpers\ConsumptionConfigurator;
use hipanel\modules\finance\tests\unit\TestCase;
use hiqdev\billing\registry\behavior\ConsumptionConfigurationBehavior;
use hiqdev\billing\registry\behavior\ResourceDecoratorBehavior;
use hiqdev\billing\registry\product\PriceType;
use hiqdev\billing\registry\TariffDefinitions\TariffTypeDefinitionFacade;
use hiqdev\billing\registry\tests\unit\ResourceDecorator\MockResourceDecorator;
use hiqdev\php\billing\product\Application\BillingRegistryService;
use hiqdev\php\billing\product\Application\BillingRegistryServiceInterface;
use hiqdev\php\billing\product\BillingRegistry;
use hiqdev\php\billing\product\TariffTypeDefinitionInterface;
use hiqdev\php\billing\tests\unit\product\Domain\Model\MockTariffType;

class ConsumptionConfiguratorTest extends TestCase
{
    private ConsumptionConfigurator $configurator;

    private MockTariffType $mockTariffType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->di()->set(BillingRegistryServiceInterface::class, $this->createBillingRegistryService());
        $this->configurator = $this->di()->get(ConsumptionConfigurator::class);
        $this->mockTariffType = new MockTariffType();
    }

    private function createBillingRegistryService(): BillingRegistryServiceInterface
    {
        $billingRegistry = new BillingRegistry();
        $billingRegistryService = new BillingRegistryService($billingRegistry);

        $billingRegistry->addTariffType($this->createTariffTypeDefinition());

        return $billingRegistryService;
    }

    private function createTariffTypeDefinition(): TariffTypeDefinitionInterface
    {
        $mockTariffType = new MockTariffType();

        return (new TariffTypeDefinitionFacade($mockTariffType))
            ->withPrices()
                ->overuse(PriceType::switch_license)
                    ->withBehaviors()
                        ->attach(new ResourceDecoratorBehavior(MockResourceDecorator::class))
                    ->end()
                ->end()
                ->overuse(PriceType::dregistration)
                    ->withBehaviors()
                        ->attach(new ResourceDecoratorBehavior(MockResourceDecorator::class))
                    ->end()
                ->end()
                ->overuse(PriceType::dtransfer)
                    ->withBehaviors()
                        ->attach(new ResourceDecoratorBehavior(MockResourceDecorator::class))
                    ->end()
                ->end()
            ->end()
            ->withBehaviors()
                ->attach(new ConsumptionConfigurationBehavior(
                    $mockTariffType->label(),
                    ['switch_license', 'dregistration', 'dtransfer'],
                    [['switch_license', 'dregistration']],
                ))
            ->end();
    }

    public function testGetColumns(): void
    {
        $columns = $this->configurator->getColumns($this->mockTariffType->name());

        $this->assertSame(['switch_license', 'dregistration', 'dtransfer'], $columns);
    }

//    public function testGetGroups(): void
//    {
//        $groups = $this->configurator->getGroups('test_tariff_type');
//
//        $this->assertSame([['col1', 'col2'], ['col3']], $groups);
//    }

    public function testGetGroupsWithLabels()
    {
        $groups = $this->configurator->getGroupsWithLabels($this->mockTariffType->name());
        $expected = [
            [
                'switch_license' => 'Mock Label',
                'dregistration' => 'Mock Label',
            ],
            [
                'dtransfer' => 'Mock Label',
            ],
        ];

        $this->assertSame($expected, $groups);
    }

    public function testGetFirstAvailableClass(): void
    {
        $this->assertSame($this->mockTariffType->name(), $this->configurator->getFirstAvailableClass());
    }

    public function testGetClassesDropDownOptions(): void
    {
        $options = $this->configurator->getClassesDropDownOptions();
        $this->assertSame(['switch_license' => 'Mock Label'], $options);
    }

    public function testGetAllPossibleColumns(): void
    {
        $columns = $this->configurator->getAllPossibleColumns();

        $this->assertSame(['switch_license', 'dregistration', 'dtransfer'], $columns);
    }

    public function testGetColumnsWithLabels(): void
    {
        $columns = $this->configurator->getColumnsWithLabels($this->mockTariffType->name());
        $expected = [
            'switch_license' => 'Mock Label',
            'dregistration' => 'Mock Label',
            'dtransfer' => 'Mock Label',
        ];

        $this->assertSame($expected, $columns);
    }

//    public function testGetDecorator(): void
//    {
//        $decorator = $this->configurator->getDecorator('test_tariff_type', 'col1');
//        $this->assertInstanceOf(MockResourceDecorator::class, $decorator);
//    }
}
