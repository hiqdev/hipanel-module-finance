<?php declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\module\ConsumptionConfiguration\Application;

use hipanel\modules\finance\module\ConsumptionConfiguration\Application\ConsumptionConfigurator;
use hipanel\modules\finance\module\ConsumptionConfiguration\Domain\Collection\ConsumptionConfiguratorDataCollection;
use hipanel\modules\finance\tests\unit\TestCase;
use hiqdev\billing\registry\TariffDefinitions\AnycastCDNTariffDefinition;
use hiqdev\php\billing\product\Application\BillingRegistryService;
use hiqdev\php\billing\product\Application\BillingRegistryServiceInterface;
use hiqdev\php\billing\product\BillingRegistry;

class AnycastCdnConsumptionConfiguratorTest extends TestCase
{
    private const CLASS_NAME = 'anycastcdn';

    private static ?BillingRegistryServiceInterface $registryService = null;

    private ConsumptionConfigurator $configurator;

    protected function setUp(): void
    {
        parent::setUp();

        // The registry guards representations globally, so the definition can be built only once per process.
        self::$registryService ??= $this->createRegistryService();
        $this->di()->set(BillingRegistryServiceInterface::class, self::$registryService); // decorators resolve titles through it
        $this->configurator = new ConsumptionConfigurator(
            self::$registryService,
            new ConsumptionConfiguratorDataCollection(self::$registryService),
        );
    }

    private function createRegistryService(): BillingRegistryServiceInterface
    {
        $registry = new BillingRegistry();
        $registry->addTariffType(AnycastCDNTariffDefinition::create());

        return new BillingRegistryService($registry);
    }

    public function testColumnsContainRegionalTrafficAndRps(): void
    {
        $columns = $this->configurator->getColumns(self::CLASS_NAME)->names();

        foreach ([
            'cdn_traf_asia_max', 'cdn_traf_europe_max', 'cdn_traf_usa_max',
            'cdn_traf95_asia_max', 'cdn_traf95_europe_max', 'cdn_traf95_usa_max',
            'cdn_rps95_max',
        ] as $type) {
            $this->assertContains($type, $columns);
        }
        $this->assertContains('cdn_traf_max', $columns, 'Total traffic stays in the view');
    }

    public function testRegionsOfOneMetricShareAChart(): void
    {
        $groups = array_map('array_keys', $this->configurator->getGroupsWithLabels(self::CLASS_NAME));

        $this->assertContains(['cdn_traf_asia_max', 'cdn_traf_europe_max', 'cdn_traf_usa_max'], $groups);
        $this->assertContains(['cdn_traf95_asia_max', 'cdn_traf95_europe_max', 'cdn_traf95_usa_max'], $groups);
    }

    public function testTotalAndRpsKeepTheirOwnChart(): void
    {
        $groups = array_map('array_keys', $this->configurator->getGroupsWithLabels(self::CLASS_NAME));

        $this->assertContains(['cdn_traf_max'], $groups);
        $this->assertContains(['cdn_rps95_max'], $groups);
    }
}
