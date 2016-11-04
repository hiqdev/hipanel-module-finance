<?php

use hipanel\helpers\Url;
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\finance\models\Bill;
use hipanel\widgets\Box;
use hipanel\widgets\AmountWithCurrency;
use hipanel\widgets\DatePicker;
use hipanel\widgets\DateTimePicker;
use hipanel\widgets\DynamicFormWidget;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/** @var yii\web\View $this */
/** @var hipanel\modules\finance\models\Bill[] $models */
/** @var array $billTypes */
/** @var array $billGroupLabels */

$model = reset($models);

$form = ActiveForm::begin([
    'id' => 'dynamic-form',
    'action' => $model->isNewRecord ? Url::to('create') : Url::to('update'),
    'enableClientValidation' => true,
    'validationUrl' => Url::toRoute([
        'validate-form',
        'scenario' => $model->isNewRecord ? $model->scenario : Bill::SCENARIO_UPDATE,
    ]),
]) ?>
<?php DynamicFormWidget::begin([
    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
    'widgetBody' => '.container-items', // required: css class selector
    'widgetItem' => '.item', // required: css class
    'limit' => 99, // the maximum times, an element can be cloned (default 999)
    'min' => 1, // 0 or 1 (default 1)
    'insertButton' => '.add-item', // css class
    'deleteButton' => '.remove-item', // css class
    'model' => reset($models),
    'formId' => 'dynamic-form',
    'formFields' => [
        'client_id',
        'type',
        'sum',
        'time',
        'label',
    ],
]) ?>

<div class="container-items">
    <?php foreach ($models as $i => $model) : ?>
        <div class="item">
            <?php Box::begin() ?>
            <div class="row input-row margin-bottom">
                <div class="col-lg-offset-10 col-sm-2 text-right">
                    <?= Html::activeHiddenInput($model, "[$i]id") ?>
                    <?php if ($model->isNewRecord) : ?>
                        <div class="btn-group">
                            <button type="button" class="add-item btn btn-success btn-sm"><i
                                    class="glyphicon glyphicon-plus"></i></button>
                            <button type="button" class="remove-item btn btn-danger btn-sm"><i
                                    class="glyphicon glyphicon-minus"></i></button>
                        </div>
                        <!-- /.btn-group -->
                    <?php endif ?>
                </div>
                <div class="form-instance">
                    <div class="col-md-2">
                        <?= $form->field($model, "[$i]client_id")->widget(ClientCombo::class, [
                            'formElementSelector' => '.form-instance',
                            'inputOptions' => [
                                'readonly' => $model->scenario == Bill::SCENARIO_UPDATE,
                            ]
                        ]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, "[$i]type")->dropDownList($billTypes, [
                            'groups' => $billGroupLabels,
                            'value' => $model->gtype ? implode(',', [$model->gtype, $model->type]) : null,
                        ]) ?>
                    </div>
                    <div class="col-md-2 <?= AmountWithCurrency::$widgetClass ?>">
                        <?= $form->field($model, "[$i]sum")->widget(AmountWithCurrency::class, [
                            'currencyAttributeName' => "[$i]currency",
                            'currencyAttributeOptions' => [
                                'items' => $this->context->getCurrencyTypes(),
                            ],
                        ]) ?>
                        <?= $form->field($model, "[$i]currency", ['template' => '{input}{error}'])->hiddenInput() ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, "[$i]time")->widget(DateTimePicker::class, [
                            'model' => $model,
                            'type' => DatePicker::TYPE_COMPONENT_APPEND,
                            'pluginOptions' => [
                                'autoclose' => true,
                                'format' => 'dd.mm.yyyy HH:ii:ss',
                            ],
                            'options' => [
                                'value' => Yii::$app->formatter->asDatetime($model->time, 'php:d.m.Y H:i:s')
                            ]
                        ]) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, "[$i]label") ?>
                    </div>
                </div>
            </div>
            <?php Box::end() ?>
        </div>
    <?php endforeach ?>
</div>

<?php DynamicFormWidget::end() ?>
<?php Box::begin(['options' => ['class' => 'box-solid']]) ?>
<div class="row">
    <div class="col-md-12 no">
        <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
        &nbsp;
        <?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
    </div>
    <!-- /.col-md-12 -->
</div>
<!-- /.row -->
<?php Box::end() ?>
<?php ActiveForm::end() ?>
