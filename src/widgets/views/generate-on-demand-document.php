<?php

use hipanel\modules\finance\forms\PrepareOnDemandDocumentForm;
use hipanel\modules\finance\widgets\BillType;
use hipanel\widgets\DatePicker;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/** @var PrepareOnDemandDocumentForm $model */
/** @var array $chargeGroups  [key => ['client' => string, 'currency' => string, 'charges' => array]] */
/** @var string $formAction */
/** @var string $saveUrl */
/** @var string $previewUrl */
/** @var \yii\web\View $this */

$documentTypes = [
    'invoice'                      => Yii::t('hipanel:finance', 'Invoice'),
    'payment_request'              => Yii::t('hipanel:finance', 'Payment request'),
    'service_invoice'              => Yii::t('hipanel:finance', 'Service invoice'),
    'purchase_invoice'             => Yii::t('hipanel:finance', 'Purchase invoice'),
    'installment_invoice'          => Yii::t('hipanel:finance', 'Installment invoice'),
    'payment_plan_payment_request' => Yii::t('hipanel:finance', 'Payment plan'),
];

$allChargeIds = [];
foreach ($chargeGroups as $group) {
    foreach ($group['charges'] as $charge) {
        if (empty($charge->included_in_documents)) {
            $allChargeIds[] = (int) $charge->id;
        }
    }
}

$colgroup = '<colgroup>'
    . '<col style="width:28%">'   // Object
    . '<col style="width:24%">'   // Type
    . '<col style="width:8%">'    // Description
    . '<col style="width:8%">'    // Qty
    . '<col style="width:7%">'    // Unit
    . '<col style="width:12%">'   // Date
    . '<col style="width:13%">'   // Sum
    . '</colgroup>';

?>

<?php $this->registerCss(
    '#generate-on-demand-document-app table[style*="table-layout"] td { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }' .
    '.flex-space-beetween { display:flex; flex-wrap:nowrap; gap:1rem; justify-content:space-between; }' .
    '.align-center { align-items:center; }'
) ?>

<div id="generate-on-demand-document-app"
     class="row"
     v-cloak
     data-locale="<?= Yii::$app->language ?>"
     data-save-url="<?= Html::encode($saveUrl) ?>"
     data-preview-url="<?= Html::encode($previewUrl) ?>"
     data-prepare-url="<?= Html::encode($formAction) ?>"
     data-charge-ids="<?= Html::encode(json_encode($allChargeIds)) ?>">

    <?php $form = ActiveForm::begin([
        'id'                     => 'generate-on-demand-document-form',
        'enableClientValidation' => false,
        'enableAjaxValidation'   => false,
    ]) ?>

    <?php /* Form controls */ ?>
    <div class="col-md-12">
        <div class="box box-widget">
            <div class="box-body">
                <div class="row">
                    <div class="col-sm-4">
                        <?= $form->field($model, 'type')->dropDownList($documentTypes) ?>
                    </div>
                    <div class="col-sm-3">
                        <?= $form->field($model, 'date')->widget(DatePicker::class, [
                            'clientOptions' => ['maxDate' => 'today'],
                        ])->hint(Yii::t('hipanel:finance', 'Leave blank to use today\'s date')) ?>
                    </div>
                    <div class="col-sm-5 text-right" style="padding-top: 25px">
                        <button type="button" class="btn btn-primary" @click="prepareDocument" :disabled="isLoading">
                            <i v-if="isLoading" class="fa fa-spin fa-spinner"></i>
                            <?= Yii::t('hipanel:finance', 'Prepare document') ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end() ?>

    <?php /* Section: charges eligible for generation alert */ ?>
    <div class="col-md-12" v-if="wasPrepared && !isPrepared">
        <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle"></i>
            <?= Yii::t('hipanel:finance', 'None of the selected charges are eligible for the chosen document type and date.') ?>
        </div>
    </div>

    <?php /* Section: selected charges */ ?>
    <div class="col-md-12">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('hipanel:finance', 'Selected charges') ?></h3>
            </div>
            <div class="box-body" style="padding: 10px 10px 0;">

                <?php foreach ($chargeGroups as $group):
                    $available   = array_values(array_filter($group['charges'], static fn($c) => empty($c->included_in_documents)));
                    $inDocuments = array_values(array_filter($group['charges'], static fn($c) => !empty($c->included_in_documents)));
                ?>
                <div class="box box-widget">
                    <div class="box-header with-border" style="background-color: #d2e8f5;">
                        <h3 class="box-title">
                            <strong><?= Html::encode($group['client']) ?></strong> &mdash; <?= Html::encode($group['currency']) ?>
                            <small class="text-muted">
                                (<?= count($available) ?> <?= Yii::t('hipanel:finance', 'available') ?><?php if ($inDocuments): ?>,
                                <?= count($inDocuments) ?> <?= Yii::t('hipanel:finance', 'not eligible') ?><?php endif ?>)
                            </small>
                        </h3>
                    </div>
                    <?php if (!empty($available)): ?>
                    <div class="box-body no-padding">
                        <table class="table table-condensed table-striped" style="table-layout:fixed">
                            <?= $colgroup ?>
                            <thead>
                                <tr>
                                    <th><?= Yii::t('hipanel', 'Object') ?></th>
                                    <th><?= Yii::t('hipanel:finance', 'Type') ?></th>
                                    <th><?= Yii::t('hipanel:finance', 'Description') ?></th>
                                    <th><?= Yii::t('hipanel:finance', 'Qty') ?></th>
                                    <th><?= Yii::t('hipanel:finance', 'Unit') ?></th>
                                    <th><?= Yii::t('hipanel:finance', 'Date') ?></th>
                                    <th class="text-right"><?= Yii::t('hipanel:finance', 'Sum') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($available as $charge): ?>
                                <tr data-charge-id="<?= (int) $charge->id ?>">
                                    <td><?= Html::encode($charge->name ?? '') ?></td>
                                    <td><?= BillType::widget(['model' => $charge, 'field' => 'ftype', 'labelField' => 'type_label']) ?></td>
                                    <td><?= Html::encode($charge->label ?? '') ?></td>
                                    <td><?= Html::encode($charge->quantity ?? '') ?></td>
                                    <td><?= Html::encode($charge->unit ?? '') ?></td>
                                    <td><?= Html::encode($charge->time ?? '') ?></td>
                                    <td class="text-right"><?= Html::encode($charge->sum ?? '') ?></td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif ?>
                    <?php if (!empty($inDocuments)): ?>
                    <div class="box-header" style="background:#f9f9f9; border-top:1px solid #f0f0f0">
                        <h4 class="box-title text-muted" style="font-size:13px">
                            <?= Yii::t('hipanel:finance', 'Not eligible for this document type') ?>
                        </h4>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-condensed" style="table-layout:fixed;opacity:.6">
                            <?= $colgroup ?>
                            <tbody>
                                <?php foreach ($inDocuments as $charge): ?>
                                <tr class="active">
                                    <td><?= Html::encode($charge->name ?? '') ?></td>
                                    <td><?= BillType::widget(['model' => $charge, 'field' => 'ftype', 'labelField' => 'type_label']) ?></td>
                                    <td><?= Html::encode($charge->label ?? '') ?></td>
                                    <td><?= Html::encode($charge->quantity ?? '') ?></td>
                                    <td><?= Html::encode($charge->unit ?? '') ?></td>
                                    <td><?= Html::encode($charge->time ?? '') ?></td>
                                    <td class="text-right"><?= Html::encode($charge->sum ?? '') ?></td>
                                </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif ?>
                </div>
                <?php endforeach ?>

            </div>
        </div>
    </div>

    <div class="col-md-12" v-if="isPrepared && groupsWithState.length > 0">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('hipanel:finance', 'Charges for document generation') ?></h3>
            </div>
            <div class="box-body" style="padding: 10px 10px 0;">

                <div v-for="group in groupsWithState" :key="group.groupKey" class="box box-widget">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <strong>{{ group.purse.client }}</strong>
                            &mdash; {{ group.purse.currency }}
                            <span v-if="group.location"> &mdash; {{ group.location }}</span>
                            &mdash; {{ group.charges.length }} <?= Yii::t('hipanel:finance', 'charge(s)') ?>
                            <small class="text-muted">#{{ group.purseId }}</small>
                        </h3>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-condensed table-striped" style="table-layout:fixed">
                            <?= $colgroup ?>
                            <thead>
                                <tr>
                                    <th><?= Yii::t('hipanel', 'Object') ?></th>
                                    <th><?= Yii::t('hipanel:finance', 'Type') ?></th>
                                    <th><?= Yii::t('hipanel:finance', 'Description') ?></th>
                                    <th><?= Yii::t('hipanel:finance', 'Qty') ?></th>
                                    <th><?= Yii::t('hipanel:finance', 'Unit') ?></th>
                                    <th><?= Yii::t('hipanel:finance', 'Date') ?></th>
                                    <th class="text-right"><?= Yii::t('hipanel:finance', 'Sum') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="charge in group.charges" :key="charge.id">
                                    <td>{{ charge.name }}</td>
                                    <td>
                                        <span class="flex-space-beetween align-center">
                                            <span>{{ charge.type_label || charge.type }}</span>
                                            <span :class="'label label-' + typeClass(charge.type)">{{ charge.gtype }}</span>
                                        </span>
                                    </td>
                                    <td>{{ stripTags(charge.description) }}</td>
                                    <td>{{ charge.quantity }}</td>
                                    <td>{{ charge.unit }}</td>
                                    <td>{{ charge.time }}</td>
                                    <td class="text-right">{{ formatSum(charge.sum, group.purse.currency) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="box-footer clearfix">
                        <a v-if="group.documentUrl"
                           :href="group.documentUrl"
                           target="_blank"
                           class="btn btn-default pull-left">
                            <?= Yii::t('hipanel:finance', 'View document') ?>
                        </a>
                        <div class="pull-right">
                            <button type="button"
                                    class="btn btn-default"
                                    :disabled="group.previewing || isSavingAll"
                                    @click="previewGroup(group)">
                                <i v-if="group.previewing" class="fa fa-spin fa-spinner"></i>
                                <?= Yii::t('hipanel:finance', 'Preview') ?>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="box-footer clearfix">
                <div class="pull-right">
                    <button type="button"
                            class="btn"
                            :class="allSaved ? 'btn-default' : 'btn-success'"
                            :disabled="isSavingAll || allSaved"
                            @click="saveAll">
                        <i v-if="isSavingAll" class="fa fa-spin fa-spinner"></i>
                        <span v-if="allSaved"><?= Yii::t('hipanel:finance', 'Saved') ?></span>
                        <span v-else><?= Yii::t('hipanel:finance', 'Generate & Save') ?></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
