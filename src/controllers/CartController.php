<?php

/*
 * Finance Plugin for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2014-2015, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\controllers;

use hipanel\modules\client\models\Client;
use Yii;

class CartController extends \yii\web\Controller
{
    public function renderDeposit($sum)
    {
        return $this->module->getMerchant()->renderDeposit([
            'sum'   => $sum,
            'back'  => '/finance/cart/finish',
        ]);
    }

    public function actionSelect()
    {
        $client = Client::findOne(['id' => Yii::$app->user->identity->id]);
        $cart   = $this->module->getCart();
        $total  = $cart->getTotal();
        $rest   = $client->balance + $client->credit;

        if ($rest<=0 && $total>0) {
            return $this->renderDeposit($total);
        }

        return $this->render('select', [
            'client' => $client,
            'cart'   => $cart,
            'rest'   => $rest,
        ]);
    }

    public function actionFull()
    {
        return $this->renderDeposit($this->module->getCart()->getTotal());
    }

    public function actionPartial()
    {
        $client = Client::findOne(['id' => Yii::$app->user->identity->id]);
        $cart   = $this->module->getCart();
        return $this->renderDeposit($cart->total - $client->balance - $client->credit);
    }

    public function actionFinish()
    {
        d('finish');
    }

}
