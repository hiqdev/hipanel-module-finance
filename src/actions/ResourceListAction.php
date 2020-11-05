<?php

namespace hipanel\modules\finance\actions;

use hipanel\actions\Action;
use hipanel\actions\IndexAction;

class ResourceListAction extends IndexAction
{
    public string $model;

    public function init()
    {
        $this->data = fn(Action $action): array => [
            'originalModel' => new $this->model,
        ];
        parent::init();
    }
}
