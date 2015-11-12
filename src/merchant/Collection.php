<?php

namespace hipanel\modules\finance\merchant;

use Yii;
use hipanel\modules\finance\models\Merchant;

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

    public function fetchMerchants(array $params = [])
    {
        $params = array_merge([
            'sum'      => $params['amount'] ?: 1,
            'site'     => Yii::$app->request->getHostInfo(),
            'username' => Yii::$app->user->identity->username,
        ], (array)$params);
        $ms = Merchant::perform('sPrepareInfo', $params);
        $merchants = [];
        foreach ($ms as $name => $m) {
            if ($m['system'] == 'wmdirect') {
                continue;
            }
            $merchants[$name] = $this->convertMerchant($m);
        }

        return $merchants;
    }

    public function convertMerchant($data)
    {
        return [
            'gateway'   => $data['label'],
            'data'      => [
                'purse'     => $data['purse'],
                'amount'    => $data['sum'],
                'fee'       => $data['fee'],
                'currency'  => strtoupper($data['currency']),
            ],
        ];
    }
}
