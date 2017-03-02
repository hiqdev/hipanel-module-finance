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
        'interkassa' => 1,
        'paxum' => 1,
        'ecoin' => 1,
    ];

    public function fetchMerchants(array $params = [])
    {
        if (Yii::$app->user->getIsGuest()) {
            return []; // todo show merchants for logged out users
        }

        $params = array_merge([
            'sum' => $params['amount'] ?: 1,
            'site' => Yii::$app->request->getHostInfo(),
            'username' => Yii::$app->user->identity->username,
        ], (array) $params);

        try {
            $merchants = Merchant::perform('prepare-info', $params, ['batch' => true]);
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

    public function convertMerchant($data)
    {
        return [
            'gateway' => $data['label'],
            'data' => [
                'purse' => $data['purse'],
                'amount' => $data['sum'],
                'fee' => $data['fee'],
                'commission_fee' => $data['commission_fee'],
                'currency' => strtoupper($data['currency']),
            ],
        ];
    }
}
