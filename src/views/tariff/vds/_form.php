<?php

/**
 * @var $this \yii\web\View
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
        <?= Html::activeHiddenInput($model, 'parent_id') ?>
        <?= $form->field($model, 'name') ?>
        <?= $form->field($model, 'note') ?>
        <?= $form->field($model, 'label') ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <table class="table table-condensed">
            <thead>
            <tr>
                <th></th>
                <th><?= Yii::t('hipanel/finance/tariff', 'Resource') ?></th>
                <th><?= Yii::t('hipanel/finance/tariff', 'Model') ?></th>
                <th><?= Yii::t('hipanel/finance/tariff', 'Price') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($model->getHardwareResources() as $resource) { ?>
                <tr>
                    <td><?= $resource->name ?></td>
                    <?php foreach ($model->getZoneResources($zone) as $type => $resource) {
                        $baseResources = $model->getZoneBaseResources($zone); ?>
                        <td>
                            <?= Html::activeHiddenInput($resource, "[$i]object_id") ?>
                            <?= Html::activeHiddenInput($resource, "[$i]type") ?>
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
                                        'data-original-price' => $baseResources[$type]->price
                                    ]); ?>
                                </div>
                            </div>
                        </td>

                        <?php $i++;
                    } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php Box::end() ?>

<div class="row">
    <?php
    $services = $model->getServices();
    $baseServices = $model->getBaseServices();
    foreach ($services as $service) { ?>
        <div class="col-md-3">
            <?php Box::begin([
                'title' => $service->name
            ]) ?>
            <table class="table table-condensed">
                <thead>
                <tr>
                    <?php foreach ($service->getOperations() as $operation => $title) {
                        print Html::tag('td', $title);
                    } ?>
                </tr>
                <tbody>
                <tr>
                    <?php foreach ($service->getOperations() as $operation => $title) {
                        $resource = $service->getResource($operation);
                        ?>
                        <td>
                            <?= Html::activeHiddenInput($resource, "[$i]object_id") ?>
                            <?= Html::activeHiddenInput($resource, "[$i]type") ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <?php

                                    $activeField = $form->field($resource, "[$i]price");
                                    Html::addCssClass($activeField->options, 'form-group-sm');
                                    echo $activeField->input('number', [
                                        'class' => 'form-control price-input',
                                        'autocomplete' => false,
                                        'step' => 'any'
                                    ])->label(false)

                                    ?>
                                </div>
                                <div class="col-md-6">
                                    <?= Html::tag('span', '', [
                                        'class' => 'base-price text-bold',
                                        'data-original-price' => $baseServices[$service->type]->getResource($operation)->price
                                    ]); ?>
                                </div>
                            </div>
                        </td>
                        <?php
                        $i++;
                    } ?>
                </tr>
                </tbody>
                </thead></table>
            <?php Box::end(); ?>
        </div>
    <?php } ?>
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
