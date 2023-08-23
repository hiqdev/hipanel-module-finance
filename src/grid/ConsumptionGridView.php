<?php

declare(strict_types=1);

namespace hipanel\modules\finance\grid;

use hipanel\grid\BoxedGridView;
use hipanel\modules\finance\helpers\ResourceHelper;
use hipanel\modules\finance\models\Consumption;

class ConsumptionGridView extends BoxedGridView
{
    public $resizableColumns = false;
    private ?Consumption $model = null;

    public function init()
    {
        if ($this->filterModel) {
            $this->model = $this->filterModel;
        } else {
            $models = $this->dataProvider->getModels();
            $this->model = reset($models);
        }
        $this->columns = ['object', ...$this->model->getColumns()];
        parent::init();
    }

    public function columns()
    {
        return ResourceHelper::buildGridColumns($this->model->getColumnsWithLabels());
    }
}
