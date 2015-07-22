<?php

use hipanel\modules\finance\grid\TariffGridView;
use hipanel\widgets\Pjax;
use yii\helpers\Html;

$this->title         = Html::encode($model->tariff);
$this->subtitle      = 'tariff detailed information';
$this->breadcrumbs[] = ['label' => Yii::t('app', 'Tariffs'), 'url' => ['index']];
$this->breadcrumbs[] = $this->title;

?>

<?php Pjax::begin(Yii::$app->params['pjax']) ?>
<div class="row">

<div class="col-md-4">
    <?= TariffGridView::detailView([
        'model'   => $model,
        'columns' => [
            'seller_id', 'client_id',
            ['attribute' => 'tariff'],
        ],
    ]) ?>
</div>

</div>
<?php Pjax::end() ?>
