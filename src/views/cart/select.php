<?php

/**
 * @var float $rest client's balance + credit
 * @var \hipanel\modules\client\models\Client $client
 * @var \hiqdev\yii2\cart\ShoppingCart $cart
 * @var \yii\web\View $this
 *
 */
use yii\helpers\Html;

$this->title = Yii::t('hipanel:finance', 'Select payment option');
?>

<div class="row">
    <div class="col-md-offset-3 col-md-6">

        <div class="box box-solid">
            <div class="box-header with-border text-center">
                <h3 class="box-title">
                    <?= Yii::t('hipanel:finance', 'You balance: {balance} {formattedCredit}', [
                        'balance' => $cart->formatCurrency($client->balance),
                        'formattedCredit' => $client->credit > 0 ? Yii::t('hipanel:finance', '(+{amount} of credit)', ['amount' => $cart->formatCurrency($client->credit)]) : ''
                    ]) ?>:
                </h3>
            </div>
            <div class="box-body">
                <?php if ($rest >= $cart->total) : ?>
                    <h3><?= Yii::t('hipanel:finance', 'It\'s enough to pay your cart') ?></h3>
                    <br>
                    <p>
                        <?= Html::a(Yii::t('hipanel:finance', 'Use credit funds without depositing'), '@finance/cart/finish', [
                            'class' => 'btn btn-lg btn-primary btn-block lock-on-click',
                            'data-loading-text' => Yii::t('hipanel:finance', 'Processing your cart...'),
                        ]) ?>
                    </p>
                <?php else : ?>
                    <h3><?= Yii::t('hipanel:finance', 'You can pay your cart partially') ?>:</h3>
                    <br>
                    <p>
                        <?php $text = Yii::t('hipanel:finance', 'Use credit funds and pay the difference {amount}', [
                            'amount' => $cart->formatCurrency($cart->total - $rest)
                        ]);
                        echo Html::a($text, '@finance/cart/partial', [
                            'class' => 'btn btn-lg btn-primary btn-block lock-on-click',
                            'data-loading-text' => Yii::t('hipanel:finance', 'Processing your cart...'),
                        ]) ?>
                    </p>
                <?php endif ?>
                <?php if ($cart->total > 0) : ?>
                    <p>
                        <?php $text = Yii::t('hipanel:finance', 'Do not use credit funds, pay the whole cart: {amount}', [
                            'amount' => $cart->formatCurrency($cart->total)
                        ]);
                        echo Html::a($text, '@finance/cart/full', [
                            'class' => 'btn btn-lg btn-primary btn-block lock-on-click',
                            'data-loading-text' => Yii::t('hipanel:finance', 'Processing your cart...')
                        ]) ?>
                    </p>
                <?php endif ?>
            </div>
        </div>

    </div>
</div>

<?php

$this->registerJs(<<<JS
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
