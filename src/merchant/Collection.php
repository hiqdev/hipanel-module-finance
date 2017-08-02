<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\merchant;

use hipanel\modules\finance\models\Merchant;
use hiqdev\hiart\ResponseErrorException;
use Yii;
use yii\helpers\ArrayHelper;

class Collection extends \hiqdev\yii2\merchant\Collection
{
    public function init()
    {
        parent::init();
        $this->loadMerchants($this->params);
    }

    public function loadMerchants(array $params = null)
    {
        $this->addItems($this->fetchMerchants($params));
    }

    public static $supportedSystems = [
        'webmoney' => 1,
        'paypal' => 1,
        'yandex' => 1,
        'interkassa' => 1,
        'paxum' => 1,
        'ecoin' => 1,
        'okpay' => 1,
        'robokassa' => 1,
        'freekassa' => 1,
        'bitpay' => 1
    ];

    public function fetchMerchants(array $params = [])
    {
        $defaults = [
            'sum' => $params['amount'] ?: 1,
            'site' => Yii::$app->request->getHostInfo(),
        ];

        if (Yii::$app->user->getIsGuest()) {
            $defaults['seller'] = Yii::$app->params['user.seller'];
        } else {
            $defaults['username'] = Yii::$app->user->identity->username;
        }

        $params = array_merge($defaults, (array)$params);

        try {
            $merchants = $this->requestMerchants($params);
        } catch (ResponseErrorException $e) {
            if ($e->getResponse()->getData() === null) {
                Yii::info('No available payment methods found', 'hipanel:finance');
                $merchants = [];
            } else {
                throw $e;
            }
        }

        $result = [];
        foreach (array_keys(static::$supportedSystems) as $system) {
            foreach ($merchants as $name => $merchant) {
                if ($merchant['system'] === $system) {
                    $result[$name] = $this->convertMerchant($merchant);
                }
            }
        }

        return $result;
    }

    public function requestMerchants($params)
    {
        return Yii::$app->getCache()->getOrSet([__METHOD__, $params], function () use ($params) {
            return Merchant::perform('prepare-info', $params, ['batch' => true]);
        }, 3600*24);
    }

    public function convertMerchant($data)
    {
        $data['currency'] = strtoupper($data['currency']);
        $data['amount']   = $data['sum'];

        return [
            'gateway' => $data['system'],
            'label' => $data['label'],
            'data' => $data,
        ];
    }
}
