<?php

/**
 * @var \yii\web\View
 * @var string $scenario
 * @var array $countries
 * @var Contact $model the primary model
 * @var ActiveForm $form
 * @var EmployeeForm $employeeForm
 */
use hipanel\modules\client\forms\EmployeeForm;
use hipanel\modules\client\models\Contact;
use hipanel\modules\client\widgets\combo\ClientCombo;
use hipanel\widgets\BackButton;
use hipanel\widgets\Box;
use hiqdev\combo\StaticCombo;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$i = 0;
$contract = $employeeForm->getContract();
?>

<div class="row">
    <div class="col-md-12">
        <?php Box::begin(); ?>
            <?= Html::submitButton(Yii::t('hipanel', 'Save'), ['class' => 'btn btn-success']); ?>
            <?= BackButton::widget() ?>
        <?php Box::end(); ?>
        <?= Html::hiddenInput('pincode', null, ['id' => 'contact-pincode']) ?>
    </div>

    <?php foreach ($employeeForm->getContacts() as $language => $model) : ?>
        <div class="col-md-6">
            <?php Box::begin([
                'title' => Html::tag('span', $language, ['class' => 'label label-default']) . ' ' . Yii::t('hipanel:client', 'Contact details'),
            ]) ?>
                <?php if ($model->scenario === 'update') : ?>
                    <?= Html::activeHiddenInput($model, "[$i]id") ?>
                    <?= Html::activeHiddenInput($model, "[$i]localization") ?>
                <?php else: ?>
                    <?= $form->field($model, 'client_id')->widget(ClientCombo::class, [
                        'clientType' => 'employee',
                    ]); ?>
                <?php endif; ?>
                <?= $form->field($model, "[$i]first_name"); ?>
                <?= $form->field($model, "[$i]last_name"); ?>
                <?= $form->field($model, "[$i]email"); ?>
                <?= $form->field($model, "[$i]street1"); ?>
                <?= $form->field($model, "[$i]street2"); ?>
                <?= $form->field($model, "[$i]street3"); ?>
                <?= $form->field($model, "[$i]city"); ?>
                <?= $form->field($model, "[$i]country")->widget(StaticCombo::class, [
                    'inputOptions' => ['id' => 'country-' . $language . '-' . $model->client_id],
                    'data' => $countries,
                    'hasId' => true,
                ]); ?>
                <?= $form->field($model, "[$i]province"); ?>
                <?= $form->field($model, "[$i]postal_code"); ?>
                <?= $form->field($model, "[$i]voice_phone"); ?>
            <?php Box::end() ?>

            <?php Box::begin(['title' => Yii::t('hipanel:client', 'Bank details')]) ?>
            <fieldset id="bank_info">
                <?= $form->field($model, "[$i]vat_number") ?>
                <?= $form->field($model, "[$i]bank_account") ?>
                <?= $form->field($model, "[$i]bank_name") ?>
                <?= $form->field($model, "[$i]bank_address") ?>
                <?= $form->field($model, "[$i]bank_swift") ?>
            </fieldset>
            <?php Box::end() ?>
        </div>
    <?php $i++ ?>
    <?php endforeach; ?>
</div>
<?php if ($contract) : ?>
    <div class="row">
        <div class="col-md-6">
            <?php $box = Box::begin(['renderBody' => false]) ?>
                <?php $box->beginHeader() ?>
                    <?= $box->renderTitle(Yii::t('hipanel:client', 'Contract information')) ?>
                <?php $box->endHeader() ?>
                <?php $box->beginBody() ?>
                    <?php foreach ($employeeForm->getContractFields() as $name => $label) : ?>
                        <?= $form->field($contract, "data[$name]")->label($label) ?>
                    <?php endforeach; ?>
                <?php $box->endBody() ?>
            <?php $box->end(); ?>
        </div>
    </div>
<?php endif; ?>
