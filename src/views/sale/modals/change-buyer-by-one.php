<?php

use hipanel\helpers\Url;
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\finance\models\Sale;
use hipanel\modules\finance\widgets\combo\PlanCombo;
use hipanel\modules\finance\widgets\LinkToObjectResolver;
use hipanel\widgets\DateTimePicker;
use yii\helpers\Html;
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
    <?php foreach ($models as $idx => $model) : ?>
        <?= Html::activeHiddenInput($model, "[$tariffType][$idx]id", ['value' => $model->id]) ?>
        <?= Html::activeHiddenInput($model, "[$tariffType][$idx]tariff_id", ['value' => $model->tariff_id]) ?>
        <div class="panel panel-default">
            <div class="panel-heading" style="display: flex; justify-content: space-between; text-transform: uppercase; font-weight: bold;">
                <span class="text-sm text-muted">
                <?= implode(" / ",
                    array_filter([
                        LinkToObjectResolver::widget([
                            'model' => $model,
                            'typeAttribute' => 'object_type',
                            'idAttribute' => 'object_id',
                        ]),
                        !empty($model->object_label) ? Html::encode($model->object_label) : null,
                    ])
                ) ?>
                </span>
                <span class="text-maroon text-sm">
                    <?= Html::encode($model->tariff) ?>
                </span>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-8">
                        <?= $form->field($model, "[$tariffType][$idx]buyer_id")->widget(ClientCombo::class, [
                            'hasId' => true,
                            'inputOptions' => [
                                'id' => "sale-$tariffType-$idx-buyer_id",
                                'readonly' => !$model->isOperateable(),
                            ],
                        ]) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, "[$tariffType][$idx]time")->widget(DateTimePicker::class, [
                            'model' => $model,
                            'clientOptions' => ['autoclose' => true, 'format' => 'yyyy-mm-dd hh:ii:00', 'todayBtn' => true],
                            'options' => [
                                'value' => Yii::$app->formatter->asDatetime(new DateTime(), 'php:Y-m-d H:i:s'),
                                'id' => "sale-$tariffType-$idx-time",
                                'readonly' => !$model->isOperateable(),
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>

    <?php endforeach ?>

<?php endforeach ?>

<?= Html::submitButton(Yii::t('hipanel:finance', 'Submit'), ['class' => 'btn btn-success btn-block']) ?>

<?php ActiveForm::end() ?>
