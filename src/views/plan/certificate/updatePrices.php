<?php

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\helpers\PlanInternalsGrouper $grouper
 * @var \hipanel\modules\finance\models\Plan $plan
 * @var array $parentPrices
 */

$this->title = Yii::t('hipanel.finance.price', 'Update prices');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Tariff plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $name ?? $plan->name, 'url' => ['view', 'id' => $id ?? $plan->id]];
$this->params['breadcrumbs'][] = $this->title;

$prices = $grouper->group();

?>

<?= $this->render('_form', compact('prices', 'plan_id', 'action', 'parentPrices')) ?>
