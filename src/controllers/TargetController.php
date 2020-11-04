<?php

namespace hipanel\modules\finance\controllers;

use hipanel\actions\ComboSearchAction;
use hipanel\base\CrudController;
use hipanel\filters\EasyAccessControl;

class TargetController extends CrudController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => EasyAccessControl::class,
                'actions' => [
                    '*' => ['plan.read', 'price.read'],
                ],
            ],
        ]);
    }

    public function actions()
    {
        return array_merge(parent::actions(), [
            'search' => [
                'class' => ComboSearchAction::class,
            ],
        ]);
    }
}
