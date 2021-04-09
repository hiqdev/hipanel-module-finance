<?php

use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\widgets\ChangeFormulaButton;

/** @var Plan|null $plan */

$this->title = Yii::t('hipanel', 'Update');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Prices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">
            <?= $plan ? Yii::t('hipanel.finance.price', 'Tariff: {name}', ['name' => $plan->name]) : '' ?>
        </h3>
        <div class="box-tools pull-right">
            <?= ChangeFormulaButton::widget(['mod' => ChangeFormulaButton::MOD_ADD]) ?>
            <?= ChangeFormulaButton::widget(['mod' => ChangeFormulaButton::MOD_REPLACE]) ?>
        </div>
    </div>
    <div class="box-body">
        <?= $this->render('_form', compact('models', 'model')) ?>
    </div>
</div>
