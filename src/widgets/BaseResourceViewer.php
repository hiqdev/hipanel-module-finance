<?php

namespace hipanel\modules\finance\widgets;

use hipanel\models\IndexPageUiOptions;
use hipanel\modules\finance\helpers\ResourceConfigurator;
use yii\base\ViewContextInterface;
use yii\base\Widget;
use yii\data\DataProviderInterface;
use yii\db\ActiveRecordInterface;

abstract class BaseResourceViewer extends Widget
{
    public DataProviderInterface $dataProvider;

    public ViewContextInterface $originalContext;

    public IndexPageUiOptions $uiModel;

    public ResourceConfigurator $configurator;

    public ActiveRecordInterface $originalSearchModel;

    public string $fetchResourcesUrl = 'fetch-resources';
}