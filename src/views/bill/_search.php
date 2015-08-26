<?php

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hiqdev\combo\StaticCombo;

?>

<div class="col-md-6">
    <?= $search->field('type')->widget(StaticCombo::classname(), [
        'data' => $paymentType,
        'hasId' => true,
        'pluginOptions' => [
            'select2Options' => [
                'multiple' => false,
            ]
        ],
    ]) ?>
<?php if (Yii::$app->user->can('support')) { ?>
    <?= $search->field('client_id')->widget(ClientCombo::classname()) ?>
<?php } ?>
</div>

<div class="col-md-6">
    <?= $search->field('descr') ?>
<?php if (Yii::$app->user->can('support')) { ?>
    <?= $search->field('seller_id')->widget(SellerCombo::classname()) ?>
<?php } ?>
</div>
