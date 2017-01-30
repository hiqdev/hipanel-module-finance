<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\controllers;

use hipanel\modules\finance\models\Merchant;
use Yii;

/**
 * Class PayController.
 * @property \hipanel\modules\finance\Module $module
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
        Yii::info(http_build_query($data), 'merchant');

        Yii::$app->get('hiart')->disableAuth();
        try {
            $result = Merchant::perform('pay', $data);
        } catch (\hiqdev\hiart\Exception $e) {
            $result['_error'] = $e->getMessage();
        }
        Yii::$app->get('hiart')->enableAuth();

        return $this->getMerchantModule()->completeHistory(array_merge(compact('transactionId'), $result));
    }
}
