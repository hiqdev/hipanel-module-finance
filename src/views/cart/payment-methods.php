<?php

\hiqdev\paymenticons\yii2\PaymentIconsAsset::register($this);

?>
<p class="lead"><?= Yii::t('cart', 'Payment Methods') ?>:</p>

<?php foreach ($merchants as $merchant) : ?>
    <i class="pi pi-<?= strtolower($merchant->gateway) ?>"></i>
<?php endforeach ?>

<p class="text-muted well well-sm no-shadow" style="margin-top: 10px">
    Hello, etsy doostang zoodles disqus groupon greplin oooj voxy zoodles, weebly ning heekya handango imeem plugg
    dopplr jibjab, movity jajah plickers sifteo edmodo ifttt zimbra.
</p>
