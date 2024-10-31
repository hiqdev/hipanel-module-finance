<?php

/**
 * @var \yii\web\View $this
 * @var \hiqdev\yii2\merchant\models\PurchaseRequest[] $merchants
 */
\hiqdev\paymenticons\yii2\PaymentIconsAsset::register($this);

$methods = [];

$provides = [
    'paypal' => ['visa', 'maestro2'],
    'interkassa' => ['visa', 'maestro2'],
];

$cashew = false;

foreach ($merchants as $merchant) {
    if ($merchant->system === 'cashew') {
        $cashew = $merchant;
        break;
    }
    $methods[] = $name = $merchant->system;
    if (isset($provides[$name])) {
        $methods = array_merge($provides[$name], $methods);
    }
}

$methods = array_unique($methods);

?>

<?php if ($cashew === false) : ?>
    <p class="lead"><?= Yii::t('cart', 'Payment methods') ?>:</p>

    <?php if (empty($methods)) : ?>
        <?= Yii::t('cart', 'No available payment methods') ?>
    <?php else : ?>
        <?php foreach ($methods as $name) : ?>
            <i class="pi pi-<?= strtolower($name) ?>"></i>
        <?php endforeach ?>
    <?php endif ?>
<?php endif ?>
