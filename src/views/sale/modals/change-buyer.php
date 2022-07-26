<?php

use hipanel\helpers\Url;
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\finance\models\Sale;
use hipanel\modules\finance\widgets\combo\PlanCombo;
use hipanel\widgets\ArraySpoiler;
use hipanel\widgets\DateTimePicker;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/** @var array $salesByTariffType */
/** @var Sale $model */

?>

<?php $form = ActiveForm::begin([
    'id' => 'change-buyer-form',
    'enableAjaxValidation' => false,
    'options' => ['autocomplete' => 'off'],
    'action' => Url::to(['@sale/change-buyer']),
    'method' => 'POST',
]) ?>

<?php foreach ($salesByTariffType as $tariffType => $models) : ?>
    <?= ArraySpoiler::widget([
        'data' => $models,
        'visibleCount' => count($models),
        'formatter' => static fn(Sale $model): string => Html::activeHiddenInput($model, "[$tariffType]id[]", ['value' => $model->id]),
        'delimiter' => '',
    ]) ?>
    <div class="panel panel-default">
        <div class="panel-heading" style="text-transform: uppercase; font-weight: bold;">
            <?= $tariffType ?>
        </div>
        <ul class="list-group">
            <?= ArraySpoiler::widget([
                'data' => $models,
                'visibleCount' => count($models),
                'formatter' => static fn(Sale $model): string => Html::tag(
                    'li',
                    sprintf(
                        "%s / %s",
                        Html::tag('span', $model->object, ['class' => 'text-bold']),
                        Html::a($model->tariff, ['@plan/view', 'id' => $model->tariff_id], ['target' => '_blank'])
                    ),
                    ['class' => 'list-group-item']
                ),
                'delimiter' => '',
            ]) ?>
        </ul>
        <div class="panel-footer">
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, "[$tariffType]tariff_id")->widget(PlanCombo::class, [
                        'hasId' => true,
                        'tariffType' => $tariffType,
                        'inputOptions' => ['id' => "sale-$tariffType-tariff_id"],
                    ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, "[$tariffType]buyer_id")->widget(ClientCombo::class, [
                        'inputOptions' => ['id' => "sale-$tariffType-buyer_id"],
                    ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, "[$tariffType]time")->widget(DateTimePicker::class, [
                        'model' => $model,
                        'clientOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd hh:ii:00', 'todayBtn' => true],
                        'options' => ['value' => Yii::$app->formatter->asDatetime(new DateTime(), 'php:Y-m-d H:i:s')],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach ?>

<?= Html::submitButton(Yii::t('hipanel:finance', 'Submit'), ['class' => 'btn btn-success btn-block']) ?>

<?php ActiveForm::end() ?>
