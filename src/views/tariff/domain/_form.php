<?php

/**
 * @var \yii\web\View
 * @var $model \hipanel\modules\finance\forms\DomainTariffForm
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

<div class="row">
    <div class="col-md-12">
        <table class="table table-condensed">
            <thead>
            <tr>
                <th></th>
                <?php foreach ($model->getResourceTypes() as $type) {
                    echo Html::tag('th', $type);
                } ?>
            </tr>
            </thead>
            <tbody>
            <?php $i = 0 ?>
            <?php foreach ($model->getZones() as $zone => $id) : ?>
                <tr>
                    <td><?= $zone ?></td>
                    <?php $baseResources = $model->getZoneParentResources($zone); ?>
                    <?php foreach ($model->getZoneResources($zone) as $type => $resource) : ?>
                    <td>
                        <?= Html::activeHiddenInput($resource, "[$i]object_id") ?>
                        <?= Html::activeHiddenInput($resource, "[$i]type") ?>

                        <?= \hipanel\modules\finance\widgets\ResourcePriceInput::widget([
                            'resource' => $resource,
                            'baseResource' => $baseResources[$type],
                            'activeField' => $form->field($resource, "[$i]price"),
                        ]) ?>
                        <?php ++$i; ?>
                        <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php Box::end() ?>

<div class="row">
    <?php $services = $model->getServices(); ?>
    <?php $baseServices = $model->getParentServices(); ?>
    <?php foreach ($services as $service) : ?>
        <div class="col-md-3">
            <?php Box::begin(['title' => $service->name]) ?>
            <table class="table table-condensed">
                <thead>
                <tr>
                    <?php foreach ($service->getOperations() as $operation => $title) : ?>
                        <?= Html::tag('td', $title) ?>
                    <?php endforeach; ?>
                </tr>
                <tbody>
                <tr>
                    <?php foreach ($service->getOperations() as $operation => $title) : ?>
                        <?php $resource = $service->getResource($operation); ?>
                        <td>
                            <?= Html::activeHiddenInput($resource, "[$i]object_id") ?>
                            <?= Html::activeHiddenInput($resource, "[$i]type") ?>
                            <?= \hipanel\modules\finance\widgets\ResourcePriceInput::widget([
                                'resource' => $resource,
                                'baseResource' => $baseServices[$service->type]->getResource($operation),
                                'activeField' => $form->field($resource, "[$i]price"),
                            ]) ?>
                        </td>
                        <?php ++$i; ?>
                    <?php endforeach ?>
                </tr>
                </tbody>
                </thead></table>
            <?php Box::end(); ?>
        </div>
    <?php endforeach; ?>
</div>


<?php Box::begin(['options' => ['class' => 'box-solid']]) ?>
<div class="row">
    <div class="col-md-12 no">
        <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
    </div>
</div>
<?php Box::end() ?>
<?php ActiveForm::end(); ?>
