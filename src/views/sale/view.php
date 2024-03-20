<?php

use hipanel\modules\finance\grid\PlanGridView;
use hipanel\modules\finance\grid\SaleGridView;
use hipanel\modules\finance\menus\SaleDetailMenu;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\Sale;
use hipanel\widgets\MainDetails;
use yii\helpers\Html;

/** @var Sale $model */
/** @var ?Plan $plan */

$this->title = Html::encode($model->object);
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance:sale', 'Sales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss('
    .profile-block {
        text-align: center;
    }
');

?>
<div class="row">
    <div class="col-md-3">
        <?= MainDetails::widget([
            'title' => $this->title ?? Yii::t('hipanel', 'No title'),
            'icon' => 'fa-briefcase',
            'menu' => SaleDetailMenu::widget(['model' => $model], ['linkTemplate' => '<a href="{url}" {linkOptions}><span class="pull-right">{icon}</span>&nbsp;{label}</a>']),
        ]) ?>
        <div class="box box-widget">
            <div class="box-body no-padding">
                <?= SaleGridView::detailView([
                    'model' => $model,
                    'boxed' => false,
                    'columns' => [
                        'object_v',
                        'object_label',
                        'time',
                        'unsale_time',
                        'seller',
                        'buyer',
                        'tariff',
                        'ticket'
                    ],
                ]) ?>
            </div>
        </div>

        <?php if (Yii::$app->user->can('client.read') && Yii::$app->user->can('access-subclients')) : ?>
            <div class="row">
                <?= $this->render('@vendor/hiqdev/hipanel-module-ticket/src/views/ticket/_clientInfo', compact('client')) ?>
            </div>
        <?php endif ?>
    </div>
    <?php if ($plan && Yii::$app->user->can('plan.read')) : ?>
        <div class="col-md-4">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= Yii::t('hipanel:finance:sale', 'Tariff information') ?></h3>
                </div>
                <div class="box-body no-padding">
                    <?= PlanGridView::detailView([
                        'model' => $plan,
                        'boxed' => false,
                        'columns' => [
                            'simple_name',
                            'client',
                            'type',
                            'state',
                            'note',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    <?php endif ?>
</div>
