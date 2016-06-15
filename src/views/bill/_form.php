<?php

use hipanel\helpers\Url;
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\widgets\DatePicker;
use hipanel\widgets\DateTimePicker;
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

<div class="container-items"><!-- widgetContainer -->
    <?php foreach ($models as $i => $model) : ?>
        <div class="row">
            <div class="col-md-4">
                <div class="box box-danger">
                    <div class="box-body">
                        <div class="form-instance" xmlns="http://www.w3.org/1999/html"
                             xmlns="http://www.w3.org/1999/html">
                            <?= $form->field($model, "[$i]client_id")->widget(ClientCombo::class, ['formElementSelector' => '.form-instance']) ?>
                            <?= $form->field($model, "[$i]type")->dropDownList($billTypes, ['groups' => $billGroupLabels]) ?>
                            <?= $form->field($model, "[$i]sum") ?>
                            <div class="form-group">
                                <?= Html::label(Yii::t('hipanel/finance', 'Date')) ?>
                                <?= DateTimePicker::widget([
                                    'model' => $model,
                                    'attribute' => "[$i]time",
                                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'dd.mm.yyyy HH:ii:ss',
                                    ],
                                ]) ?>
                            </div>
                            <?= $form->field($model, "[$i]label") ?>
                        </div>
                    </div>
                </div>
                <!-- ticket-_form -->
            </div>
        </div>
    <?php endforeach ?>
</div>
<?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-default']) ?>
&nbsp;
<?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
<?php ActiveForm::end(); ?>
