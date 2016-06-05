<?php

use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hiqdev\combo\StaticCombo;
use kartik\field\FieldRange;
use kartik\widgets\DatePicker;
use yii\helpers\Html;
/**
 * @var \hipanel\widgets\AdvancedSearch $search
 */
?>

<div class="col-md-4 col-sm-6 col-xs-12">
    <div class="form-group">
        <?= Html::label(Yii::t('hipanel', 'Date')) ?>
        <?= DatePicker::widget([
            'model' => $search->model,
            'attribute' => 'time_from',
            'attribute2' => 'time_till',
            'type' => DatePicker::TYPE_RANGE,
            'pluginOptions' => [
                'autoclose' => true,
                'format' => 'dd.mm.yyyy',
            ],
        ]) ?>
    </div>
</div>
<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('type')->widget(StaticCombo::classname(), [
        'data' => $type,
        'hasId' => true,
    ]) ?>
</div>
<div class="col-md-4 col-sm-6 col-xs-12">
    <?php if (Yii::$app->user->can('support')) : ?>
        <?= $search->field('client_id')->widget(ClientCombo::classname()) ?>
    <?php endif ?>
</div>
<div class="col-md-4 col-sm-6 col-xs-12">
    <?= FieldRange::widget([
        'model' => $search->model,
        'attribute1' => 'sum_gt',
        'attribute2' => 'sum_lt',
        'label' => Yii::t('hipanel', 'Sum'),
    ]) ?>
</div>
<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('descr') ?>
</div>
<?php if (Yii::$app->user->can('support')) : ?>
    <div class="col-md-4 col-sm-6 col-xs-12">
        <?= $search->field('seller_id')->widget(SellerCombo::classname()) ?>
    </div>
<?php endif ?>
<div class="col-md-4 col-sm-6 col-xs-12">
    <?= $search->field('currency')->widget(StaticCombo::classname(), [
        'data' => ['usd' => 'USD', 'eur' => 'EUR'],
        'hasId' => true,
        'pluginOptions' => [
            'select2Options' => [
                'multiple' => false,
            ],
        ],
    ]) ?>
</div>
