<?php

namespace hipanel\modules\finance\controllers;

use hipanel\actions\ComboSearchAction;
use hipanel\actions\IndexAction;
use hipanel\base\CrudController;
use hipanel\filters\EasyAccessControl;
use hipanel\modules\client\models\stub\ClientRelationFreeStub;
use hipanel\modules\finance\actions\ResourceDetailAction;
use hipanel\modules\finance\actions\ResourceFetchDataAction;
use hipanel\modules\finance\actions\ResourceListAction;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\Target;
use hipanel\modules\finance\models\TargetSearch;
use Yii;

class TargetresourceController extends TargetController
{
    public function actions(): array
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => ResourceListAction::class,
                'model' => Target::class,
                'searchModel' => TargetSearch::class,
            ],
        ]);
    }
}
