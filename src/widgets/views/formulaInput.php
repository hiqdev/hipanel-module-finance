<?php

use yii\helpers\Html;
use yii\helpers\Json;

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\widgets\FormulaInput $widget
 */
$widget = $this->context;
?>

<?= Html::activeTextarea($widget->model, $widget->attribute, [
    'class' => 'form-control formula-input',
    'data-active-formula-lines' => json_encode(
        array_column($widget->model->getFormulaLines(), 'is_actual'),
        JSON_FORCE_OBJECT
    ),
]) ?>




