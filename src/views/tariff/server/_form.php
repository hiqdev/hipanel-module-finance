<?php

/**
 * @var \yii\web\View
 * @var $model \hipanel\modules\finance\forms\ServerTariffForm
 */
use hipanel\helpers\Url;
use hipanel\widgets\Box;
use hipanel\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php
Pjax::begin(['id' => 'tariff-pjax-container']);
$form = ActiveForm::begin([
    'id' => 'tariff-create-form',
    'action' => $action,
]);
$i = 0;
?>

<?php Box::begin(['options' => ['class' => 'box-solid']]) ?>
<div class="row">
    <div class="col-md-12 no">
        <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']) ?>
        <?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>
    </div>
</div>
<?php Box::end() ?>

<?php Box::begin() ?>
<div class="row">
    <div class="col-md-12">
        <?= Html::activeHiddenInput($model, 'id') ?>
        <?= Html::activeHiddenInput($model, 'parent_id') ?>
        <?= $form->field($model, 'parent_id')->widget(\hipanel\modules\finance\widgets\TariffCombo::class, [
            'tariffType' => $model->getTariff()->type,
            'inputOptions' => [
                'id' => 'tariff-parent_id',
                'data-url' => Url::current(['parent_id' => null]),
                'readonly' => isset($model->id),
            ],
        ]); ?>
        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'note') ?>
        <?= $form->field($model, 'label') ?>
    </div>
</div>
<?php Box::end() ?>

<?php if (isset($model->parentTariff)): ?>
<div class="row">
    <?php if (!empty($model->getHardwareResources())) : ?>
        <div class="col-md-4">
            <?php Box::begin(['title' => Yii::t('hipanel:finance:tariff', 'Hardware')]) ?>
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th><?= Yii::t('hipanel:finance:tariff', 'Resource') ?></th>
                    <th><?= Yii::t('hipanel:finance:tariff', 'Model') ?></th>
                    <th><?= Yii::t('hipanel:finance:tariff', 'Price per period') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($model->getHardwareResources() as $resource) : ?>
                    <tr>
                        <td><?= $resource->decorator()->displayTitle() ?></td>
                        <td><?= $resource->decorator()->displayPrepaidAmount() ?></td>
                        <td>
                            <?= Html::activeHiddenInput($resource, "[$i]object_id", [
                                'value' => $resource->realObjectId(),
                            ]) ?>
                            <?= Html::activeHiddenInput($resource, "[$i]type") ?>
                            <?= \hipanel\modules\finance\widgets\ResourcePriceInput::widget([
                                'basePrice' => $model->getParentHardwareResource($resource->object_id)->fee,
                                'activeField' => $form->field($resource, "[$i]fee"),
                            ]) ?>
                        </td>
                    </tr>
                    <?php $i++; ?>
                <?php endforeach ?>
                </tbody>
            </table>
            <?php Box::end() ?>
        </div>
    <?php endif ?>
    <div class="col-md-8">
        <?php Box::begin(['title' => Yii::t('hipanel:finance:tariff', 'Resources')]) ?>
        <table class="table table-condensed">
            <thead>
            <tr>
                <th><?= Yii::t('hipanel:finance:tariff', 'Resource') ?></th>
                <th><?=  Yii::t('hipanel:finance:tariff', 'Unit') ?></th>
                <th><?= Yii::t('hipanel:finance:tariff', 'Price per period') ?></th>
                <th><?= Yii::t('hipanel:finance:tariff', 'Prepaid amount') ?></th>
                <th><?= Yii::t('hipanel:finance:tariff', 'Overuse price') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model->getOrFakeOveruseResources() as $resource) : ?>
                <tr>
                    <?php $baseResource = $model->getParentOveruseResource($resource->type_id) ?>
                    <td><?= $resource->decorator()->displayTitle() ?></td>
                    <td>
                        <?= \hipanel\modules\finance\widgets\ResourceUnitWidget::widget([
                            'activeField' => $form->field($resource, "[$i]unit")->label(false),
                            'resource' => $resource,
                        ]) ?>
                    </td>
                    <td style="width: 20%">
                        <?= Html::activeHiddenInput($resource, "[$i]object_id") ?>
                        <?= Html::activeHiddenInput($resource, "[$i]type") ?>
                        <?= \hipanel\modules\finance\widgets\ResourcePriceInput::widget([
                            'basePrice' => floatval($baseResource->fee),
                            'activeField' => $form->field($resource, "[$i]fee")
                        ]) ?>
                    </td>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $activeField = $form->field($resource, "[$i]quantity")->label(false);

                                echo \hipanel\modules\finance\widgets\PrepaidAmountWidget::widget([
                                    'activeField' => $activeField,
                                    'resource' => $resource,
                                ]); ?>
                            </div>
                            <div class="col-md-6">
                                <?= Html::tag('span', '', [
                                    'class' => 'base-price text-bold',
                                    'data-original-price' => 0//$baseResource->decorator()->getPrepaidQuantity(),
                                ]); ?>
                            </div>
                        </div>
                        <?php
                        ?>
                    </td>
                    <td>
                        <?= \hipanel\modules\finance\widgets\ResourcePriceInput::widget([
                            'basePrice' => $baseResource->price,
                            'activeField' => $form->field($resource, "[$i]price"),
                        ]) ?>
                    </td>
                </tr>
                <?php $i++; ?>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php Box::end() ?>
    </div>
</div>
<?php endif ?>

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
);

Pjax::end();
?>
