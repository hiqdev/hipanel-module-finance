<?php

\hiqdev\paymenticons\yii2\PaymentIconsAsset::register($this);

$provides = [
    'paypal'     => ['visa', 'maestro2'],
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
<p class="lead"><?= Yii::t('cart', 'Payment Methods') ?>:</p>

<?php foreach ($methods as $name) : ?>
    <i class="pi pi-<?= strtolower($name) ?>"></i>
<?php endforeach ?>

<p class="text-muted well well-sm no-shadow" style="margin-top: 10px">
    Hello, etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning heekya handango imeem plugg
    dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.
</p>
