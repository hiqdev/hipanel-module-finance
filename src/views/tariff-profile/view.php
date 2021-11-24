<?php

use hipanel\modules\finance\grid\TariffProfileGridView;
use hipanel\modules\finance\logic\AbstractTariffManager;
use hipanel\modules\finance\menus\ProfileDetailMenu;
use hipanel\modules\finance\models\TariffProfile;
use hipanel\widgets\ClientSellerLink;
use hipanel\widgets\MainDetails;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View
 * @var $manager AbstractTariffManager
 * @var $model TariffProfile
 */

$this->title = Html::encode($model->getTitle());
$this->params['subtitle'] = Yii::t('hipanel.finance.tariffprofile', 'Tariff profile detailed information');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel.finance.tariffprofile', 'Tariff profiles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-3">
        <?= MainDetails::widget([
            'title' => $this->title,
            'icon' => 'fa-dollar',
            'subTitle' => ClientSellerLink::widget(['model' => $model]),
            'menu' => ProfileDetailMenu::widget(['model' => $model], ['linkTemplate' => '<a href="{url}" {linkOptions}><span class="pull-right">{icon}</span>&nbsp;{label}</a>']),
        ]) ?>
    </div>
    <div class="col-md-9">
        <?= TariffProfileGridView::detailView([
            'boxed' => true,
            'model' => $model,
            'columns' => array_merge([
                'name',
                'domain_tariff',
                'certificate_tariff',
            ], array_map(static fn(string $type): string => $type . '_tariff', $model->getNotDomainTariffTypes())),
        ]) ?>
    </div>
</div>
