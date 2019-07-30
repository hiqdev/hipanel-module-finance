<?php

use hipanel\modules\finance\widgets\PriceDifferenceWidget;
use hipanel\modules\finance\widgets\ResourcePriceWidget;
use hipanel\widgets\Box;
use yii\helpers\Html;

/**
 * @var \yii\web\View
 * @var $model \hipanel\modules\finance\forms\CertificateTariffForm
 */
Box::begin() ?>
<div class="row">
    <div class="col-md-12">
        <table class="table table-condensed">
            <thead>
            <tr>
                <th></th>
                <?php foreach ($model->getResourceTypes() as $type) : ?>
                    <th style="text-align: center" colspan="<?= count($model->getPeriods()) ?>"><?= $type ?></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <th></th>
                <?php foreach ($model->getResourceTypes() as $type) : ?>
                    <?php foreach ($model->getPeriods() as $period => $periodLabel) : ?>
                        <?= Html::tag('th', $periodLabel); ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <?php $i = 0; ?>
            <?php foreach ($model->getCertificateTypes() as $id => $certificateType) : ?>
                <tr>
                    <td><?= $certificateType ?></td>
                    <?php foreach ($model->getTypeResources($certificateType) as $type => $resource) : ?>
                        <?php /** @var \hipanel\modules\finance\models\CertificateResource $resource */ ?>
                        <?php $baseResources = $model->getTypeParentResources($certificateType); ?>
                        <?php foreach ($model->getPeriods() as $period => $periodLabel) : ?>
                            <td>
                                <div class="row">
                                    <?php $textc = $price > 0 ? '' : ' text-warning' ?>
                                    <div class="col-md-6 <?= $textc ?>">
                                        <?= ResourcePriceWidget::widget([
                                            'price' => $resource->getMoneyForPeriod($period),
                                        ]) ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= PriceDifferenceWidget::widget([
                                            'new' => $resource->getMoneyForPeriod($period),
                                            'old' => $baseResources[$type]->getMoneyForPeriod($period),
                                        ]) ?>
                                    </div>
                                </div>
                            </td>
                        <?php endforeach; ?>
                        <?php ++$i; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php Box::end() ?>
