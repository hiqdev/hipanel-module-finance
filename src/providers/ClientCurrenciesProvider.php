<?php

declare(strict_types=1);

/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\providers;

use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\models\Purse;
use yii\caching\CacheInterface;

readonly class ClientCurrenciesProvider
{
    public function __construct(private CacheInterface $cache)
    {
    }

    /**
     * Currencies the client already has a purse in, cached for an hour.
     * @return string[]
     */
    public function get(int|string $clientId): array
    {
        return $this->cache->getOrSet($this->getKey($clientId), function () use ($clientId): array {
            $purses = Purse::find()->where(['client_id' => $clientId])->all();

            return ArrayHelper::getColumn($purses, 'currency');
        }, 3600);
    }

    private function getKey(int|string $clientId): string
    {
        return 'clientCurrencies' . $clientId;
    }

    public function invalidate(int|string $clientId): void
    {
        $this->cache->delete($this->getKey($clientId));
    }
}
