<?php declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\registry;

use hipanel\modules\finance\tests\unit\TestCase;
use hiqdev\billing\registry\ResourceDecorator\ResourceDecoratorData;
use hiqdev\billing\registry\ResourceDecorator\target\CdnTraffic95RegionalResourceDecorator;
use hiqdev\billing\registry\ResourceDecorator\target\CdnTrafficRegionalResourceDecorator;

class RegionalResourceDecoratorTest extends TestCase
{
    public function testTrafficTitleContainsRegionOfPriceType(): void
    {
        $decorator = new CdnTrafficRegionalResourceDecorator($this->dataFor('cdn_traf_europe_max'));

        $this->assertSame('Traffic EUROPE', $decorator->displayTitle());
    }

    public function test95thTitleContainsRegionOfPriceType(): void
    {
        $decorator = new CdnTraffic95RegionalResourceDecorator($this->dataFor('cdn_traf95_usa_max'));

        $this->assertSame('95th USA', $decorator->displayTitle());
    }

    private function dataFor(string $priceType): ResourceDecoratorData
    {
        return new ResourceDecoratorData(null, null, null, null, $priceType, '');
    }
}
