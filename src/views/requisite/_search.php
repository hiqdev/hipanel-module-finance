<?php

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hiqdev\yii2\daterangepicker\DateRangePicker;
use hipanel\widgets\RefCombo;

/**
 * @var \hipanel\widgets\AdvancedSearch
 */
?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('name_like') ?>
</div>

<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('email_like') ?>
</div>

<?php if (Yii::$app->user->can('support')) : ?>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <?= $search->field('client_id')->widget(ClientCombo::class) ?>
    </div>

    <div class="col-md-4 col-sm-6 col-xs-12">
        <?= $search->field('seller_id')->widget(SellerCombo::class) ?>
    </div>

    <div class="col-md-4 col-sm-6 col-xs-12">
        <?= DateRangePicker::widget([
            'model' => $search->model,
            'attribute' => 'time_from',
            'attribute2' => 'time_till',
            'options' => [
                'class' => 'form-control',
            ],
            'dateFormat' => 'yyyy-MM-dd',
        ]) ?>
    </div>

    <div class="col-md-4 col-sm-6 col-xs-12">
        <?= $search->field('currency')->widget(RefCombo::class, [
            'gtype' => 'type,currency',
            'i18nDictionary' => 'hipanel:finance',
            'multiple' => false,
        ]) ?>
    </div>
<?php endif ?>
