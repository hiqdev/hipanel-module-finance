<?php

/**
 * @var \yii\web\View
 * @var $model \hipanel\modules\finance\forms\DomainTariffForm
 */
use hipanel\modules\finance\models\Tariff;
use hipanel\widgets\Box;
use hipanel\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php Pjax::begin(['id' => 'tariff-pjax-container']) ?>

<?php $form = ActiveForm::begin(array_filter([
    'id' => 'tariff-create-form',
    'action' => isset($action) ? $action : null,
])) ?>

<?php Box::begin() ?>
<div class="row">
    <div class="col-md-12">
        <?= Html::activeHiddenInput($model, 'id') ?>
        <?= $form->field($model, 'parent_id')->widget(\hipanel\modules\finance\widgets\TariffCombo::class, [
            'tariffType' => Tariff::TYPE_DOMAIN,
            'inputOptions' => [
                'id' => 'tariff-parent_id',
                'data-url' => Url::current(['parent_id' => null]),
                'readonly' => isset($model->id),
            ],
        ]); ?>
        <?= $form->field($model, 'name') ?>
    </div>
</div>
<?php Box::end() ?>

<?php if (isset($model->parentTariff)): ?>
    <?php Box::begin() ?>
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
                                'basePrice' => $baseResources[$type]->price,
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
                                    'basePrice' => $baseServices[$service->type]->getResource($operation)->price,
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
<?php endif ?>


<?php Box::begin(['options' => ['class' => 'box-solid']]) ?>
<div class="row">
    <div class="col-md-12 no">
        <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
    </div>
</div>
<?php Box::end() ?>
<?php ActiveForm::end(); ?>

<?php
$this->registerJs(<<<'JS'
    $('#tariff-parent_id').on('change', function () {
        var fakeInput = $('<input>').attr({'name': 'parent_id', 'value': $(this).val()});
        var formAction = $(this).closest('select').attr('data-url');
        var fakeForm = $('<form>').attr({'method': 'get', 'action': formAction}).html(fakeInput).on('submit', function(event) {
            $.pjax.submit(event, '#tariff-pjax-container');
            event.preventDefault();
        }).trigger('submit');
    });
JS
); ?>

<?php Pjax::end(); ?>
