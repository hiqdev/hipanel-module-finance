<?php declare(strict_types=1);
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\merchant;

use hipanel\modules\finance\models\Merchant;
use hiqdev\hiart\ResponseErrorException;
use hiqdev\yii2\merchant\Currencies;
use hiqdev\yii2\merchant\models\Currency;
use hiqdev\yii2\merchant\Module;
use Yii;

/**
 * Class CurrenciesCollection
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class CurrenciesCollection extends Currencies
{
    /** @var Currency[] */
    private array $currencies;

    public Module $module;

    public function __construct()
    {
        $this->currencies = $this->fetchCurrencies();
    }

    /**
     * @return string[]
     */
    public function getList(): array
    {
        return $this->currencies;
    }

    /**
     * @return string[]
     * @throws ResponseErrorException
     */
    private function fetchCurrencies(): array
    {
        $params = [];
        if (Yii::$app->user->getIsGuest()) {
            $params['seller'] = Yii::$app->params['user.seller'];
        } else {
            $params['client_id'] = Yii::$app->user->getId();
        }

        $result = [];
        $currencies = $this->requestCurrencies($params);
        foreach ($currencies ?? [] as $currency) {
            $result[] = new Currency(['code' => $currency['code']]);
        }

        return $result;
    }

    public function requestCurrencies($params): array
    {
        $userId = Yii::$app->user->getIsGuest() ? null : Yii::$app->user->identity->getId();

        try {
            return Yii::$app->getCache()->getOrSet([__METHOD__, $params, $userId], function () use ($params) {
                return Merchant::perform('get-possible-currencies', $params);
            }, 3600);
        } catch (ResponseErrorException $e) {
            if ($e->getResponse()->getData() === null) {
                Yii::info('No available currencies found', 'hipanel:finance');
                return [];
            }

            throw $e;
        }
    }
}
