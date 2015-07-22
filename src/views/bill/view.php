<?php

use hipanel\modules\finance\grid\BillGridView;
use hipanel\widgets\Pjax;
use yii\helpers\Html;

$this->title                   = Html::encode($model->domain);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Domains'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php Pjax::begin(Yii::$app->params['pjax']) ?>
<div class="row">

<div class="col-md-4">
    <?= BillGridView::detailView([
        'model'   => $model,
        'columns' => [
            'seller_id', 'client_id',
            ['attribute' => 'bill'],
        ],
    ]) ?>
</div>

</div>
<?php Pjax::end() ?>
