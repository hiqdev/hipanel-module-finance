<?php

use hipanel\modules\finance\models\Plan;
use hipanel\modules\server\widgets\combo\ServerCombo;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

/**
 * @var Plan $plan
 */

$model = new \hipanel\modules\finance\models\PriceSuggestionRequestForm([
    'plan_id' => $plan->id,
]);
?>

<?php $form = ActiveForm::begin(['action' => ['@price/suggest'], 'method' => 'GET']) ?>

<?= $form->field($model, 'plan_id')->hiddenInput()->label(false) ?>

<?php if ($plan->type === Plan::TYPE_SERVER): ?>
    <?= $form->field($model, 'object_id')->widget(ServerCombo::class) ?>
    <?= $form->field($model, 'type')->widget(\hiqdev\combo\StaticCombo::class, [
        'data' => [
            'default' => Yii::t('hipanel.finance.suggestionTypes', 'default'),
            'parts' => Yii::t('hipanel.finance.suggestionTypes', 'parts'),
        ],
    ]) ?>
<?php elseif ($plan->type === Plan::TYPE_TEMPLATE): ?>
    <?= $form->field($model, 'object_id')->hiddenInput(['value' => $model->plan_id])->label(false) ?>
    <?= $form->field($model, 'type')->widget(\hiqdev\combo\StaticCombo::class, [
        'data' => [
            'model_groups' => Yii::t('hipanel.finance.suggestionTypes', 'model_groups'),
            'server_monthly' => Yii::t('hipanel.finance.suggestionTypes', 'server_monthly'),
        ],
    ]) ?>
<?php endif ?>

<hr/>

<?= Html::submitButton(Yii::t('hipanel.finance.price', 'Proceed to creation'), [
    'class' => 'btn btn-success',
]) ?>

<?php ActiveForm::end(); ?>

