<?php

use hipanel\modules\finance\widgets\PriceDifferenceWidget;
use hipanel\widgets\Box;
use yii\helpers\Html;

/**
 * @var \yii\web\View
 * @var $model \hipanel\modules\finance\forms\DomainTariffForm
 */
Box::begin() ?>
<div class="row">
    <div class="col-md-12">
        <table class="table table-condensed">
            <thead>
            <tr>
                <th></th>
                <?php foreach ($model->getResourceTypes() as $type) {
                    echo Html::tag('th', $type);
                } ?>
            </tr>
            </thead>
            <tbody>
            <?php $i = 0; ?>
            <?php foreach ($model->getZones() as $zone => $id) : ?>
                <tr>
                    <td><strong><?= $zone ?></strong></td>
                    <?php $baseResources = $model->getZoneParentResources($zone); ?>
                    <?php foreach ($model->getZoneResources($zone) as $type => $resource) : ?>
                        <td>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= Yii::$app->formatter->asCurrency($resource->price, $resource->currency) ?>
                                </div>
                                <div class="col-md-6">
                                    <?= PriceDifferenceWidget::widget([
                                        'new' => $resource->price,
                                        'old' => $baseResources[$type]->price,
                                    ]) ?>
                                </div>
                            </div>
                        </td>
                        <?php ++$i; ?>
                    <?php endforeach ?>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>
<?php Box::end() ?>

<div class="row">
    <?php
    $services = $model->getServices();
    $baseServices = $model->getParentServices(); ?>
    <?php foreach ($services as $service) : ?>
        <div class="col-md-3">
            <?php Box::begin(['title' => $service->name]) ?>
            <table class="table table-condensed">
                <thead>
                <tr>
                    <?php foreach ($service->getOperations() as $operation => $title) : ?>
                        <?= Html::tag('td', $title); ?>
                    <?php endforeach; ?>
                </tr>
                <tbody>
                <tr>
                    <?php foreach ($service->getOperations() as $operation => $title) : ?>
                        <?php $resource = $service->getResource($operation); ?>
                        <td>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= Yii::$app->formatter->asCurrency($resource->price, $resource->currency) ?>
                                </div>
                                <div class="col-md-6">
                                    <?= PriceDifferenceWidget::widget([
                                        'new' => $resource->price,
                                        'old' => $baseServices[$service->type]->getResource($operation)->price,
                                    ]) ?>
                                </div>
                            </div>
                        </td>
                    <?php endforeach; ?>
                </tr>
                </tbody>
                </thead></table>
            <?php Box::end(); ?>
        </div>
    <?php endforeach ?>
</div>
