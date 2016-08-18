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
    'enableClientValidation' => true
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
            <?php
            $i = 0;
            foreach ($model->getZones() as $zone => $id) { ?>
                <tr>
                    <td><?= $zone ?></td>
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
                                        'step' => 0.1
                                    ])->label(false)

                                    ?>
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
        if (price === NaN) return false;
        
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
