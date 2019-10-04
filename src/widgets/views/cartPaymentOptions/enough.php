<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var \yii\web\View $this
 * @var \hiqdev\yii2\cart\ShoppingCart $cart
 * @var \hipanel\modules\finance\models\Purse $purse
 * @var float $amount
 * @var string $currency
 */

if ($cart->getCurrency() === $currency) {
    $link = Url::to('@finance/cart/finish');
} else {
    $link = Url::to(['@finance/cart/finish', 'exchangeFromCurrency' => $currency]);
}

?>

<p>
    <?= Html::a(Yii::t('hipanel:finance', 'Pay {amount} from your balance', [
        'amount' => Yii::$app->formatter->asCurrency($amount, $currency)
    ]), $link, [
        'class' => 'btn btn-lg btn-primary btn-block lock-on-click',
        'data-loading-text' => Yii::t('hipanel:finance', 'Processing your cart...'),
    ]) ?>
</p>
