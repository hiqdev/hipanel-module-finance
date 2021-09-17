<?php

use hipanel\models\IndexPageUiOptions;
use hipanel\modules\client\models\Client;
use hipanel\modules\finance\grid\PlanGridView;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\proxy\ResourceSearch;
use hipanel\modules\finance\models\Target;
use hipanel\modules\finance\widgets\ResourceDetailViewer;
use yii\data\DataProviderInterface;
use yii\helpers\Html;

/** @var ResourceSearch $model */
/** @var Target $originalModel */
/** @var Plan $tariff */
/** @var Client $client */
/** @var DataProviderInterface $dataProvider */
/** @var IndexPageUiOptions $uiModel */

$this->title = Html::encode($originalModel->name);
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Targets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-3">

        <?php if (Yii::$app->user->can('client.read') && Yii::$app->user->can('access-subclients')) : ?>
            <div class="row">
                <?= $this->render('@vendor/hiqdev/hipanel-module-ticket/src/views/ticket/_clientInfo', compact('client')) ?>
            </div>
        <?php endif ?>

        <div class="box box-widget">
            <div class="box-header with-border">
                <h3 class="box-title"><?= Yii::t('hipanel:finance:sale', 'Tariff information') ?></h3>
            </div>
            <div class="box-body no-padding">
                <?= PlanGridView::detailView([
                    'model' => $tariff,
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
</div>
