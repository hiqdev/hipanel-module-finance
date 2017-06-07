<?php

use hipanel\modules\finance\grid\BillGridView;
use hipanel\modules\finance\menus\BillDetailMenu;
use hipanel\widgets\ClientSellerLink;
use hipanel\widgets\MainDetails;
use hipanel\widgets\Pjax;
use yii\helpers\Html;

$this->title = $model->label;
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Payments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

Pjax::begin(Yii::$app->params['pjax']) ?>
<div class="row">

    <div class="col-md-3">
        <?= MainDetails::widget([
            'title' => $model->gtype_label,
            'icon' => 'fa-money',
//            'subTitle' => Html::a($model->client, ['@client/view', 'id' => $model->client_id]),
            'subTitle' => ClientSellerLink::widget(['model' => $model]),
            'menu' => BillDetailMenu::widget(['model' => $model], ['linkTemplate' => '<a href="{url}" {linkOptions}><span class="pull-right">{icon}</span>&nbsp;{label}</a>']),
        ]) ?>
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
