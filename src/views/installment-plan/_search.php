<?php

/** @var \hipanel\widgets\AdvancedSearch $search */

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hipanel\widgets\MonthPicker;
use hipanel\modules\finance\widgets\combo\InstallmentPlanStateCombo;

?>

<?php if (Yii::$app->user->can('owner-staff')): ?>
<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('seller_id')->widget(SellerCombo::class) ?>
</div>
<?php endif ?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('client_id')->widget(ClientCombo::class) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('state')->widget(InstallmentPlanStateCombo::class) ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('month')
        ->label(false)
        ->widget(MonthPicker::class, [
            'options' => [
                'placeholder' => Yii::t('hipanel:finance', 'Month within plan period'),
            ],
            'clientOptions' => [
                'dateFormat' => 'Y-m-01',
            ],
        ]) ?>
</div>
