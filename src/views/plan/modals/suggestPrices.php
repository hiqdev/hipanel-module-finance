<?php

use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\PriceSuggestionRequestForm;
use hipanel\modules\finance\widgets\combo\TemplatePlanCombo;
use hipanel\modules\server\widgets\combo\ServerCombo;
use hiqdev\combo\StaticCombo;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var Plan $plan
 * @var PriceSuggestionRequestForm $model
 */

?>

<?php $form = ActiveForm::begin(['id' => 'create-prices', 'action' => ['@price/suggest'], 'method' => 'GET']) ?>

<?= $form->field($model, 'plan_id')->hiddenInput()->label(false) ?>

<?php
// TODO: think about splitting to multiple files, if files becomes monstrous.
// $this->render("../{$model->type}/suggestPricesModal", compact('form', 'model', 'plan'));
?>

<?php if ($plan->type === Plan::TYPE_SERVER): ?>
    <?php if ($model->isObjectPredefined()) : ?>
        <?= $form->field($model, 'object_id')->hiddenInput()->label(false) ?>
    <?php else : ?>
        <?= $form->field($model, 'object_id')->widget(ServerCombo::class) ?>
    <?php endif; ?>
    <?= $form->field($model, 'template_plan_id')->widget(TemplatePlanCombo::class, [
        'plan_id' => $plan->id,
        'object_input_type' => $model->isObjectPredefined() ? null : 'server/server',
    ]) ?>
    <?= $form->field($model, 'type')->widget(StaticCombo::class, [
        'data' => [
            'default' => Yii::t('hipanel.finance.suggestionTypes', 'default'),
            'services' => Yii::t('hipanel.finance.suggestionTypes', 'services'),
            'parts' => Yii::t('hipanel.finance.suggestionTypes', 'parts'),
        ],
    ]) ?>
<?php elseif ($plan->type === Plan::TYPE_TEMPLATE): ?>
    <?= $form->field($model, 'object_id')->hiddenInput(['value' => $model->plan_id])->label(false) ?>
    <?= $form->field($model, 'type')->widget(StaticCombo::class, [
        'data' => [
            'model_groups' => Yii::t('hipanel.finance.suggestionTypes', 'model_groups'),
            'dedicated_server' => Yii::t('hipanel.finance.suggestionTypes', 'dedicated_server'),
            'v_cdn' => Yii::t('hipanel.finance.suggestionTypes', 'v_cdn'),
            'p_cdn' => Yii::t('hipanel.finance.suggestionTypes', 'p_cdn'),
        ],
    ]) ?>
<?php elseif (in_array($plan->type, [Plan::TYPE_VCDN, Plan::TYPE_PCDN], true)): ?>
    <?php if ($model->isObjectPredefined()) : ?>
        <?= $form->field($model, 'object_id')->hiddenInput()->label(false) ?>
    <?php else : ?>
        <?= $form->field($model, 'object_id')->widget(ServerCombo::class, [
            'filter' => ['type' => ['format' => $plan->type === Plan::TYPE_PCDN ? 'cdnpix' : 'cdn']],
        ]) ?>
    <?php endif; ?>
    <?= $form->field($model, 'template_plan_id')->widget(TemplatePlanCombo::class, [
        'plan_id' => $plan->id,
        'object_input_type' => $model->isObjectPredefined() ? null : 'server/server',
    ]) ?>
    <?= $form->field($model, 'type')->widget(StaticCombo::class, [
        'data' => [
            'default' => Yii::t('hipanel.finance.suggestionTypes', 'default'),
        ],
    ]) ?>
<?php elseif (in_array($plan->type, [Plan::TYPE_CERTIFICATE, Plan::TYPE_DOMAIN], true)): ?>
    <?= $form->field($model, 'template_plan_id')->widget(TemplatePlanCombo::class, [
        'plan_id' => $plan->id,
    ]) ?>
    <?php $form->action = ['@plan/create-prices', 'id' => $plan->id]; ?>
<?php else: ?>
    <p class="text-center bg-warning"
       style="padding: 1rem;"><?= Yii::t('hipanel.finance.plan', 'This plan doesn\'t support detailed prices') ?></p>
<?php endif ?>

<?= Html::submitButton(Yii::t('hipanel.finance.price', 'Proceed to creation'), [
    'class' => 'btn btn-block btn-success',
]) ?>

<?php ActiveForm::end(); ?>
