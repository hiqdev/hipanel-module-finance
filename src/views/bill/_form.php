<?php

use hipanel\helpers\Url;
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\widgets\Box;
use hipanel\widgets\AmountWithCurrencyWidget;
use hipanel\widgets\DatePicker;
use hipanel\widgets\DateTimePicker;
use hipanel\widgets\DynamicFormWidget;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/** @var yii\web\View $this */
/** @var hipanel\modules\finance\models\Bill $model */
/** @var array $billTypes */
/** @var array $billGroupLabels */
$form = ActiveForm::begin([
    'id' => 'dynamic-form',
    'enableClientValidation' => true,
    'validationUrl' => Url::toRoute([
        'validate-form',
        'scenario' => $model->isNewRecord ? $model->scenario : 'update',
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
        <div class="row item">
            <div class="box box-default">
                <div class="col-lg-offset-10 col-sm-2 text-right">
                    <?php if ($model->isNewRecord) : ?>
                        <div class="btn-group">
                            <button type="button" class="add-item btn btn-default btn-sm"><i class="glyphicon glyphicon-plus"></i></button>
                            <button type="button" class="remove-item btn btn-default btn-sm"><i class="glyphicon glyphicon-minus"></i></button>
                        </div>
                        <!-- /.btn-group -->
                    <?php endif ?>
                </div>
                <div class="box-body">
                    <div class="form-instance" xmlns="http://www.w3.org/1999/html">
                        <div class="col-md-2">
                            <?= $form->field($model, "[$i]client_id")->widget(ClientCombo::class, ['formElementSelector' => '.form-instance']) ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, "[$i]type")->dropDownList($billTypes, ['groups' => $billGroupLabels]) ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, "[$i]sum")->widget(AmountWithCurrencyWidget::class, [
                                'inputOptions' => ['placeholder' => '0.00'],
                                'selectAttribute' => "[$i]currency",
                                'selectAttributeOptions' => [
                                    'items' => $this->context->getCurrencyTypes(),
                                ],
                            ]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, "[$i]time")->widget(DateTimePicker::class, [
                                'model' => $model,
                                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'dd.mm.yyyy HH:ii:ss',
                                ],
                            ]) ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, "[$i]label") ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>

<?php DynamicFormWidget::end() ?>
<?php Box::begin(['options' => ['class' => 'box-solid']]) ?>
<div class="row">
    <div class="col-md-12 no">
        <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
        &nbsp;
        <?= Html::button(Yii::t('app', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
    </div>
    <!-- /.col-md-12 -->
</div>
<!-- /.row -->
<?php Box::end() ?>
<?php ActiveForm::end() ?>
