<?php


?>

<h1><?= Yii::t('app', 'On your credit') ?>:
    <?= $cart->formatCurrency($client->balance) ?> &nbsp;
    <?= ($client->credit > 0) ? ' ( + ' . $cart->formatCurrency($client->credit) . ' )' : '' ?>
</h1>

<?php if ($rest >= $cart->total) : ?>
    <h3><?= Yii::t('app', 'it\'s enough to pay your cart') ?>:</h3>
    <br>
    <p><a href="/finance/cart/finish" class="btn btn-lg btn-primary" style="width:30em">
        <?= Yii::t('app', 'Use credit funds without depositing') ?>
    </a></p>
<?php else : ?>
    <h3><?= Yii::t('app', 'You can pay your cart partially') ?>:</h3>
    <br>
    <p><a href="/finance/cart/partial" class="btn btn-lg btn-primary" style="width:30em">
        <?= Yii::t('app', 'Use credit funds and pay the difference') ?>: <?= $cart->formatCurrency($cart->total - $rest) ?>
    </a></p>
<?php endif ?>
<?php if ($cart->total > 0) : ?>
    <p><a href="/finance/cart/full" class="btn btn-lg btn-info" style="width:30em">
        <?= Yii::t('app', 'Do not use credit funds, pay the whole cart') ?>: <?= $cart->formatCurrency($cart->total) ?>
    </a></p>
<?php endif ?>
