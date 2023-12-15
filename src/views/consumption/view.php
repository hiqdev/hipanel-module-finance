<?php

use hipanel\modules\finance\helpers\ConsumptionConfigurator;
use hipanel\modules\finance\models\Consumption;
use hipanel\modules\finance\widgets\ConsumptionViewer;
use yii\base\ViewNotFoundException;
use yii\db\ActiveRecordInterface;

/** @var ConsumptionConfigurator $configurator */
/** @var Consumption $consumption */
/** @var ActiveRecordInterface $mainObject */

$this->title = $consumption->name;
if (Yii::$app->user->can('test.beta')) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:finance', 'Resource consumption'), 'url' => ['index', 'ConsumptionSearch' => ['class' => $consumption->class]]];
}
$this->params['breadcrumbs'][] = $this->title;

try {
    $content = $this->render(sprintf("views/_%s", $consumption->class), ['mainObject' => $mainObject]);
} catch (ViewNotFoundException $exception) {
    $content = $this->render("views/_videocdn", ['mainObject' => $mainObject]);
}

?>

<div class="row">
    <div class="col-md-3">
        <?= $content ?>
    </div>
    <div class="col-md-9">
        <?= ConsumptionViewer::widget([
            'configurator' => $configurator,
            'consumption' => $consumption,
            'mainObject' => $mainObject,
        ]) ?>
    </div>
</div>
