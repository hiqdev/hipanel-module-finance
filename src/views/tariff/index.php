<?php

use hipanel\modules\finance\grid\TariffGridView;

$this->title                   = Yii::t('app', 'Tariffs');
$this->params['subtitle']      = Yii::$app->request->queryParams ? 'filtered list' : 'full list';
$this->params['breadcrumbs'][] = $this->title;

?>

<?= tariffGridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'columns'      => [
        'checkbox',
        'seller_id', 'client_id',
        'tariff',
    ],
]) ?>
