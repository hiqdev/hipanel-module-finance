<?php

use yii\bootstrap\Modal;

?>

<?php Modal::begin([
    'header' => '<h2>Proceed order</h2>',
    'toggleButton' => [
        'label' => '<i class="fa fa-credit-card"></i> ' . Yii::t('cart', 'Make order'),
        'id'    => 'make-order-button',
        'class' => 'btn btn-success' . ($module->termsPage ? ' disabled' : ''),
    ],
]) ?>

<?= 'Say hello...' ?>

<?php Modal::end() ?>
