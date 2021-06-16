<?php

/**
 * @var \yii\web\View
 * @var \hipanel\modules\finance\forms\BillImportForm $model
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = Yii::t('hipanel:finance', 'Import payments');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-4">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('hipanel:finance', 'How to import payments') ?></h3>
            </div>
            <div class="box-body text-justify">
                <?= Yii::t('hipanel:finance',
    '<p>Use the following format: <pre>Client;Time;Amount;Currency;Type;Description;Requisite</pre>Each payment must be placed on new line.</p>
<p><span class="label label-default">Time</span> can be either: <ul><li><code>this</code> (alias <code>thisMonth</code>) &ndash; first day of this month</li><li><code>prev</code> (alias <code>prevMonth</code>) &ndash; first day of previous month</li><li>date or date with time (for example <code>2016-11-01</code>, <code>01.11.2016 10:20:30</code>)</li></ul></p>
<p><span class="label label-default">Type</span> can be either: <ul><li>a full type notation (<code>deposit,webmoney</code>, <code>deposit,wmdirect</code>)</li><li>short type notation (<code>webmoney</code>, <code>server_traf95_max</code>)</li><li>simply a type name (<code>Partner invoice</code>, <code>WebMoney.Merchant</code>)</li></ul></p>
<p><span class="label label-default">Requisite</span> is a full name of requisite</p>
<p>After the <span class="label label-success">Import</span> button pressing, you will be redirected to the payments creation page to verify and confirm payments.</p>') ?>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="bill-import">
            <?php $form = ActiveForm::begin() ?>
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= $model->getAttributeLabel('data') ?></h3>
                </div>
                <div class="box-body">
                    <div class="form-instance">
                        <?= $form->field($model, 'data')->textarea(['rows' => 20])->label(false) ?>
                    </div>
                </div>
            </div>
            <?= Html::submitButton(Yii::t('hipanel:finance', 'Import'), ['class' => 'btn btn-success btn-flat']) ?>
            &nbsp;
            <?= Html::button(Yii::t('hipanel', 'Cancel'),
                ['class' => 'btn btn-default btn-flat', 'onclick' => 'history.go(-1)']) ?>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>
