<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

declare(strict_types=1);

namespace hipanel\modules\finance\providers;

use Yii;

class BillPreloadStorage
{
    private const string SESSION_PREFIX = 'bill_preload_';

    public function store(array $billsData): string
    {
        $preloadKey = Yii::$app->security->generateRandomString(16);
        Yii::$app->session->set(self::SESSION_PREFIX . $preloadKey, $billsData);

        return $preloadKey;
    }

    public function take(string $preloadKey): ?array
    {
        $sessionKey = self::SESSION_PREFIX . $preloadKey;
        $billsData = Yii::$app->session->get($sessionKey);
        if ($billsData !== null) {
            Yii::$app->session->remove($sessionKey);
        }

        return is_array($billsData) ? $billsData : null;
    }
}
