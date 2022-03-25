<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\models\Tariffprofile;
use hipanel\modules\finance\widgets\combo\TariffCombo;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var Tariffprofile $model
 * @var string $client
 * @var int $client_id
 */
?>

<?php $form = ActiveForm::begin([
    'id' => 'tariffprofile-form',
    'validationUrl' => Url::toRoute(['validate-form', 'scenario' => $model->scenario]),
]) ?>

<?php $model->client_id = $client_id ?>
<?php $model->client = $client ?>

<div class="box box-widget">
    <div class="box-body">
        <?php if (!$model->isNewRecord) : ?>
            <?= Html::activeHiddenInput($model, 'id') ?>
        <?php endif ?>
        <?php if ($model->isNewRecord || !$model->isDefault()) : ?>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'name') ?>
                </div>
            </div>
        <?php endif ?>

        <?= Html::activeHiddenInput($model, 'client_id') ?>
        <?= Html::activeHiddenInput($model, 'client') ?>

        <?php foreach ($model->getDomainTariffTypes() as $type) : ?>
            <?php if (!Yii::getAlias('@' . $type, false)) : ?>
                <?php continue ?>
            <?php endif ?>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, $type)->widget(TariffCombo::class, [
                        'tariffType' => $type,
                        'hasId' => true,
                        'multiple' => false,
                        'client' => $client,
                    ]) ?>
                </div>
            </div>
        <?php endforeach ?>
        <?php if (Yii::getAlias('@server', false)) : ?>
            <?php foreach (array_chunk($model->getNotDomainTariffTypes(), 3) as $types) : ?>
                <div class="row">
                    <?php foreach ($types as $type) : ?>
                        <div class="col-md-4">
                            <?= $form->field($model, $type)->widget(TariffCombo::class, [
                                'type' => 'tariff/name/' . $type,
                                'name' => $type,
                                'current' => $model->tariffs[$type] ?? null,
                                'tariffType' => $type,
                                'client' => $client,
                                'hasId' => true,
                                'multiple' => true,
                            ]) ?>
                        </div>
                    <?php endforeach ?>
                </div>
            <?php endforeach ?>
        <?php endif ?>
    </div>
</div>

<?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
&nbsp;
<?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>

<?php ActiveForm::end() ?>
