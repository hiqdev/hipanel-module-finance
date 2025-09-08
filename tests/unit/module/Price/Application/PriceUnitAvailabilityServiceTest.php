<?php

declare(strict_types=1);

namespace unit\module\Price\Application;

use hipanel\modules\finance\module\Price\Infrastructure\Persistence\RefUnitRepository;
use hipanel\modules\finance\tests\unit\TestCase;
use hipanel\modules\finance\module\Price\Application\PriceUnitAvailabilityService;
use hipanel\modules\finance\module\Price\Domain\Collection\UnitCollection;
use hipanel\modules\finance\module\Price\Domain\Model\Unit;
use hiqdev\billing\registry\Application\UnitService;
use hiqdev\billing\registry\Domain\Model\TariffType;
use hiqdev\billing\registry\Domain\Model\Unit\Unit as BillingUnit;
use hiqdev\billing\registry\product\PriceType;
use hiqdev\billing\registry\TariffDefinitions\TariffTypeDefinitionFacade;
use hiqdev\php\billing\product\Application\BillingRegistryService;
use hiqdev\php\billing\product\Application\BillingRegistryServiceInterface;
use hiqdev\php\billing\product\BillingRegistry;

class PriceUnitAvailabilityServiceTest extends TestCase
{
    private RefUnitRepository $unitRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->unitRepository = $this->createMock(RefUnitRepository::class);
    }

    /**
     * @dataProvider priceTypeProvider
     */
    public function testReturnsUnitsForKnownPriceType(
        UnitCollection $collection,
        string $priceType,
        string $defaultUnitCode,
        array $expected,
    ): void {
        $this->unitRepository->method('findAll')->willReturn($collection);

        $service = $this->createService();
        $result = $service->getAvailableUnitsForPrice($priceType, $defaultUnitCode);

        $this->assertEquals($expected, $result->toArray());
    }

    private function createService(): PriceUnitAvailabilityService
    {
        return new PriceUnitAvailabilityService(
            $this->createBillingRegistryService(),
            $this->unitRepository,
            $this->di()->get(UnitService::class),
        );
    }

    private function createBillingRegistryService(): BillingRegistryServiceInterface
    {
        $registry = new BillingRegistry();
        $registryService = new BillingRegistryService($registry);

        $tariffTypeDefinition = new TariffTypeDefinitionFacade(TariffType::server);
        $tariffTypeDefinition
            ->withPrices()
                ->overuse(PriceType::power)
                    ->unit(BillingUnit::w)
                ->end()
            ->end();

        $registry->addTariffType($tariffTypeDefinition);

        return $registryService;
    }

    public static function priceTypeProvider(): \Generator
    {
        yield [
            'collection' => new UnitCollection([
                new Unit('w', 'Watt'),
                new Unit('kw', 'Kilowatt'),
                new Unit('gb', 'Gigabyte'),
            ]),
            'priceType' => 'overuse,power',
            'defaultUnitCode' => 'w',
            'expected' => ['w' => 'Watt', 'kw' => 'Kilowatt'],
        ];

        yield 'ReturnsDefaultUnitForUnknownPriceType' => [
            'collection' => new UnitCollection([
                new Unit('w', 'Watt'),
                new Unit('kw', 'Kilowatt'),
            ]),
            'priceType' => 'nonexistent',
            'defaultUnitCode' => 'kw',
            'expected' => ['kw' => 'Kilowatt'],
        ];

        yield 'ReturnsEmptyCollectionWhenDefaultCodeIsEmpty' => [
            'collection' => new UnitCollection([
                new Unit('w', 'Watt'),
            ]),
            'priceType' => 'nonexistent',
            'defaultUnitCode' => '',
            'expected' => [],
        ];

        yield 'ReturnsEmptyCollectionWhenNoUnitsMatchFraction' => [
            'collection' => new UnitCollection([
                new Unit('gb', 'Gigabyte'),
            ]),
            'priceType' => 'overuse,power',
            'defaultUnitCode' => 'w',
            'expected' => [],
        ];
    }
}
