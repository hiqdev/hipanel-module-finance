<?php

namespace hipanel\modules\finance\widgets;

use hipanel\helpers\ArrayHelper;
use hipanel\modules\client\models\Client;
use hipanel\modules\finance\cart\CartCalculator;
use hipanel\modules\finance\models\ExchangeRate;
use hipanel\modules\finance\models\Purse;
use hipanel\modules\finance\Module;
use hiqdev\yii2\cart\ShoppingCart;
use LogicException;
use OutOfRangeException;
use Yii;
use yii\base\Widget;

/**
 * Class CartCurrencyNegotiator is a widget that suggests
 * possible cart payment options, that may include currency converting
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
final class CartCurrencyNegotiator extends Widget
{
    /**
     * @var ShoppingCart
     */
    public $cart;

    /**
     * @var Client
     */
    public $client;

    /**
     * @var Module
     */
    public $merchantModule;
    /**
     * @var CartCalculator
     */
    private $calculator;

    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->calculator = new CartCalculator($this->cart);
    }

    public function run()
    {
        $cartCurrency = $this->cart->getCurrency();
        $this->renderCurrencyOptions($this->cart->getTotal(), $cartCurrency);

        $convertibleCurrencies = $this->convertibleCurrencies(
            ArrayHelper::getColumn($this->client->purses, 'currency'),
            $cartCurrency
        );
        foreach ($convertibleCurrencies as $rate) {
            $this->renderCurrencyOptions($this->getCartAmountInCurrency($rate->from), $rate->from);
        }
    }

    /**
     * @return string formatted cart totals
     * @throws \yii\base\InvalidConfigException
     */
    public function renderCartTotals(): string
    {
        return Yii::$app->formatter->asCurrency($this->cart->getTotal(), $this->cart->getCurrency());
    }

    /**
     * @param string $currency
     * @return float
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function getCartAmountInCurrency(string $currency): float
    {
        $cartCurrency = $this->cart->getCurrency();
        if ($cartCurrency === $currency) {
            return $this->cart->getTotal();
        }

        $convertibleCurrencies = $this->convertibleCurrencies(
            ArrayHelper::getColumn($this->client->purses, 'currency'),
            $cartCurrency
        );

        foreach ($convertibleCurrencies as $rate) {
            if ($rate->from === strtoupper($currency)) {
                return $this->cart->getTotal() / $rate->rate;
            }
        }

        throw new OutOfRangeException("No exchange rate from \"$cartCurrency\" to \"$currency\"");
    }

    public function getFullAmount(string $currency): float
    {
        $purse = $this->getClientPurseByCurrency($currency);

        if ($purse->getBudget() < 0) {
            return -$purse->getBudget() + $this->getCartAmountInCurrency($currency);
        }

        return $this->getCartAmountInCurrency($currency);
    }

    public function getPartialAmount(string $currency): float
    {
        $purse = $this->getClientPurseByCurrency($currency);

        return $this->getCartAmountInCurrency($currency) - $purse->getBudget();
    }

    public function renderBalance(): string
    {
        return $this->render('balance');
    }

    /**
     * @param string[] $clientPursesCurrencies
     * @param string $cartCurrency
     * @return ExchangeRate[]
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    private function convertibleCurrencies(array $clientPursesCurrencies, string $cartCurrency): array
    {
        /** @var ExchangeRate[] $rates */
        $rates = Yii::$app->cache->getOrSet(['exchange-rates', Yii::$app->user->id], static function () {
            return ExchangeRate::find()->select(['from', 'to', 'rate'])->all();
        }, 3600);

        $result = [];

        foreach ($clientPursesCurrencies as $currency) {
            foreach ($rates as $rate) {
                if ($rate->from === strtoupper($currency) && $rate->to === strtoupper($cartCurrency)) {
                    $result[] = $rate;
                }
            }
        }

        return $result;
    }

    public function getViewPath()
    {
        return parent::getViewPath() . DIRECTORY_SEPARATOR . 'cartPaymentOptions';
    }

    public function render($view, $params = [])
    {
        return parent::render($view, array_merge($params, [
            'cart' => $this->cart,
            'client' => $this->client,
        ]));
    }

    private function renderCurrencyOptions(float $amount, string $currency): void
    {
        $currency = strtolower($currency);

        $purse = $this->getClientPurseByCurrency($currency);
        $options = [
            'purse' => $purse,
            'amount' => $amount,
            'currency' => $currency,
        ];

        if ($purse->getBudget() >= $amount || Yii::$app->user->can('manage')) {
            echo $this->render('enough', $options);
        } elseif ($purse->getBudget() > 0) {
            echo $this->render('partial', array_merge($options, [
                'amount' => $this->getPartialAmount($currency)
            ]));
        }

        if ($purse->getBudget() < 0) {
            echo $this->render('full_payment', array_merge($options, [
                'amount' => $this->getFullAmount($currency),
                'debt' => -$purse->getBudget(),
            ]));
        } else {
            echo $this->render('full_payment', $options);
        }
    }

    private function getClientPurseByCurrency(string $currency): Purse
    {
        $purse = $this->client->getPurseByCurrency($currency);
        if ($purse !== null) {
            return $purse;
        }

        $firstPurse = $this->client->getPrimaryPurse();
        if ($firstPurse === null) {
            throw new LogicException('Primary purse was not found');
        }

        $fakePurse = clone $firstPurse;
        $fakePurse->id = null;
        $fakePurse->currency = $currency;
        $fakePurse->balance = 0;
        $fakePurse->credit = 0;

        return $fakePurse;
    }
}
