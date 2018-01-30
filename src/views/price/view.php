<?php

use hipanel\modules\finance\grid\PriceGridView;
use hipanel\modules\finance\menus\PriceDetailMenu;
use yii\helpers\Html;

$this->title = Html::encode($model->id);
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Prices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-3">
        <div class="box box-solid">
            <div class="box-body no-padding">
                <div class="profile-user-img text-center">
                    <i class="fa fa-bar-chart fa-5x"></i>
                </div>
                <p class="text-center">
                    <span class="profile-user-role">
                        <?= $this->title ?>
                    </span>
                    <br>
                    <span class="profile-user-name"><?= $model->type ?></span>
                </p>

                <div class="profile-usermenu" style="border-top: 1px solid #f4f4f4;">
                    <?= PriceDetailMenu::widget(['model' => $model]) ?>
                </div>
            </div>
            <div class="box-footer no-padding">
                <?= PriceGridView::detailView([
                    'model' => $model,
                    'boxed' => false,
                    'columns' => array_filter([
                        'type',
                        'plan',
                        'unit',
                        'price',
                        'currency',
                        'note',
                    ]),
                ]) ?>
            </div>
        </div>
    </div>
</div>
