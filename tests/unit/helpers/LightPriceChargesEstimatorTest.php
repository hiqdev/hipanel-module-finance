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

use hipanel\modules\finance\helpers\LightPriceChargesEstimator;
use hipanel\modules\finance\tests\unit\TestCase;
use ReflectionMethod;

class LightPriceChargesEstimatorTest extends TestCase
{
    private function resolveActionCurrency(LightPriceChargesEstimator $estimator, array $action): string
    {
        $method = new ReflectionMethod($estimator, 'resolveActionCurrency');
        $method->setAccessible(true);

        return $method->invoke($estimator, $action);
    }

    public function testDerivesActionCurrencyFromChargesWhenMissing(): void
    {
        $estimator = new LightPriceChargesEstimator([]);

        $action = [
            'charges' => [
                ['sum' => 1000, 'currency' => 'USD'],
                ['sum' => 500, 'currency' => 'USD'],
            ],
        ];

        $this->assertSame('USD', $this->resolveActionCurrency($estimator, $action));
    }

    public function testPreservesActionCurrencyWhenAlreadyProvided(): void
    {
        $estimator = new LightPriceChargesEstimator([]);

        $action = [
            'currency' => 'EUR',
            'charges' => [
                ['sum' => 1000, 'currency' => 'USD'],
            ],
        ];

        $this->assertSame('EUR', $this->resolveActionCurrency($estimator, $action));
    }

    public function testHandlesEmptyPeriodByDerivingCurrencyFromOtherPeriods(): void
    {
        $calculations = [
            '2024-01-01' => [
                'currency' => 'USD',
                'targets' => [],
            ],
            '2024-02-01' => [
                'currency' => 'USD',
                'targets' => [],
            ],
        ];

        $estimator = new LightPriceChargesEstimator($calculations);
        $result = $estimator->calculateForPeriods(['now']);

        $this->assertSame([], $result['Jan 2024']['targets']);
        $this->assertSame('USD', $result['Jan 2024']['currency']);
        $this->assertSame(0, $result['Jan 2024']['sum']);

        $this->assertSame([], $result['Feb 2024']['targets']);
        $this->assertSame('USD', $result['Feb 2024']['currency']);
    }
}
