<?php

/**
 * @var \yii\web\View
 * @var $model \hipanel\modules\finance\forms\CertificateTariffForm
 */
use hipanel\widgets\Box;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'id' => 'tariff-create-form',
]) ?>

<?php Box::begin() ?>
<div class="row">
    <div class="col-md-12">
        <?= Html::activeHiddenInput($model, 'id') ?>
        <?= $form->field($model, 'name') ?>
        <?= Html::activeHiddenInput($model, 'parent_id') ?>
    </div>
</div>

<?php if (isset($model->parentTariff)): ?>
<div class="row">
    <div class="col-md-12">
        <table class="table table-condensed">
            <thead>
            <tr>
                <th></th>
                <?php foreach ($model->getResourceTypes() as $type) : ?>
                    <?php foreach ($model->getPeriods() as $period => $periodLabel) : ?>
                        <?= Html::tag('th', Yii::t('hipanel:finance:tariff', '{operation} for {duration}', [
                            'operation' => $type,
                            'duration' => $periodLabel,
                        ])); ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <?php
            $i = 0;
            foreach ($model->getCertificateTypes() as $id => $certificateType) {
                ?>
                <tr>
                    <td><?= $certificateType ?></td>
                    <?php foreach ($model->getTypeResources($certificateType) as $type => $resource) : ?>
                        <?php $baseResources = $model->getTypeParentResources($certificateType); ?>
                        <?= Html::activeHiddenInput($resource, "[$i]object_id") ?>
                        <?= Html::activeHiddenInput($resource, "[$i]type") ?>
                        <?php foreach ($model->getPeriods() as $period => $periodLabel) : ?>
                            <td>
                                <?= \hipanel\modules\finance\widgets\ResourcePriceInput::widget([
                                    'basePrice' => $baseResources[$type]->getPriceForPeriod($period),
                                    'activeField' => $form->field($resource, "[$i]data[sums][$period]"),
                                ]) ?>
                            </td>
                        <?php endforeach; ?>
                        <?php ++$i; ?>
                    <?php endforeach; ?>
                </tr>
                <?php
            } ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif ?>
<?php Box::end() ?>

<?php Box::begin(['options' => ['class' => 'box-solid']]) ?>
<div class="row">
    <div class="col-md-12 no">
        <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
    </div>
</div>
<?php Box::end() ?>
<?php ActiveForm::end(); ?>
