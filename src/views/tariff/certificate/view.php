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
                <?php foreach ($model->getResourceTypes() as $type) {
                    echo Html::tag('th', $type);
                } ?>
            </tr>
            </thead>
            <tbody>
            <?php
            $i = 0;
            foreach ($model->getCertificateTypes() as $id => $certificateType) {
                ?>
                <tr>
                    <td><strong><?= $certificateType ?></strong></td>
                    <?php foreach ($model->getTypeResources($certificateType) as $type => $resource) {
                        $baseResources = $model->getTypeParentResources($certificateType); ?>
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
                        <?php ++$i;
                    } ?>
                </tr>
                <?php
            } ?>
            </tbody>
        </table>
    </div>
</div>
<?php Box::end() ?>
