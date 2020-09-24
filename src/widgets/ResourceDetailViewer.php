<?php

namespace hipanel\modules\finance\widgets;

use yii\db\ActiveRecordInterface;

class ResourceDetailViewer extends BaseResourceViewer
{
    public ActiveRecordInterface $originalModel;

    public function run(): string
    {
        return $this->render('ResourceDetailViewer', [
            'configurator' => $this->configurator,
            'dataProvider' => $this->dataProvider,
            'originalContext' => $this->originalContext,
            'originalModel' => $this->originalModel,
            'originalSearchModel' => $this->originalSearchModel,
            'uiModel' => $this->uiModel,
        ]);
    }
}