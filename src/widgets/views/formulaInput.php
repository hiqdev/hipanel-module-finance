<?php

use yii\helpers\Html;

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\widgets\FormulaInput $widget
 */

$widget = $this->context;
?>

<?= Html::activeTextarea($widget->model, $widget->attribute, [
    'class' => 'form-control formula-input',
]); ?>




