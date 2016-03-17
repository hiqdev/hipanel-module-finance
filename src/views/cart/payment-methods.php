<?php

\hiqdev\paymenticons\yii2\PaymentIconsAsset::register($this);

$methods = [];

$provides = [
    'paypal' => ['visa', 'maestro2'],
    'interkassa' => ['visa', 'maestro2'],
];

foreach ($merchants as $merchant) {
    $name = strtolower($merchant->gateway);
    $methods[] = $name;
    $pro = $provides[$name];
    if ($pro) {
        $methods = array_merge($pro, $methods);
    }
}

$methods = array_unique($methods);

?>
    <p class="lead"><?= Yii::t('cart', 'Payment methods') ?>:</p>

<?php if (empty($methods)) :
    echo Yii::t('cart', 'No available payment methods');
else :
    foreach ($methods as $name) : ?>
        <i class="pi pi-<?= strtolower($name) ?>"></i>
    <?php endforeach;
endif; ?>
