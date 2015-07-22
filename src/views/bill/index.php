<?php

use hipanel\modules\finance\grid\BillGridView;

$this->title                   = Yii::t('app', 'Bills');
$this->params['breadcrumbs'][] = $this->title;
$this->params['subtitle']      = Yii::$app->request->queryParams ? 'filtered list' : 'full list';

?>

<?= billGridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'  => $searchModel,
    'columns'      => [
        'checkbox',
        'seller_id', 'client_id',
        'time', 'sum', 'balance', 'gtype', 'description',
    ],
]) ?>
