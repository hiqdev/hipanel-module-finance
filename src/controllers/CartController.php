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
use hipanel\helpers\Url;
use hipanel\modules\client\models\Client;
use hipanel\modules\finance\cart\CartFinisher;
use hipanel\modules\finance\widgets\CartCurrencyNegotiator;
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
     * @param string $currency
     * @return \yii\web\Response
     */
    private function renderDeposit(float $amount, string $currency)
    {
        $cart = $this->module->getCart();

        $form = new DepositForm([
            'amount' => $amount,
            'currency' => $currency,
            'finishUrl' => $cart->getCurrency() === $currency
                ? Url::to(['@finance/cart/finish'])
                : Url::to(['@finance/cart/finish', 'exchangeFromCurrency' => $currency])
        ]);
        $form->validate();

        return $this->module->getMerchant()->renderDeposit($form);
    }

    public function actionSelect()
    {
        $finisher = new CartFinisher([
            'cart' => $this->module->getCart(),
            'exchangeFromCurrency' => null,
            'user' => Yii::$app->user,
        ]);
        $finisher->ensureCanBeFinished();

        return $this->render('select', [
            'negotiator' => $this->getNegotiator()
        ]);
    }

    /** @var \hipanel\modules\finance\widgets\CartCurrencyNegotiator */
    private $negotiator;
    private function getNegotiator(): CartCurrencyNegotiator
    {
        if ($this->negotiator === null) {
            $this->negotiator = new CartCurrencyNegotiator([
                'cart' => $this->module->getCart(),
                'client' => $this->getClient(),
                'merchantModule' => $this->module->getMerchant(),
            ]);
        }

        return $this->negotiator;
    }

    public function actionFull(string $currency = null)
    {
        $negotiator = $this->getNegotiator();
        $currency = $currency ?? $this->module->getCart()->getCurrency();

        return $this->renderDeposit($negotiator->getFullAmount($currency), $currency);
    }

    public function actionPartial(string $currency = null)
    {
        $negotiator = $this->getNegotiator();
        $currency = $currency ?? $this->module->getCart()->getCurrency();

        return $this->renderDeposit($negotiator->getPartialAmount($currency), $currency);
    }

    public function actionFinish(string $exchangeFromCurrency = null)
    {
        $cart = $this->module->getCart();

        $finisher = new CartFinisher([
            'cart' => $cart,
            'exchangeFromCurrency' => $exchangeFromCurrency,
            'user' => Yii::$app->user,
        ]);
        $finisher->run();

        $client = $this->getClient();

        return $this->render('finish', [
            'balance' => $client->balance,
            'currency' => $client->currency,
            'success' => $finisher->getSuccess(),
            'error' => $finisher->getError(),
            'pending' => $finisher->getPending(),
            'remarks' => (array) Yii::$app->getView()->params['remarks'],
        ]);
    }

    private function getClient(): Client
    {
         return Client::find()
            ->withPurses()
            ->where(['id' => Yii::$app->user->identity->id])
            ->one();
    }
}
