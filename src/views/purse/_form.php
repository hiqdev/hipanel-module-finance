<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\models\Purse;
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hipanel\modules\finance\widgets\combo\RequisitesCombo;
use hipanel\modules\finance\widgets\combo\ResellerRequisitesCombo;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$model = reset($models);

$form = ActiveForm::begin([
    'id' => 'dynamic-form',
    'enableClientValidation' => true,
    'enableAjaxValidation' => true,
    'validationUrl' => Url::toRoute([
        'validate-form', 
        'scenario' => $model->isNewRecord ? 'create' : 'update',
    ]),
]);
?>

<div class="container-items">
    <?php foreach ($models as $i => $model) : ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box box-widget">
                    <div class="box-header with-border">
                        <?php if ($model->isNewRecord) : ?>
                            <h3 class="box-title"></h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool remove-item"><i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool add-item"><i class="fa fa-plus"></i></button>
                            </div>
                        <?php else: ?>
                            <h3 class="box-title"><?= $model->client ?> <?= $model->currency ?></h3>
                        <?php endif ?>
                    </div>
                    <div class="box-body">
                        <div class="form-instance">
                                <div class="col-md-2">
                                    <?= $form->field($model, "[{$i}]seller")->widget(SellerCombo::class, [
                                        'formElementSelector' => '.form-instance',
                                        'clientType' => ['owner', 'reseller', 'client'],
                                        'inputOptions' => [
                                            'readonly' => !$model->isNewRecord,
                                        ],
                                    ]) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($model, "[{$i}]client")->widget(ClientCombo::class, [
                                        'formElementSelector' => '.form-instance',
                                        'inputOptions' => [
                                            'readonly' => !$model->isNewRecord,
                                        ],
                                    ]) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($model, "[{$i}]currency")->dropDownList(Purse::getCurrencyOptions(), [
                                        'readonly' => !$model->isNewRecord,
                                    ]) ?>
                                </div>
                            <?php if (!$model->isNewRecord): ?>
                                <?= $form->field($model, "[{$i}]id")->hiddenInput()->label(false) ?>
                            <?php endif ?>
                            <div class="col-md-2">
                                <?= $form->field($model, "[{$i}]requisite_id")->widget(ResellerRequisitesCombo::class, [
                                    'inputOptions' => [
                                        'data-attribute' => 'reseller-requisite',
                                    ],
                                ]) ?>
                            </div>
                            <div class="col-md-2">
                                <?= $form->field($model, "[{$i}]contact_id")->widget(RequisitesCombo::class, [
                                    'inputOptions' => [
                                        'data-attribute' => 'requisite',
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

<?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
&nbsp;
<?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>

<?php $form->end() ?>
