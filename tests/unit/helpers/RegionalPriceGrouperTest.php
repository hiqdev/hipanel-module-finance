<?php declare(strict_types=1);

namespace hipanel\modules\finance\tests\unit\helpers;

use hipanel\modules\finance\helpers\RegionalPriceGrouper;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\RegionalPrice;
use hipanel\modules\finance\tests\unit\TestCase;

class RegionalPriceGrouperTest extends TestCase
{
    public function testRegionsOfOneMetricAndKindFormOneRow(): void
    {
        $prices = [
            $this->price(1, 'overuse,cdn_traf_asia_max'),
            $this->price(2, 'overuse,cdn_traf_europe_max'),
            $this->price(3, 'overuse,cdn_traf_usa_max'),
        ];

        $rows = array_values(RegionalPriceGrouper::group($prices));

        $this->assertCount(1, $rows);
        $this->assertInstanceOf(RegionalPrice::class, $rows[0]);
        $this->assertSame('overuse,cdn_traf_max', $rows[0]->type);
        $this->assertSame([1, 2, 3], $rows[0]->memberIds());
        $this->assertSame(['asia', 'europe', 'usa'], array_keys($rows[0]->members));
    }

    public function testMonthlyAndOveruseAndMetricsAreGroupedSeparately(): void
    {
        $prices = [
            $this->price(1, 'monthly,cdn_traf_asia_max'),
            $this->price(2, 'overuse,cdn_traf_asia_max'),
            $this->price(3, 'overuse,cdn_traf95_asia_max'),
        ];

        $this->assertCount(3, RegionalPriceGrouper::group($prices));
    }

    public function testTotalAndOtherPricesStayAsTheyAreInTheirPlace(): void
    {
        $total = $this->price(1, 'overuse,cdn_traf_max');
        $regional = $this->price(2, 'overuse,cdn_traf_asia_max');
        $rps = $this->price(3, 'overuse,cdn_rps95_max');
        $hardware = $this->price(4, 'monthly,hardware');

        $rows = array_values(RegionalPriceGrouper::group([$total, $regional, $rps, $hardware]));

        $this->assertSame($total, $rows[0]);
        $this->assertInstanceOf(RegionalPrice::class, $rows[1]);
        $this->assertSame($rps, $rows[2]);
        $this->assertSame($hardware, $rows[3]);
    }

    public function testRegionalPricesOfDifferentObjectsAreNotMerged(): void
    {
        $prices = [
            $this->price(1, 'overuse,cdn_traf_asia_max', 10),
            $this->price(2, 'overuse,cdn_traf_asia_max', 20),
        ];

        $this->assertCount(2, RegionalPriceGrouper::group($prices));
    }

    private function price(int $id, string $type, int $objectId = 10): Price
    {
        $price = new Price();
        $price->id = $id;
        $price->type = $type;
        $price->object_id = $objectId;

        return $price;
    }
}
