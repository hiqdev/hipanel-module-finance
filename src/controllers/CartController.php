<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\controllers;

use hipanel\filters\EasyAccessControl;
use hipanel\modules\client\models\Client;
use hipanel\modules\finance\cart\CartFinisher;
use hipanel\modules\finance\Module;
use hiqdev\yii2\merchant\models\DepositForm;
use Yii;

class CartController extends \yii\web\Controller
{
    /**
     * @var Module
     */
    public $module;

    public function behaviors()
    {
        return [
            [
                'class' => EasyAccessControl::class,
                'actions' => [
                    'deposit' => '@',
                    'select' => '@',
                    'partial' => '@',
                    'full' => '@',
                    'finish' => '@',
                ],
            ],
        ];
    }

    /**
     * @param float $amount
     * @return \yii\web\Response
     */
    public function renderDeposit($amount)
    {
        $form = new DepositForm(['amount' => $amount]);
        $form->finishUrl = '/finance/cart/finish';

        return $this->module->getMerchant()->renderDeposit($form);
    }

    public function actionSelect()
    {
        $client = Client::findOne(['id' => Yii::$app->user->identity->id]);
        $cart = $this->module->getCart();
        $total = $cart->getTotal();
        $budget = $client->balance + $client->credit;

        if ($budget <= 0 && $total > 0) {
            return $this->renderDeposit($total);
        }

        return $this->render('select', [
            'cart' => $cart,
            'client' => $client,
            'budget' => $budget,
        ]);
    }

    public function actionFull()
    {
        return $this->renderDeposit($this->module->getCart()->getTotal());
    }

    public function actionPartial()
    {
        $client = Client::findOne(['id' => Yii::$app->user->identity->id]);
        $cart = $this->module->getCart();

        return $this->renderDeposit($cart->total - $client->balance - $client->credit);
    }

    public function actionFinish()
    {
        $cart = $this->module->getCart();

        $finisher = new CartFinisher(['cart' => $cart]);
        $finisher->run();

        $client = Client::findOne(['id' => Yii::$app->user->identity->id]);

        return $this->render('finish', [
            'balance' => $client->balance,
            'success' => $finisher->getSuccess(),
            'error' => $finisher->getError(),
            'pending' => $finisher->getPending(),
            'remarks' => (array) Yii::$app->getView()->params['remarks'],
        ]);
    }
}
