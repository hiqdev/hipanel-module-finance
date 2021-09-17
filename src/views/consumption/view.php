<?php

use hipanel\modules\finance\helpers\ConsumptionConfigurator;
use hipanel\modules\finance\models\Consumption;
use hipanel\modules\finance\widgets\ConsumptionViewer;
use yii\db\ActiveRecordInterface;

/** @var ConsumptionConfigurator $configurator */
/** @var Consumption $consumption */
/** @var ActiveRecordInterface $mainObject */

$this->title = $consumption->name;
if (Yii::$app->user->can('test.beta')) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Resource consumption'), 'url' => ['index', 'ConsumptionSearch' => ['class' => $consumption->class]]];
}
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-3">
        <?= $this->render(sprintf("views/_%s", $consumption->class), ['mainObject' => $mainObject]) ?>
    </div>
    <div class="col-md-9">
        <?= ConsumptionViewer::widget([
            'configurator' => $configurator,
            'consumption' => $consumption,
            'mainObject' => $mainObject,
        ]) ?>
    </div>
</div>
