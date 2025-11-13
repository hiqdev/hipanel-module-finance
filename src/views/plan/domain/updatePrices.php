<?php

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\helpers\PlanInternalsGrouper $grouper
 * @var \hipanel\modules\finance\models\Plan $plan
 * @var array[] $parentPrices
 * @var \hipanel\modules\finance\models\DomainZonePrice[][] $zonePrices
 * @var \hipanel\modules\finance\models\DomainServicePrice[] $servicePrices
 * @var \hipanel\modules\finance\models\DomainZonePrice[][] $parentZonePrices
 * @var \hipanel\modules\finance\models\DomainServicePrice[] $parentServicePrices
 */
$this->title = Yii::t('hipanel.finance.price', 'Update prices');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Tariff plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $plan->name, 'url' => ['view', 'id' => $plan->id]];
$this->params['breadcrumbs'][] = $this->title;

[$zonePrices, $servicePrices] = $grouper->group();
[$parentZonePrices, $parentServicePrices] = $parentPrices;
$plan_id ??= null;

?>

<?= $this->render('_form', compact(
    'action',
    'plan',
    'plan_id',
    'zonePrices',
    'servicePrices',
    'parentZonePrices',
    'parentServicePrices',
    'scenario'
)) ?>
