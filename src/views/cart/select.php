<?php

/**
 * @var float $budget client's balance + credit
 * @var \hipanel\modules\client\models\Client $client
 * @var \hiqdev\yii2\cart\ShoppingCart $cart
 * @var \yii\web\View $this
 */
use yii\helpers\Html;

$this->title = Yii::t('hipanel:finance', 'Select payment option');
?>

<div class="row">
    <div class="col-md-offset-1 col-md-10">

        <div class="box box-solid">
            <div class="box-header with-border text-center">
                <h3 class="box-title">
                    <?= Yii::t('hipanel:finance', 'You balance: {balance} {formattedCredit}', [
                        'balance' => $cart->formatCurrency($client->balance, $client->currency),
                        'formattedCredit' => $client->credit > 0 ? Yii::t('hipanel:finance', '(+{amount} of credit)', ['amount' => $cart->formatCurrency($client->credit, $client->currency)]) : '',
                    ]) ?>:
                </h3>
            </div>
            <div class="box-body">
                <?php if (round($budget * 100) >= round($cart->total * 100)) : ?>
                    <h3><?= Yii::t('hipanel:finance', 'It\'s enough to pay your cart') ?></h3>
                    <br>
                    <p>
                        <?= Html::a(Yii::t('hipanel:finance', 'Use credit funds without depositing'), '@finance/cart/finish', [
                            'class' => 'btn btn-lg btn-primary btn-block lock-on-click',
                            'data-loading-text' => Yii::t('hipanel:finance', 'Processing your cart...'),
                        ]) ?>
                    </p>
                <?php else : ?>
                    <h3><?= Yii::t('hipanel:finance', 'It\'s not enough to pay your cart') ?></h3>
                    <br>
                    <?php if (strtolower($client->currency) !== strtolower($cart->currency)
                                && Yii::$app->user->can('resel')) : ?>
                        <p>
                            <?php $text = Yii::t('hipanel:finance', 'Use credit funds and pay the difference {amount}', [
                                'amount' => $cart->formatCurrency($cart->total - $budget),
                            ]) ?>
                            <?= Html::a($text, '@finance/cart/partial', [
                                'class' => 'btn btn-lg btn-primary btn-block lock-on-click',
                                'data-loading-text' => Yii::t('hipanel:finance', 'Processing your cart...'),
                            ]) ?>
                        </p>
                    <?php endif ?>
                <?php endif ?>
                <?php if ($cart->total > 0) : ?>
                    <p>
                        <?php $text = Yii::t('hipanel:finance', 'Do not use credit funds, pay the whole cart: {amount}', [
                            'amount' => $cart->formatCurrency($cart->total),
                        ]) ?>
                        <?= Html::a($text, '@finance/cart/full', [
                            'class' => 'btn btn-lg btn-primary btn-block lock-on-click',
                            'data-loading-text' => Yii::t('hipanel:finance', 'Processing your cart...'),
                        ]) ?>
                    </p>
                <?php endif ?>
            </div>
        </div>

    </div>
</div>

<?php

$this->registerJs(<<<'JS'
$('.lock-on-click').one('click', function (e) {
    if ($(this).hasClass('disabled')) {
        e.preventDefault();
        return;
    }

    $(this).button('loading');
    $('.lock-on-click').not(this).fadeOut();
});
JS
);
