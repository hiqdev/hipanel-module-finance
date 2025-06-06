<?php
/**
 * @var yii\web\View $this
 * @var BillingRegistryInterface $registry
 */

use hipanel\modules\finance\widgets\BillingRegistry\TariffTypesWidget;
use hiqdev\php\billing\product\BillingRegistryInterface;

$this->title = 'Tariff Types and Prices';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="box box-solid">
    <div class="box-body tariff-types-prices">
        <?= TariffTypesWidget::widget([
            'registry' => $registry,
        ]) ?>
    </div>
</div>
