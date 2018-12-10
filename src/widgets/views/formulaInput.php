<?php

use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\widgets\FormulaInput $widget
 */

$widget = $this->context;
?>

<div class="input-group">
    <?= Html::activeTextarea($widget->model, $widget->attribute, [
        'class' => 'form-control formula-input',
        'rows' => $widget->formulaLinesCount(),
    ]); ?>
    <span class="input-group-addon">
        <?= Html::button('', [
            'class' => 'fa fa-question-circle text-info formula-help-modal',
            'data-toggle' => 'modal',
            'data-target' => $widget->getHelpModalSelector(),
            'tabindex' => -1,
            'onClick' => new \yii\web\JsExpression(<<<JS
$($(this).data('target')).data('input', $(this).closest('.input-group').find('.formula-input'))
JS
            )
        ]) ?>
    </span>
</div>




