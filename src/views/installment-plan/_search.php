<?php

/** @var \hipanel\widgets\AdvancedSearch $search */

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hipanel\modules\server\widgets\combo\DeviceCombo;
use hipanel\widgets\MonthPicker;
use hipanel\modules\finance\widgets\combo\InstallmentPlanStateCombo;
use hiqdev\yii2\daterangepicker\DateRangePicker;

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
    <?= $search->field('serialno_inilike') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('partno_inilike') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('device_like')->widget(DeviceCombo::class) ?>
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

<div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
        <?= DateRangePicker::widget([
            'model'      => $search->model,
            'attribute'  => 'since_ge',
            'attribute2' => 'since_le',
            'options'    => [
                'class'       => 'form-control',
                'placeholder' => Yii::t('hipanel:finance', 'Installment start'),
            ],
            'dateFormat' => 'yyyy-MM-dd',
        ]) ?>
    </div>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
        <?= DateRangePicker::widget([
            'model'      => $search->model,
            'attribute'  => 'till_ge',
            'attribute2' => 'till_le',
            'options'    => [
                'class'       => 'form-control',
                'placeholder' => Yii::t('hipanel:finance', 'Installment end'),
            ],
            'dateFormat' => 'yyyy-MM-dd',
        ]) ?>
    </div>
</div>
