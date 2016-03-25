<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\controllers;

use hipanel\base\Err;
use hipanel\modules\finance\models\Merchant;
use hiqdev\hiart\HiArtException;
use Yii;

/**
 * Class PayController.
 */
class PayController extends \hiqdev\yii2\merchant\controllers\PayController
{
    public function getMerchantModule()
    {
        return $this->module->getMerchant();
    }

    public function render($view, $params = [])
    {
        return $this->getMerchantModule()->getPayController()->render($view, $params);
    }

    public function checkNotify()
    {
        $transactionId = Yii::$app->request->get('transactionId') ?: Yii::$app->request->post('transactionId');
        $history = $this->getMerchantModule()->readHistory($transactionId);
        $data = array_merge([
            'username'      => $history['username'],
            'merchant'      => $history['merchant'],
            'transactionId' => $transactionId,
        ], $_REQUEST);
        #$data = array_merge($history, $_REQUEST);
        Yii::info(http_build_query($data), 'merchant');
        Yii::$app->get('hiresource')->disableAuth();
        try {
            $result = Merchant::perform('Pay', $data);
        } catch (HiArtException $e) {
            $result = Err::set($data, $e->getMessage());
        }
        Yii::$app->get('hiresource')->enableAuth();

        return $this->getMerchantModule()->completeHistory($result);
    }
}
