<?php

use hipanel\helpers\Url;
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\finance\models\Bill;
use hipanel\widgets\AmountWithCurrency;
use hipanel\widgets\DateTimePicker;
use hipanel\widgets\DynamicFormWidget;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var Bill[] $models
 */

$this->title = Yii::t('hipanel:finance', 'Add internal transfer');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $model = reset($models) ?>

<?php $form = ActiveForm::begin([
    'id' => 'bill-dynamic-form',
    'action' => Url::to(['@bill/create-transfer']),
    'enableClientValidation' => true,
    'enableAjaxValidation' => true,
    'validationUrl' => Url::toRoute([
        'validate-form',
        'scenario' => $model->scenario,
    ]),
]) ?>

    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'bills_dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.bill-item', // required: css class
        'limit' => 99, // the maximum times, an element can be cloned (default 999)
        'min' => 1, // 0 or 1 (default 1)
        'insertButton' => '.add-item', // css class
        'deleteButton' => '.remove-item', // css class
        'model' => $model,
        'formId' => 'bill-dynamic-form',
        'formFields' => [
            'sum',
            'quantity',
            'client_id',
            'receiver_id',
            'time',
        ],
    ]) ?>
        <?php $i = 0 ?>
        <div class="container-items">
            <?php foreach ($models as $model) : ?>
                <div class="bill-item">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">&nbsp;</h3>
                            <div class="box-tools">
                                <div class="btn-group">
                                    <button type="button" class="add-item btn btn-box-tool">
                                        <i class="glyphicon glyphicon-plus text-success"></i>
                                    </button>
                                    <button type="button" class="remove-item btn btn-box-tool">
                                        <i class="glyphicon glyphicon-minus text-danger"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row input-row margin-bottom">
                                <div class="col-lg-offset-10 col-sm-2 text-right">
                                    <?= Html::activeHiddenInput($model, "[$i]id") ?>
                                </div>
                                <div class="form-instance">
                                    <div class="col-md-2 <?= AmountWithCurrency::$widgetClass ?>">
                                        <?= $form->field($model, "[$i]sum")->widget(AmountWithCurrency::class, [
                                            'currencyAttributeName' => "[$i]currency",
                                            'currencyAttributeOptions' => [
                                                'items' => $this->context->getCurrencyTypes(),
                                            ],
                                            'options' => [
                                                'data-bill-sum' => true,
                                            ],
                                        ]) ?>
                                        <?= $form->field($model, "[$i]currency", ['template' => '{input}{error}'])->hiddenInput() ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?= $form->field($model, "[$i]client_id")->widget(ClientCombo::class, [
                                            'formElementSelector' => '.form-instance',
                                        ])->label(Yii::t('hipanel:finance', 'Sender')) ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?= $form->field($model, "[$i]receiver_id")->widget(ClientCombo::class, [
                                            'formElementSelector' => '.form-instance',
                                        ]) ?>
                                    </div>
                                    <div class="col-md-2">
                                        <?= $form->field($model, "[$i]time")->widget(DateTimePicker::class, [
                                            'model' => $model,
                                            'options' => [
                                                'value' => Yii::$app->formatter->asDatetime(new DateTime(), 'php:Y-m-d H:i:s'),
                                            ],
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach ?>
        </div>


    <?php $this->registerJs(<<<JS
        $('#dynamic-form').on('change', '.charge-item input[data-attribute=sum]', function () {
            $(this).closest('.bill-item').find('input[data-bill-sum]').blur();
        });
JS
    ) ?>

    <?php DynamicFormWidget::end() ?>

    <div class="row">
        <div class="col-md-12">
            <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
            &nbsp;
            <?= Html::button(Yii::t('hipanel', 'Cancel'),
                ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
        </div>
    </div>

<?php ActiveForm::end() ?>
