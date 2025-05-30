<?php

use hipanel\modules\finance\forms\BillFromPricesForm;
use hipanel\modules\finance\grid\PriceGridView;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\widgets\BillTypeVueTreeSelect;
use hipanel\modules\finance\widgets\TreeSelectBehavior;
use hipanel\widgets\DateTimePicker;
use yii\bootstrap\ActiveForm;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var BillFromPricesForm $model
 * @var Price[] $prices
 * @var array $billTypes
 */

?>

<?php $form = ActiveForm::begin([
    'id' => 'create-bill-from-prices-form',
    'action' => Url::to(['@bill/create-from-prices']),
    'enableClientValidation' => true,
]) ?>

<?php foreach ($prices as $price) : ?>
    <?= Html::hiddenInput('selection[]', $price->id) ?>
<?php endforeach ?>

<div class="row">
    <div class="col-md-6">
        <?= $form->field($model, "type_id")->widget(BillTypeVueTreeSelect::class, [
            'billTypes' => $billTypes,
            'deprecatedTypes' => Yii::$app->params['module.finance.bill.types']['deprecated.types'],
            'behavior' => TreeSelectBehavior::Hidden,
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'time')->widget(DateTimePicker::class, [
            'model' => $model,
            'options' => [
                'value' => Yii::$app->formatter->asDatetime(($model->isNewRecord && empty($model->time) ? new DateTime() : $model->time),
                    'php:Y-m-d H:i:s'),
            ],
        ]) ?>
    </div>
    <div class="col-md-12">
        <?= $form->field($model, 'charges_description')->textarea() ?>
    </div>
    <div class="col-md-12">
        <?= PriceGridView::widget([
            'boxed' => false,
            'showHeader' => false,
            'dataProvider' => new ArrayDataProvider(['models' => $prices, 'pagination' => false]),
            'layout' => '{items}',
            'columns' => [
                'object->name',
                'object->label',
                'type',
                'price',
            ],
        ]) ?>
        <?= Html::submitButton(Yii::t('hipanel:finance', 'Proceed to bill(s) creation'), [
            'class' => 'btn btn-block btn-success',
        ]) ?>
    </div>
</div>

<?php ActiveForm::end() ?>
