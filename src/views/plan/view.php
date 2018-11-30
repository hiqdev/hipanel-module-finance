<?php

use hipanel\modules\finance\helpers\PlanInternalsGrouper;
use hipanel\modules\finance\models\Plan;
use yii\helpers\Html;
use hipanel\modules\finance\grid\PlanGridView;
use hipanel\modules\finance\menus\PlanDetailMenu;
use hipanel\widgets\IndexPage;


/**
 * @var \yii\web\View $this
 * @var Plan $model
 * @var PlanInternalsGrouper $grouper
 * @var array $parentPrices
 */

$this->title = Html::encode($model->name);
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Tariff plans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCss('
    .profile-block {
        text-align: center;
    }
');

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
                    <?= PlanDetailMenu::widget(['model' => $model]) ?>
                </div>
            </div>
            <div class="box-footer no-padding">
                <?= PlanGridView::detailView([
                    'model' => $model,
                    'boxed' => false,
                    'columns' => array_filter([
                        'simple_name',
                        'monthly',
                        'client',
                        'type',
                        'state',
                        'note',
                    ]),
                ]) ?>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <?php $page = IndexPage::begin(['model' => $model, 'layout' => 'noSearch']) ?>
            <?php if (in_array($model->type, [
                Plan::TYPE_SERVER,
                Plan::TYPE_VCDN,
                Plan::TYPE_PCDN,
                Plan::TYPE_TEMPLATE,
                Plan::TYPE_CERTIFICATE,
                Plan::TYPE_DOMAIN
            ], true)): ?>
                <?php $page->beginContent('show-actions') ?>
                    <h4 class="box-title" style="display: inline-block;">&nbsp;<?= Yii::t('hipanel:finance', 'Prices') ?></h4>
                <?php $page->endContent() ?>
                <?= $this->render($model->type . '/view', compact('model', 'grouper', 'page', 'parentPrices')) ?>
            <?php else: ?>
                <?php $page->beginContent('table') ?>
                    <div class="col-md-12">
                        <h2><?= Yii::t('hipanel:finance', 'This plan type viewing is not implemented yet') ?></h2>
                    </div>
                <?php $page->endContent() ?>
            <?php endif; ?>
        <?php $page->end() ?>
    </div>
</div>
