<?php

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\widgets\CartCurrencyNegotiator $negotiator
 */

$this->title = Yii::t('hipanel:finance', 'Select payment option');
?>

<div class="row">
    <div class="col-md-offset-1 col-md-10">
        <div class="box box-solid">
            <div class="box-header with-border text-center">
                <h3 class="box-title">
                    <?= Yii::t('hipanel:finance', 'Checkout total: {amount}', [
                        'amount' => $negotiator->renderCartTotals()
                    ]) ?>
                </h3>
            </div>
            <div class="box-header with-border text-center">
                <?= $negotiator->renderBalance() ?>
            </div>
            <div class="box-body">
                <?= $negotiator->run() ?>
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
