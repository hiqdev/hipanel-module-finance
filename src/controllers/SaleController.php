<?php

namespace hipanel\modules\finance\controllers;

use hipanel\actions\IndexAction;

class SaleController extends \hipanel\base\CrudController
{
    public function actions()
    {
        return [
            'index' => [
                'class' => IndexAction::class,
            ],
        ];
    }
}
