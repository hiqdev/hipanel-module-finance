<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\widgets\RequisiteTemplatesWidget;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use hipanel\widgets\RefCombo;

?>

<div>

    <!-- Nav tabs -->
    <?php if (count($models) > 1) : ?>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active"><a href="#bulk" aria-controls="home" role="tab" data-toggle="tab"><?= Yii::t('hipanel', 'Set for all') ?></a></li>
            <li role="presentation"><a href="#by-one" aria-controls="profile" role="tab" data-toggle="tab"><?= Yii::t('hipanel', 'Set by one') ?></a></li>
        </ul>
    <?php endif ?>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="bulk">
            <div class="row" style="margin-top: 15pt;">
                <div class="col-md-12">
                    <?php if (!empty($models)) : ?>
                        <div class="panel panel-default">
                            <div class="panel-heading"><?= Yii::t('hipanel:finance', 'Affected requisites') ?></div>
                            <div class="panel-body">
                                <?= \hipanel\widgets\ArraySpoiler::widget([
                                    'data' => $models,
                                    'visibleCount' => count($models),
                                    'formatter' => function ($model) {
                                        return "{$model->email} / {$model->name}";
                                    },
                                    'delimiter' => ',&nbsp; ',
                                ]); ?>
                            </div>
                        </div>

                        <?= RequisiteTemplatesWidget::widget([
                            'model' => $models,
                            'actionUrl' => 'bulk-set-templates',
                        ]) ?>
                        <br>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="by-one">
            <?php $form = ActiveForm::begin([
                'id' => 'bulk-set-templates',
                'action' => Url::toRoute('set-templates'),
                'enableAjaxValidation' => true,
                'validateOnBlur' => true,
                'validationUrl' => Url::toRoute(['validate-form', 'scenario' => 'set-templates']),
            ]) ?>
            <div class="row" style="margin-top: 15pt;">
                <?php foreach ($models as $model) : ?>
                    <div class="col-md-12" style="line-height: 34px;">
                        <b><?= "{$model->name} / {$model->organization}" ?></b>
                    </div>
                    <?= Html::activeHiddenInput($model, "[$model->id]id") ?>
                    <?php foreach (['invoice', 'acceptance', 'contract', 'probation'] as $template) : ?>
                        <div class="col-md-6">
                            <?= $form->field($model, "[$model->id]{$template}_id")->widget(RefCombo::class, [
                                'gtype' => "type,document,{$template}",
                            ]) ?>
                        </div>
                    <?php endforeach ?>
                    <hr>
                <?php endforeach ?>
            </div>
            <hr>
            <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success', 'id' => 'save-button']) ?>
            <?php ActiveForm::end() ?>
        </div>
    </div>

</div>
