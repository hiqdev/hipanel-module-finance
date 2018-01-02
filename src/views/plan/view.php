<?php

use hipanel\modules\finance\grid\SaleGridView;
use hipanel\modules\finance\grid\TariffGridView;
use yii\helpers\Html;

$this->title = Html::encode($model->object);
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance:sale', 'Sale'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss("
    .profile-block {
        text-align: center;
    }
");

?>
<div class="row">
    <div class="col-md-4">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('hipanel:finance:sale', 'Sale information') ?></h3>
            </div>
            <div class="box-body">
                <?= SaleGridView::detailView([
                    'model' => $model,
                    'boxed' => false,
                    'columns' => [
                        'object_v',
                        'time',
                        'seller',
                        'buyer',
                        'tariff'
                    ],
                ]) ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <?= $this->render('@hipanel/modules/ticket/views/ticket/_clientInfo', compact('client')); ?>
    </div>
    <div class="col-md-4">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('hipanel:finance:sale', 'Tariff information') ?></h3>
            </div>
            <div class="box-body">
                <?= TariffGridView::detailView([
                    'model' => $tariff,
                    'boxed' => false,
                    'columns' => [
                        'used',
                        'note',
                        'type',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>
