<?php

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hiqdev\combo\StaticCombo;

?>

<div class="col-md-6">
    <?= $search->field('descr') ?>
    <?= $search->field('type')->widget(StaticCombo::classname(), [
        'data' => $paymentType,
        'hasId' => true,
        'pluginOptions' => [
            'select2Options' => [
                'multiple' => false,
            ]
        ],
    ]) ?>
</div>

<div class="col-md-6">
    <?= $search->field('client_id')->widget(ClientCombo::classname()) ?>
    <?= $search->field('seller_id')->widget(SellerCombo::classname()) ?>
</div>
