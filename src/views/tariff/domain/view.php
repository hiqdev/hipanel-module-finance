<?php

use hipanel\widgets\Box;
use yii\helpers\Html;

/**
 * @var $this \yii\web\View
 * @var $tariffForm \hipanel\modules\finance\forms\DomainTariffForm
 */

Box::begin() ?>
<div class="row">
    <div class="col-md-12">
        <table class="table table-condensed">
            <thead>
            <tr>
                <th></th>
                <?php foreach ($tariffForm->getResourceTypes() as $type) {
                    echo Html::tag('th', $type);
                } ?>
            </tr>
            </thead>
            <tbody>
            <?php
            $i = 0;
            foreach ($tariffForm->getZones() as $zone => $id) { ?>
                <tr>
                    <td><strong><?= $zone ?></strong></td>
                    <?php foreach ($tariffForm->getZoneResources($zone) as $type => $resource) {
                        $baseResources = $tariffForm->getZoneBaseResources($zone); ?>
                        <td>
                            <div class="row">
                                <div class="col-md-6">
                                    <?= Yii::$app->formatter->asCurrency($resource->price, $resource->currency) ?>
                                </div>
                                <div class="col-md-6">
                                    <?php
                                        $diff = $resource->price - $baseResources[$type]->price;
                                        if ($diff != 0) {
                                            echo Html::tag(
                                                'span',
                                                ($diff > 0 ? '+' : '') . Yii::$app->formatter->asDecimal($diff, 2),
                                                ['class' => $diff > 0 ? 'text-success' : 'text-danger']
                                            );
                                        }
                                    ?>
                                </div>
                            </div>
                        </td>

                        <?php $i++;
                    } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php Box::end() ?>
