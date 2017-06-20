<?php

use hipanel\modules\finance\widgets\PriceDifferenceWidget;
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
                    <?php foreach ($model->getPeriods() as $period => $periodLabel) : ?>
                        <?= Html::tag('th', Yii::t('hipanel:finance:tariff', '{op} for {duration}', [
                            'op' => $type,
                            'duration' => $periodLabel,
                        ])); ?>
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
                                    <div class="col-md-6">
                                        <?= Yii::$app->formatter->asCurrency($resource->getPriceForPeriod($period), $resource->currency) ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?= PriceDifferenceWidget::widget([
                                            'new' => $resource->getPriceForPeriod($period),
                                            'old' => $baseResources[$type]->getPriceForPeriod($period),
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
