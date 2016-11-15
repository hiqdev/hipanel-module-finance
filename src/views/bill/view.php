<?php

use hipanel\helpers\Url;
use hipanel\modules\finance\grid\BillGridView;
use hipanel\widgets\Pjax;
use yii\helpers\Html;

$this->title = Html::encode($model->domain);
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

Yii::$app->response->redirect(Url::to('@bill/index'))->send();

Pjax::begin(Yii::$app->params['pjax']) ?>
<div class="row">

    <div class="col-md-4">
        <?= BillGridView::detailView([
            'model' => $model,
            'columns' => [
                'seller_id',
                'client_id',

            ],
        ]) ?>
    </div>

</div>
<?php Pjax::end() ?>
