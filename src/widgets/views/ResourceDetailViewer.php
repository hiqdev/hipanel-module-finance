<?php

use hipanel\modules\finance\helpers\ResourceConfigurator;
use hipanel\modules\finance\helpers\ResourceHelper;
use yii\data\DataProviderInterface;
use yii\db\ActiveRecordInterface;

/** @var DataProviderInterface $dataProvider */
/** @var ActiveRecordInterface $originalModel */
/** @var ActiveRecordInterface $originalSearchModel */
/** @var ResourceConfigurator $configurator */

$resources = $dataProvider->getModels();
$resourceInitialData = ResourceHelper::prepareDetailView($dataProvider->getModels(), $configurator);
$this->registerJsVar('_init_resources', $resourceInitialData);
$this->registerJsVar('_init_resources_id', $originalModel->id);
?>

<div id="resource-detail"></div>
