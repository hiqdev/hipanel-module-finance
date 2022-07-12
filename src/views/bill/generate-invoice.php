<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\assets\GenerateInvoiceAsset\GenerateInvoiceAsset;
use hipanel\modules\finance\forms\GenerateInvoiceForm;
use hipanel\modules\finance\widgets\combo\BillRequisitesCombo;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/** @var GenerateInvoiceForm $model */
/** @var array $billIds */

GenerateInvoiceAsset::register($this);

$this->title = Yii::t('hipanel:finance', 'Generate invoice');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div id="generate-invoice-app" class="row" v-cloak data-locale="<?= Yii::$app->language ?>">
    <div class="col-md-3">

        <?php $form = ActiveForm::begin([
            'id' => 'generate-invoice-form',
            'action' => Url::to(['@bill/generate-invoice']),
            'enableClientValidation' => true,
            'validateOnBlur' => false,
            'validateOnChange' => false,
            'enableAjaxValidation' => false,
        ]) ?>

        <div class="box box-widget">

            <div class="box-body">

                <?= Html::activeHiddenInput($model, 'purse_id') ?>
                <?= Html::activeHiddenInput($model, 'bill_ids') ?>

                <?= $form->field($model, 'requisite_id')->widget(BillRequisitesCombo::class) ?>
                <?= $form->field($model, 'month') ?>
                <?= $form->field($model, 'vat_rate')->input('number')->hint(Yii::t('hipanel:finance', 'If left blank, the default value will be taken')) ?>
                <?= $form->field($model, 'takeOutCharges')->checkbox() ?>

            </div>
            <div class="box-footer">
                <?= Html::submitButton(Yii::t('hipanel:finance', 'Prepare invoice'), ['class' => 'btn btn-default btn-block']) ?>

            </div>

            <div v-if="isLoading" class="overlay"></div>

        </div>

        <?php ActiveForm::end() ?>

        <?= Html::button(Yii::t('hipanel:finance', 'Show invoice'), [
            'class' => 'btn btn-success btn-block',
            'v-if' => 'isInvoicePrepared',
            '@click' => 'generateInvoice',
        ]) ?>

        <?= Html::button(Yii::t('hipanel:finance', 'Create document'), [
            'class' => 'btn btn-success btn-block',
            'v-if' => 'isInvoicePrepared',
            '@click' => 'generateInvoiceAndRouteToDocument',
        ]) ?>

    </div>
    <div class="col-md-9" v-if="isInvoicePrepared">
        <div class="box box-widget">
            <div class="box-body">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="number">Document number</label>
                            <input type="text" class="form-control" id="number"
                                   v-model="invoice.data.monthly_invoice.number">
                        </div>
                        <div class="form-group">
                            <label for="vat_number">VAT Number</label>
                            <input type="text" class="form-control" id="vat_number"
                                   v-model="invoice.data.contact.vat_number">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="prepared-for">Prepared for</label>
                            <input type="text" class="form-control" id="prepared-for"
                                   v-model="invoice.data.contact.name">
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" id="address" v-model="invoice.data.contact.address">
                        </div>
                    </div>
                </div>

            </div>

            <div class="box-footer" v-for="(item, idx) in invoice.data.monthly_invoice.items" :key="idx">
                <div class="row">

                    <div class="form-group col-md-9">
                        <input type="text" class="form-control" v-model.lasy="item.description">
                    </div>

                    <div class="form-group col-md-3">
                        <input type="text" class="form-control" v-model="item.value" disabled="disabled">
                    </div>

                </div>

            </div>

            <div class="box-footer">
                <dl class="dl-horizontal pull-right">
                    <dt>Total for {{ invoice.data.monthly_invoice.period.month }} {{
                        invoice.data.monthly_invoice.period.year }}
                    </dt>
                    <dd>{{ invoice.data.monthly_invoice.subtotal }}</dd>
                    <dt>Tax (VAT {{ invoice.data.vat_rate }}%)</dt>
                    <dd>{{ invoice.data.monthly_invoice.tax_value }}</dd>
                    <dt>TOTAL</dt>
                    <dd>{{ invoice.data.monthly_invoice.final_total }}</dd>
                </dl>
            </div>

            <div v-if="isLoading" class="overlay"></div>

        </div>
    </div>

</div>
