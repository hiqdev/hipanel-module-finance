<?php

use hipanel\models\IndexPageUiOptions;
use hipanel\modules\finance\widgets\ResourceListViewer;
use hiqdev\hiart\ActiveDataProvider;
use yii\db\ActiveRecordInterface;

/** @var ActiveRecordInterface $originalModel */
/** @var ActiveDataProvider $dataProvider */
/** @var IndexPageUiOptions $uiModel */
/** @var ActiveRecordInterface $model */

$this->title = Yii::t('hipanel', 'Target resources');
$this->params['breadcrumbs'][] = ['label' => Yii::t('hipanel:server', 'Targets'), 'url' => ['@target/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<?= ResourceListViewer::widget([
    'dataProvider' => $dataProvider,
    'originalContext' => $this->context,
    'originalSearchModel' => $model,
    'uiModel' => $uiModel,
    'configurator' => Yii::$container->get('target-resource-config'),
]) ?>
