<?php

use hipanel\assets\BootstrapDatetimepickerAsset;
use hipanel\helpers\Url;
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\modules\client\widgets\combo\SellerCombo;
use hipanel\modules\finance\models\Sale;
use hipanel\modules\finance\widgets\combo\PlanCombo;
use hipanel\widgets\combo\ObjectCombo;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/** @var Sale $model */
/** @var Sale[] $models */

BootstrapDatetimepickerAsset::register($this);

$this->registerJs(/** @lang ECMAScript 6 */ "
$('.datetime').datetimepicker({
    locale: hipanel.locale.get(),
    format: 'YYYY-MM-DD HH:mm:SS',
    showClose: true,
    showTodayButton: true
});");

$this->registerCss(/** @lang CSS */ "
.sales tbody tr td { align-items: center; vertical-align: middle; }
.sales tbody tr td .form-group { position: relative; padding-top: 1.5rem; }
.sales tbody tr td .form-group .help-block-error { position: absolute; top: 4.5rem; }
");

?>

<?php $form = ActiveForm::begin([
    'id' => 'sale-form',
    'enableClientValidation' => true,
    'validateOnBlur' => true,
    'enableAjaxValidation' => true,
    'validationUrl' => Url::toRoute(['validate-form', 'scenario' => $model->scenario]),
]) ?>

<div class="box box-widget sales">
    <div class="box-body">
        <table class="table table-condensed no-margin">
            <thead>
            <tr>
                <th><?= Yii::t('hipanel:finance:sale', 'Object') ?></th>
                <th><?= Yii::t('hipanel:finance:sale', 'Seller') ?></th>
                <th><?= Yii::t('hipanel:finance:sale', 'Buyer') ?></th>
                <th><?= Yii::t('hipanel:finance:sale', 'Tariff') ?></th>
                <th><?= Yii::t('hipanel:finance:sale', 'Time') ?></th>
                <?php if (Yii::$app->user->can('sale.update')) : ?>
                    <th><?= Yii::t('hipanel:finance:sale', 'Close time') ?></th>
                    <th><?= Yii::t('hipanel', 'Ticket') ?></th>
                <?php endif ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($models as $idx => $sale) : ?>
                <tr>
                    <?php if ($model->isNewRecord) : ?>
                        <td>
                            <?= $form->field($model, "[$idx]object_id")->widget(ObjectCombo::class, [
                                'class_attribute' => "object_type",
                                'class_attribute_name' => "[$idx]object_type",
                            ])->label(false) ?>
                        </td>
                        <td>
                            <?= $form->field($sale, "[$idx]seller_id")->widget(SellerCombo::class, [
                                'hasId' => true,
                                'formElementSelector' => 'td',
                            ])->label(false) ?>
                        </td>
                        <td>
                            <?= $form->field($sale, "[$idx]buyer_id")->widget(ClientCombo::class, [
                                'hasId' => true,
                                'formElementSelector' => 'td',
                            ])->label(false) ?>
                        </td>
                    <?php else : ?>
                        <?= Html::activeHiddenInput($sale, "[$idx]id") ?>
                        <td><?= $sale->object ?></td>
                        <td><?= $sale->seller ?></td>
                        <td><?= $sale->buyer ?></td>
                    <?php endif ?>
                    <td>
                        <?= $form->field($sale, "[$idx]tariff_id")->widget(PlanCombo::class, [
                            'hasId' => true,
                            'tariffType' => $sale->tariff_type,
                        ])->label(false) ?>
                    </td>
                    <td>
                        <?= $form->field($sale, "[$idx]time")->textInput(['class' => 'form-control datetime'])->label(false) ?>
                    </td>
                    <?php if (Yii::$app->user->can('sale.update')) : ?>
                        <td>
                            <?= $form->field($sale, "[$idx]unsale_time")
                                ->textInput(['class' => 'form-control datetime'])
                                ->label(false)
                            ?>
                        </td>
                        <td>
                            <?= $form->field($model, "[$idx]ticket_id")
                                ->textInput()
                                ->label(false)
                            ?>
                        </td>
                    <?php endif ?>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<?= Html::submitButton(Yii::t('hipanel:finance:sale', 'Save'), ['class' => 'btn btn-success']) ?>
&nbsp;
<?= Html::button(Yii::t('hipanel', 'Cancel'), ['class' => 'btn btn-default', 'onclick' => 'history.go(-1)']) ?>

<?php ActiveForm::end() ?>
