<?php

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var float $basePrice
 * @var \yii\widgets\ActiveField $activeField
 * @var float $minPrice
 */

?>

<div class="row">
    <div class="col-md-6">
        <?php
        Html::addCssClass($activeField->options, 'form-group-sm');
        echo $activeField->input('number', [
            'class' => 'form-control price-input',
            'autocomplete' => false,
            'step' => 'any',
            'data' => [
                'min-price' => $minPrice
            ],
            'value' => $basePrice
        ])->label(false); ?>
    </div>
    <div class="col-md-6">
        <?= Html::tag('span', '', [
            'class' => 'base-price text-bold',
            'data-original-price' => $basePrice,
        ]); ?>
    </div>
</div>
