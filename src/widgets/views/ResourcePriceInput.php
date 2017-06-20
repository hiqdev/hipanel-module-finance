<?php

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var \hipanel\modules\finance\models\CertificateResource $resource
 * @var \hipanel\modules\finance\models\CertificateResource $baseResource
 * @var \yii\widgets\ActiveField $activeField
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
        ])->label(false); ?>
    </div>
    <div class="col-md-6">
        <?= Html::tag('span', '', [
            'class' => 'base-price text-bold',
            'data-original-price' => $baseResource->{Html::getAttributeName($activeField->attribute)},
        ]); ?>
    </div>
</div>
