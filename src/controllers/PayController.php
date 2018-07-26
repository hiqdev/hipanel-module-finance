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
use hiqdev\hiart\ResponseErrorException;
use hiqdev\yii2\merchant\actions\RequestAction;
use hiqdev\yii2\merchant\events\TransactionInsertEvent;
use hiqdev\yii2\merchant\transactions\Transaction;
use Yii;
use yii\base\InvalidParamException;
use yii\helpers\Json;

/**
 * Class PayController.
 * @property \hipanel\modules\finance\Module $module
 */
class PayController extends \hiqdev\yii2\merchant\controllers\PayController
{
    const SESSION_MERCHANT_LATEST_TRANSACTION_ID = 'MERCHANT_LATEST_TRANSACTION_ID';

    public function actions()
    {
        return array_merge(parent::actions(), [
            'request' => [
                'class' => RequestAction::class,
                'on ' . RequestAction::EVENT_AFTER_TRANSACTION_INSERT => function (TransactionInsertEvent $event) {
                    if ($event->transaction instanceof Transaction) {
                        Yii::$app->session->set(self::SESSION_MERCHANT_LATEST_TRANSACTION_ID, $event->transaction->getId());
                    }
                }
            ]
        ]);
    }

    public function getMerchantModule()
    {
        return $this->module->getMerchant();
    }

    public function render($view, $params = [])
    {
        return $this->getMerchantModule()->getPayController()->render($view, $params);
    }

    public function checkNotify(string $transactionId = null): ?Transaction
    {
        $id = $transactionId
            ?? Yii::$app->request->get('transactionId')
            ?? Yii::$app->request->post('transactionId')
            ?? Yii::$app->session->get(self::SESSION_MERCHANT_LATEST_TRANSACTION_ID);

        $transaction = $this->getMerchantModule()->findTransaction($id);
        if ($transaction === null) {
            return null;
        }

        $data = array_merge([
            'transactionId' => $transaction->getId(),
            'merchant' => $transaction->getMerchant(),
            'username' => $transaction->getParameter('username'),
        ], $_REQUEST);
        Yii::info(http_build_query($data), 'merchant');

        if (($input = file_get_contents('php://input')) !== null) {
            try {
                $data['rawBody'] = Json::decode($input);
            } catch (InvalidParamException $e) {}
        }

        try {
            return Yii::$app->get('hiart')->callWithDisabledAuth(function () use ($transaction, $data) {
                $result = Merchant::perform('pay', $data);

                $this->completeTransaction($transaction, $result);
                return $transaction;
            });
        } catch (ResponseErrorException $e) {
            // Does not matter
            return $transaction;
        }
    }

    public function actionProxyNotification()
    {
        // Currently used at least for FreeKassa integration
        $data = array_merge(Yii::$app->request->get(), Yii::$app->request->post());

        $result = Yii::$app->get('hiart')->callWithDisabledAuth(function () use ($data) {
            return Merchant::perform('pay-transaction', $data);
        });

        $this->layout = false;
        return $result;
    }

    /**
     * @param Transaction $transaction
     * @param $response
     * @return mixed
     */
    protected function completeTransaction($transaction, $response)
    {
        if ($transaction->isCompleted() || isset($data['_error'])) {
            return $transaction;
        }

        if ($response === '"OK"') {
            echo $response;
            Yii::$app->end();
        }

        $transaction->complete();
        $transaction->addParameter('bill_id', $response['id']);

        $this->getMerchantModule()->saveTransaction($transaction);

        return $transaction;
    }
}
