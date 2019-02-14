<?php

/**
 * @var \yii\web\View
 * @var $manager \hipanel\modules\finance\logic\AbstractTariffManager
 */

use hipanel\modules\finance\menus\ProfileDetailMenu;
use hipanel\widgets\Box;
use hipanel\widgets\ClientSellerLink;
use hipanel\modules\finance\grid\ProfileTariffGridView;
use yii\helpers\Html;

$this->title = Html::encode($model->name);
$this->params['subtitle'] = Yii::t('hipanel.finance.profiletariff', 'Tariff profile detailed information');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel.finance.profiletariff', 'Tariff profiles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-3">
        <?php Box::begin([
            'options' => [
                'class' => 'box-solid',
            ],
            'bodyOptions' => [
                'class' => 'no-padding',
            ],
        ]) ?>
        <div class="profile-user-img text-center">
            <i class="fa fa-dollar fa-5x"></i>
        </div>
        <p class="text-center">
            <span class="profile-user-name">
                <?= Html::encode($model->name) ?>
                <br/>
                <?= ClientSellerLink::widget(['model' => $model]) ?>
            </span>
            <br>
            <span class="profile-user-role"></span>
        </p>
        <div class="profile-usermenu">
            <?= ProfileDetailMenu::widget(['model' => $model]) ?>
        </div>
        <?php Box::end() ?>
    </div>
    <div class="col-md-9">
        <?= ProfileTariffGridView::detailView([
            'boxed' => true,
            'model' => $model,
            'columns' => [
                'name',
                'domain_tariff',
                'certificate_tariff',
                'svds_tariff',
                'ovds_tariff',
                'server_tariff',
            ],
        ]) ?>
    </div>
</div>
