<?php declare(strict_types=1);
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\tests\unit\helpers;

use hipanel\modules\finance\helpers\PriceSort;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\tests\unit\TestCase;
use ReflectionMethod;

class PriceSortTest extends TestCase
{
    public function testRpsSortsRightAfterTheTotal95thTrafficPrice(): void
    {
        $rank = $this->byServerMainPricesRank();

        $trafMax = $this->priceWithSubtype('cdn_traf_max');
        $traf95Max = $this->priceWithSubtype('cdn_traf95_max');
        $rps95Max = $this->priceWithSubtype('cdn_rps95_max');
        $serverDu = $this->priceWithSubtype('server_du');

        $this->assertLessThan($rank($rps95Max), $rank($trafMax));
        $this->assertLessThan($rank($rps95Max), $rank($traf95Max));
        $this->assertLessThan($rank($serverDu), $rank($rps95Max));
    }

    public function testUnknownSubtypeSortsLast(): void
    {
        $rank = $this->byServerMainPricesRank();

        $rps95Max = $this->priceWithSubtype('cdn_rps95_max');
        $unknown = $this->priceWithSubtype('some_future_metric');

        $this->assertLessThan($rank($unknown), $rank($rps95Max));
        $this->assertSame(INF, $rank($unknown));
    }

    public function testRegionsOfSameMetricStayTogetherInRegionOrder(): void
    {
        $rank = $this->byServerMainPricesRank();
        $subtypes = [
            'cdn_traf95_usa_max', 'cdn_traf_usa_max', 'cdn_rps95_max', 'cdn_traf95_asia_max',
            'cdn_traf_asia_max', 'cdn_traf95_europe_max', 'cdn_traf_europe_max',
        ];

        usort($subtypes, fn(string $a, string $b) => $rank($this->priceWithSubtype($a)) <=> $rank($this->priceWithSubtype($b)));

        $this->assertSame([
            'cdn_traf_asia_max', 'cdn_traf_europe_max', 'cdn_traf_usa_max',
            'cdn_traf95_asia_max', 'cdn_traf95_europe_max', 'cdn_traf95_usa_max',
            'cdn_rps95_max',
        ], $subtypes);
    }

    public function testRegionalPricesSortBetweenTotalAndNextMetric(): void
    {
        $rank = $this->byServerMainPricesRank();

        $this->assertLessThan($rank($this->priceWithSubtype('cdn_traf_asia_max')), $rank($this->priceWithSubtype('cdn_traf_max')));
        $this->assertLessThan($rank($this->priceWithSubtype('cdn_traf95_asia_max')), $rank($this->priceWithSubtype('cdn_traf95_max')));
    }

    private function byServerMainPricesRank(): \Closure
    {
        $method = new ReflectionMethod(PriceSort::class, 'byServerMainPrices');
        $method->setAccessible(true);

        return $method->invoke(null);
    }

    private function priceWithSubtype(string $subtype): Price
    {
        $price = new Price();
        $price->type = "overuse,$subtype";

        return $price;
    }
}
