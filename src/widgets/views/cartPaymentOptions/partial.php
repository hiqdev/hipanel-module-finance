<?php

use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var \hiqdev\yii2\cart\ShoppingCart $cart
 * @var \hipanel\modules\finance\models\Purse $purse
 * @var float $amount
 * @var string $currency
 */

?>

<p>
    <?php $text = Yii::t('hipanel:finance', 'Use {balance} from the balance and pay the difference {amount}', [
        'balance' => Yii::$app->formatter->asCurrency($purse->getBudget(), $currency),
        'amount' => Yii::$app->formatter->asCurrency($amount, $currency),
    ]) ?>
    <?= Html::a($text, ['@finance/cart/partial', 'currency' => $currency], [
        'class' => 'btn btn-lg btn-primary btn-block lock-on-click',
        'data-loading-text' => Yii::t('hipanel:finance', 'Processing your cart...'),
    ]) ?>
</p>
