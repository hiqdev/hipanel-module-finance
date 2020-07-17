<?php


namespace hipanel\modules\finance\providers;


use hiqdev\yii2\cart\PaymentMethodsProviderInterface;
use Yii;

/**
 * Class PaymentMethodsProvider
 * @package hipanel\modules\finance\providers
 */
class PaymentMethodsProvider implements PaymentMethodsProviderInterface
{
    /**
     * @inheritDoc
     */
    public function getPaymentMethods()
    {
        $merchants = Yii::$app->getModule('merchant')->getPurchaseRequestCollection(
            new \hiqdev\yii2\merchant\models\DepositRequest(['amount' => 5])
        )->getItems();

        return Yii::$app->getView()->renderFile(dirname(__DIR__) . '/views/cart/payment-methods.php', [
            'merchants' => $merchants,
        ]);
    }
}
