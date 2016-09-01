<?php

/**
 * @var $this \yii\web\View
 * @var $model \hipanel\modules\finance\forms\VdsTariffForm
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
        <?= Html::activeHiddenInput($model, 'parent_id') ?>
        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'note') ?>
        <?= $form->field($model, 'label') ?>
    </div>
</div>
<?php Box::end() ?>

<div class="row">
    <div class="col-md-6">
        <?php Box::begin(['title' => Yii::t('hipanel/finance/tariff', 'Hardware')]) ?>
        <table class="table table-condensed">
            <thead>
            <tr>
                <th><?= Yii::t('hipanel/finance/tariff', 'Resource') ?></th>
                <th><?= Yii::t('hipanel/finance/tariff', 'Model') ?></th>
                <th><?= Yii::t('hipanel/finance/tariff', 'Price') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $package = $model->getPackage();
            foreach ($model->getHardwareResources() as $resource) {
                $baseResource = $model->getBaseHardwareResource($resource->object_id); ?>
                <tr>
                    <td><?= $package->getResourceTitle($resource->model_type) ?></td>
                    <td><?= $package->getResourceValue($resource->model_type) ?></td>
                    <td>
                        <?= Html::activeHiddenInput($resource, "[$i]object_id") ?>
                        <?= Html::activeHiddenInput($resource, "[$i]type") ?>
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $activeField = $form->field($resource, "[$i]fee");
                                Html::addCssClass($activeField->options, 'form-group-sm');
                                echo $activeField->input('number', [
                                    'class' => 'form-control price-input',
                                    'autocomplete' => false,
                                    'step' => 'any'
                                ])->label(false); ?>
                            </div>
                            <div class="col-md-6">
                                <?= Html::tag('span', '', [
                                    'class' => 'base-price text-bold',
                                    'data-original-price' => $baseResource->fee
                                ]); ?>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <?php Box::end() ?>
    </div>
    <div class="col-md-6">
        <?php Box::begin(['title' => Yii::t('hipanel/finance/tariff', 'Overuses')]) ?>
        <table class="table table-condensed">
            <thead>
            <tr>
                <th><?= Yii::t('hipanel/finance/tariff', 'Resource') ?></th>
                <th><?= Yii::t('hipanel/finance/tariff', 'Price') ?></th>
                <th><?= Yii::t('hipanel/finance/tariff', 'Prepaid amount') ?></th>
                <th><?= Yii::t('hipanel/finance/tariff', 'Overuse price') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $package = $model->getPackage();
            foreach ($model->getOveruseResources() as $resource) {
                $baseResource = $model->getBaseResource($resource->object_id); ?>
                <tr>
                    <td><?= $package->getResourceTitle($resource->type) ?></td>
                    <td>
                        <?= Html::activeHiddenInput($resource, "[$i]object_id") ?>
                        <?= Html::activeHiddenInput($resource, "[$i]type") ?>
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $activeField = $form->field($resource, "[$i]fee");
                                Html::addCssClass($activeField->options, 'form-group-sm');
                                echo $activeField->input('number', [
                                    'class' => 'form-control price-input',
                                    'autocomplete' => false,
                                    'step' => 'any'
                                ])->label(false); ?>
                            </div>
                            <div class="col-md-6">
                                <?= Html::tag('span', '', [
                                    'class' => 'base-price text-bold',
                                    'data-original-price' => $baseResource->fee
                                ]); ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?= $package->getResourceByType($resource->type)->quantity ?>
                    </td>
                    <td>
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $activeField = $form->field($resource, "[$i]price");
                                Html::addCssClass($activeField->options, 'form-group-sm');
                                echo $activeField->input('number', [
                                    'class' => 'form-control price-input',
                                    'autocomplete' => false,
                                    'step' => 'any'
                                ])->label(false); ?>
                            </div>
                            <div class="col-md-6">
                                <?= Html::tag('span', '', [
                                    'class' => 'base-price text-bold',
                                    'data-original-price' => $baseResource->price
                                ]); ?>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <?php Box::end() ?>
    </div>
    <div class="col-md-12">
    </div>
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

<?php

$this->registerJs(<<<JS
    $('.price-input').on('change mouseup', function () {
        var price = parseFloat($(this).val());
        if (isNaN(price)) return false;
        
        var base = $(this).closest('td').find('.base-price');
        var basePrice = parseFloat(base.attr('data-original-price'));
        var delta = price - basePrice;
        
        if (delta <= -basePrice) {
            $(this).val('0.01').trigger('change');
            return false;
        }
        
        base.removeClass('text-success text-danger');
        
        base.text(delta.toFixed(2)).addClass(delta >= 0 ? 'text-success' : 'text-danger');
    });

    $('.price-input').trigger('change');
JS
);

$this->registerCss('
.base-price { font-weight: bold; }
.form-group.form-group-sm { margin-bottom: 0; }
');

?>
