<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\widgets\AddExtraPricesButton;
use hipanel\modules\finance\widgets\ChangeFormulaButton;
use yii\bootstrap\Alert;
use yii\helpers\Html;

/** @var Plan $plan */
/** @var Price[] $models */
/** @var Price $model */
/** @var string $type */
/** @var bool $canAddExtraPrices */

$this->title = Yii::t('hipanel.finance.price', 'Create suggested prices');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Tariff plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $plan->name, 'url' => ['@plan/view', 'id' => $plan->id]];
$this->params['breadcrumbs'][] = $this->title;

$canAddExtraPrices = in_array($type, ['calculator_private_cloud', 'calculator_public_cloud'], true);
?>


<div class="box box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">
            <?= $plan ? Yii::t('hipanel.finance.price', 'Tariff: {name}', ['name' => $plan->name]) : '' ?>
        </h3>
        <div class="box-tools pull-right">
            <?php if ($canAddExtraPrices) : ?>
                <?= AddExtraPricesButton::widget(['plan_id' => $plan->id, 'type' => $type]) ?>
            <?php endif ?>
            <?php if (!empty($models)) : ?>
                <?= ChangeFormulaButton::widget(['mod' => ChangeFormulaButton::MOD_ADD]) ?>
                <?= ChangeFormulaButton::widget(['mod' => ChangeFormulaButton::MOD_REPLACE]) ?>
            <?php endif ?>
        </div>
    </div>
    <div class="box-body">
        <?php if (!empty($models)) : ?>
            <?= $this->render('_form', compact('models', 'model', 'plan', 'type')) ?>
        <?php else: ?>
            <div id="prices-form">
                <?php Alert::begin([
                    'options' => [
                        'class' => 'alert-info',
                    ],
                    'closeButton' => false,
                ]) ?>
                <?= Html::tag('h4', Yii::t('hipanel.finance.price', 'No price suggestions for this object')) ?>
                <?= Yii::t('hipanel.finance.price', 'We could not suggest any new prices of type "{suggestionType}" for the selected object. Probably, they were already created earlier or this suggestion type is not compatible with this object type', [
                    'suggestionType' => Yii::t('hipanel.finance.suggestionTypes', $type),
                ]) ?>
                <br/>
                <?= Yii::t('hipanel.finance.price', 'You can return back to plan {backToPlan}', [
                    'backToPlan' => Html::a($plan->name, Url::to(['@plan/view', 'id' => $plan->id])),
                ]) ?>
                <?php if ($canAddExtraPrices) : ?>
                    <?= Yii::t('hipanel:finance', 'or {0}', Yii::t('hipanel:finance', 'Add extra prices')) ?>
                <?php endif ?>
                <?php Alert::end() ?>
            </div>
        <?php endif ?>
    </div>
</div>
