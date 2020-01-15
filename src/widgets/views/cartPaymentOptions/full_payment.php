<?php

use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var \hiqdev\yii2\cart\ShoppingCart $cart
 * @var \hipanel\modules\finance\models\Purse $purse
 * @var float $amount
 * @var string $currency
 * @var float $debt
 */

if (isset($debt)) {
    $text = Yii::t('hipanel:finance', 'Recharge account to pay the whole cart: {amount}, including debt {debt}', [
        'amount' => Yii::$app->formatter->asCurrency($amount, $currency),
        'debt' => Yii::$app->formatter->asCurrency($debt, $currency),
    ]);
} else {
    $text = Yii::t('hipanel:finance', 'Recharge account to pay the whole cart: {amount}', [
        'amount' => Yii::$app->formatter->asCurrency($amount, $currency)
    ]);
}
?>

<p>
    <?= Html::a($text, ['@finance/cart/full', 'currency' => $currency], [
        'class' => 'btn btn-lg btn-primary btn-block lock-on-click',
        'data-loading-text' => Yii::t('hipanel:finance', 'Processing your cart...'),
    ]) ?>
</p>
