<?php

use hipanel\modules\finance\forms\BillFromPricesForm;
use hipanel\modules\finance\grid\PriceGridView;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\widgets\combo\MultipleBillTypeCombo;
use hipanel\widgets\DateTimePicker;
use yii\bootstrap\ActiveForm;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var BillFromPricesForm $model */
/** @var Price[] $prices */
/** @var array $billTypes */
/** @var array $billGroupLabels */

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
        <?= $form->field($model, "type")->widget(MultipleBillTypeCombo::class, [
            'billTypes' => $billTypes,
            'billGroupLabels' => $billGroupLabels,
            'multiple' => false,
            'useFullType' => true,
        ]) ?>
    </div>
    <div class="col-md-6">
        <?= $form->field($model, 'time')->widget(DateTimePicker::class, [
            'model' => $model,
            'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd hh:ii:ss',
            ],
            'options' => [
                'value' => Yii::$app->formatter->asDatetime(($model->isNewRecord && empty($model->time) ? new DateTime() : $model->time),
                    'php:Y-m-d H:i:s'),
            ],
        ]) ?>
    </div>
    <div class="col-md-12">
        <?= PriceGridView::widget([
            'boxed' => false,
            'showHeader' => false,
            'dataProvider' => new ArrayDataProvider(['models' => $prices, 'pagination' => false]),
            'layout' => '{items}',
            'columns' => [
                'object->name',
            ],
        ]) ?>
        <?= Html::submitButton(Yii::t('hipanel:finance', 'Proceed to bill(s) creation'), [
            'class' => 'btn btn-block btn-success',
        ]) ?>
    </div>
</div>

<?php ActiveForm::end() ?>
