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

$hideBlock = false;

foreach ($merchants as $merchant) {
    if (Yii::$app->getModule('merchant')->cashewOnly && $merchant->system === 'cashew') {
        $hideBlock = true;
        break;
    }
    $methods[] = $name = $merchant->system;
    if (isset($provides[$name])) {
        $methods = array_merge($provides[$name], $methods);
    }
}

?>

<?php if ($hideBlock !== true) : ?>
    <p class="lead"><?= Yii::t('cart', 'Payment methods') ?>:</p>
    <?php if (empty($methods)) : ?>
        <?= Yii::t('cart', 'No available payment methods') ?>
    <?php else : ?>
        <?php foreach ($methods as $name) : ?>
            <i class="pi pi-<?= strtolower($name) ?>"></i>
        <?php endforeach ?>
    <?php endif ?>
<?php endif ?>
