<?php

/**
 * @var $this \yii\web\View
 * @var $model \hipanel\modules\finance\forms\VdsTariffForm
 */

use hipanel\helpers\Url;
use hipanel\widgets\Box;
use hipanel\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>

<?php
Pjax::begin(['id' => 'tariff-pjax-container']);
$form = ActiveForm::begin(['id' => 'tariff-create-form']) ?>

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
        <?= $form->field($model, 'parent_id')->dropDownList($model->getParentTariffsList(), [
            'id' => 'tariff-parent_id',
            'data-url' => Url::current(['parent_id' => null]),
            'readonly' => isset($model->id)
        ]); ?>
        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'note') ?>
        <?= $form->field($model, 'label') ?>
    </div>
</div>
<?php Box::end() ?>

<div class="row">
    <div class="col-md-4">
        <?php Box::begin(['title' => Yii::t('hipanel/finance/tariff', 'Hardware')]) ?>
        <table class="table table-condensed">
            <thead>
            <tr>
                <th><?= Yii::t('hipanel/finance/tariff', 'Resource') ?></th>
                <th><?= Yii::t('hipanel/finance/tariff', 'Model') ?></th>
                <th><?= Yii::t('hipanel/finance/tariff', 'Price per period') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i = 0;
            foreach ($model->getHardwareResources() as $resource) {
                $baseResource = $model->getParentHardwareResource($resource->object_id); ?>
                <tr>
                    <td><?= $resource->decorator()->displayTitle() ?></td>
                    <td><?= $resource->decorator()->displayPrepaidAmount() ?></td>
                    <td>
                        <?= Html::activeHiddenInput($resource, "[$i]object_id", [
                            'value' => $resource->realObjectId()
                        ]) ?>
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
                                <?php
                                echo Html::tag('span', '', [
                                    'class' => 'base-price text-bold',
                                    'data-original-price' => $baseResource->fee
                                ]); ?>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php $i++;
            } ?>
            </tbody>
        </table>
        <?php Box::end() ?>
    </div>
    <div class="col-md-8">
        <?php Box::begin(['title' => Yii::t('hipanel/finance/tariff', 'Resources')]) ?>
        <table class="table table-condensed">
            <thead>
            <tr>
                <th><?= Yii::t('hipanel/finance/tariff', 'Resource') ?></th>
                <th><?= Yii::t('hipanel/finance/tariff', 'Price per period') ?></th>
                <th><?= Yii::t('hipanel/finance/tariff', 'Prepaid amount') ?></th>
                <th><?= Yii::t('hipanel/finance/tariff', 'Overuse price') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($model->getOveruseResources() as $resource) {
                $baseResource = $model->getParentOveruseResource($resource->type_id); ?>
                <tr>
                    <td><?= $resource->decorator()->displayTitle() ?></td>
                    <td style="width: 20%">
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
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                $activeField = $form->field($resource, "[$i]quantity")->label(false);

                                echo \hipanel\modules\finance\widgets\PrepaidAmountWidget::widget([
                                    'activeField' => $activeField,
                                    'resource' => $resource
                                ]);
                                ?>
                            </div>
                            <div class="col-md-6">
                                <?= Html::tag('span', '', [
                                    'class' => 'base-price text-bold',
                                    'data-original-price' => $baseResource->decorator()->getPrepaidQuantity()
                                ]); ?>
                            </div>
                        </div>
                        <?php
                        ?>
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
            <?php $i++;
            } ?>
            </tbody>
        </table>

        <?php Box::end() ?>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php
$this->registerJs(<<<JS
    $('#tariff-parent_id').on('change', function () {
        var fakeInput = $('<input>').attr({'name': 'parent_id', 'value': $(this).val()});
        var formAction = $(this).closest('select').attr('data-url');
        var fakeForm = $('<form>').attr({'method': 'get', 'action': formAction}).html(fakeInput).on('submit', function(event) {
            $.pjax.submit(event, '#tariff-pjax-container');
            event.preventDefault();
        }).trigger('submit');     
    });

    $('.price-input').on('change mouseup', function () {
        var base = $(this).closest('td').find('.base-price');
        var price = parseFloat($(this).val());
        var basePrice = parseFloat(base.attr('data-original-price'));
        
        if (isNaN(price)) return false;
        if (isNaN(basePrice)) basePrice = 0;
       
        var delta = price - basePrice;
        if (delta < -basePrice) {
            $(this).val('0').trigger('change');
            return false;
        }
        
        base.removeClass('text-success text-danger')
            .text(delta.toFixed(2))
            .addClass(delta >= 0 ? 'text-success' : 'text-danger');
    });

    $('.price-input').trigger('change');
JS
);

Pjax::end();

$this->registerCss('
.base-price { font-weight: bold; }
.form-group.form-group-sm { margin-bottom: 0; }
');

?>
