<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\models\Tariff;
use hipanel\modules\finance\models\Tariffprofile;
use hipanel\modules\finance\widgets\combo\TariffCombo;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var Tariffprofile $model
 */
?>

<?php $form = ActiveForm::begin([
    'id' => 'tariffprofile-form',
    'validationUrl' => Url::toRoute(['validate-form', 'scenario' => $model->scenario]),
]) ?>

<?php $model->client_id = $client_id ?>

<div class="row">
    <div class="col-md-4">
        <div class="box box-widget">
            <div class="box-body">
                <?php if (!$model->isNewRecord) : ?>
                    <?= Html::activeHiddenInput($model, 'id') ?>
                <?php endif ?>
                <?php if ($model->isNewRecord || ((int) $model->id !== (int) $model->client_id)) : ?>
                    <?= $form->field($model, 'name') ?>
                <?php endif ?>

                <?= Html::activeHiddenInput($model, 'client_id') ?>

                <?php foreach ([Tariff::TYPE_DOMAIN, Tariff::TYPE_CERT] as $type) : ?>
                <?php if (Yii::getALias("@{$type}", false) === false) : ?>
                    <?php continue ?>
                <?php endif ?>
                    <?= $form->field($model, $type)->widget(TariffCombo::class, [
                        'tariffType' => $type,
                        'hasId' => true,
                        'multiple' => false,
                        'client' => $client,
                    ]) ?>
                <?php endforeach ?>
                <?php  if (Yii::getAlias('@server', false) !== false) : ?>
                    <?php foreach ([Tariff::TYPE_XEN, Tariff::TYPE_OPENVZ, Tariff::TYPE_SERVER] as $type) : ?>
                        <?= $form->field($model, $type)->widget(TariffCombo::class, [
                            'tariffType' => $type,
                            'client' => $client,
                            'hasId' => true,
                            'multiple' => true,
                        ]) ?>
                    <?php endforeach ?>
                <?php endif ?>
            </div>
        </div>

        <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
        &nbsp;
        <?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
    </div>
</div>

<?php ActiveForm::end() ?>
