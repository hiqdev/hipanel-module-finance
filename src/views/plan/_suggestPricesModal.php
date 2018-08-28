<?php

use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\widgets\combo\TemplatePlanCombo;
use hipanel\modules\server\widgets\combo\ServerCombo;
use hiqdev\combo\StaticCombo;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var Plan $plan
 */

$model = new \hipanel\modules\finance\models\PriceSuggestionRequestForm([
    'plan_id' => $plan->id,
    'plan_type' => $plan->type,
]);
?>

<?php $form = ActiveForm::begin(['id' => 'create-prices', 'action' => ['@price/suggest'], 'method' => 'GET']) ?>

<?= $form->field($model, 'plan_id')->hiddenInput()->label(false) ?>

<?php if ($plan->type === Plan::TYPE_SERVER): ?>
    <?= $form->field($model, 'object_id')->widget(ServerCombo::class) ?>
    <?= $form->field($model, 'template_plan_id')->widget(TemplatePlanCombo::class, [
        'plan_id' => $plan->id,
        'object_input_type' => 'server/server'
    ]) ?>
    <?= $form->field($model, 'type')->widget(StaticCombo::class, [
        'data' => [
            'default' => Yii::t('hipanel.finance.suggestionTypes', 'default'),
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
    <?= $form->field($model, 'object_id')->widget(ServerCombo::class, [
        'filter' => ['type' => ['format' => $plan->type === Plan::TYPE_PCDN ? 'cdnpix' : 'cdn']]
    ]) ?>
    <?= $form->field($model, 'template_plan_id')->widget(TemplatePlanCombo::class, [
        'plan_id' => $plan->id,
        'object_input_type' => 'server/server'
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
    <h4>This plan does not support detailed prices</h4>
<?php endif ?>

<hr/>

<?= Html::submitButton(Yii::t('hipanel.finance.price', 'Proceed to creation'), [
    'class' => 'btn btn-success',
]) ?>

<?php ActiveForm::end(); ?>

