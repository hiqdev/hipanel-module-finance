<?php

use hipanel\modules\finance\widgets\PriceDifferenceWidget;
use hipanel\widgets\Box;

/**
 * @var \yii\web\View
 * @var $model \hipanel\modules\finance\forms\ServerTariffForm
 * @var $manager \hipanel\modules\finance\logic\AbstractTariffManager
 */
?>

<div class="row">
    <div class="col-md-12">
        <?php Box::begin(['title' => Yii::t('hipanel:finance:tariff', 'Tariff')]) ?>
        <table class="table table-condensed">
            <thead>
            <tr>
                <td><?= Yii::t('hipanel:finance:tariff', 'Price') ?></td>
                <?php if (Yii::$app->user->can('manage')) : ?>
                    <td><?= Yii::t('hipanel:finance:tariff', 'Parent tariff price') ?></td>
                    <td><?= Yii::t('hipanel:finance:tariff', 'Profit') ?></td>
                <?php endif; ?>
            </tr>
            </thead>
            <tbody>
            <tr>
                <?php $price = $model->calculation()->price; ?>
                <?php $basePrice = $model->parentCalculation()->price; ?>
                <td><?= Yii::$app->formatter->asCurrency($price, $model->calculation()->currency) ?></td>
                <?php if (Yii::$app->user->can('manage')) : ?>
                    <td><?= Yii::$app->formatter->asCurrency($basePrice, $model->parentCalculation()->currency) ?></td>
                    <td>
                        <?= PriceDifferenceWidget::widget([
                            'new' => $price,
                            'old' => $basePrice,
                        ]) ?>
                    </td>
                <?php endif; ?>
            </tr>
            </tbody>
        </table>
        <?php Box::end() ?>
    </div>
    <div class="col-md-12">
        <?php Box::begin(['title' => Yii::t('hipanel:finance:tariff', 'Hardware')]) ?>
        <table class="table table-condensed">
            <thead>
            <tr>
                <th><?= Yii::t('hipanel:finance:tariff', 'Resource') ?></th>
                <th><?= Yii::t('hipanel:finance:tariff', 'Model') ?></th>
                <th><?= Yii::t('hipanel:finance:tariff', 'Price per period') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model->getHardwareResources() as $resource) : ?>
                <?php $baseResource = $model->getParentHardwareResource($resource->object_id); ?>
                <tr>
                    <td><?= $resource->decorator()->displayTitle() ?></td>
                    <td><?= $resource->decorator()->displayPrepaidAmount() ?></td>
                    <td>
                        <?= \hipanel\modules\finance\widgets\ResourcePriceWidget::widget([
                            'price' => $resource->fee,
                            'currency' => $resource->currency
                        ]) ?>
                        <?php if (Yii::$app->user->can('manage')) : ?>
                            <?= PriceDifferenceWidget::widget([
                                'new' => $resource->fee,
                                'old' => $baseResource->fee,
                            ]) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
        <?php Box::end() ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?php Box::begin(['title' => Yii::t('hipanel:finance:tariff', 'Resources')]) ?>
        <table class="table table-condensed">
            <thead>
            <tr>
                <th><?= Yii::t('hipanel:finance:tariff', 'Resource') ?></th>
                <th><?= Yii::t('hipanel:finance:tariff', 'Price per period') ?></th>
                <th><?= Yii::t('hipanel:finance:tariff', 'Prepaid amount') ?></th>
                <th><?= Yii::t('hipanel:finance:tariff', 'Overuse price') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($model->getOveruseResources() as $resource) : ?>
                <?php $baseResource = $model->getParentOveruseResource($resource->type_id); ?>
                <tr>
                    <td><?= $resource->decorator()->displayTitle() ?></td>
                    <td>
                        <?= \hipanel\modules\finance\widgets\ResourcePriceWidget::widget([
                            'price' => $resource->fee,
                            'currency' => $resource->currency
                        ]) ?>
                        <?php if (Yii::$app->user->can('manage')) : ?>
                            <?= PriceDifferenceWidget::widget([
                                'new' => $resource->fee,
                                'old' => $baseResource->fee,
                            ]) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $resource->decorator()->displayPrepaidAmount() ?>
                    </td>
                    <td>
                        <?= \hipanel\modules\finance\widgets\ResourcePriceWidget::widget([
                            'price' => $resource->price,
                            'currency' => $resource->currency
                        ]) ?>
                        <?php if (Yii::$app->user->can('manage')) : ?>
                            <?= PriceDifferenceWidget::widget([
                                'new' => $resource->price,
                                'old' => $baseResource->price,
                            ]) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
        <?php Box::end() ?>
    </div>
</div>
