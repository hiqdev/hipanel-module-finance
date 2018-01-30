<?php

namespace hipanel\modules\finance\controllers;

use hipanel\actions\IndexAction;
use hipanel\actions\SmartCreateAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\ValidateFormAction;
use hipanel\actions\ViewAction;
use hipanel\base\CrudController;
use hiqdev\hiart\Query;
use Yii;
use yii\base\Event;

class PlanController extends CrudController
{
    public function actions()
    {
        return array_merge(parent::actions(), [
            'create' => [
                'class' => SmartCreateAction::class,
            ],
            'update' => [
                'class' => SmartUpdateAction::class,
            ],
            'index' => [
                'class' => IndexAction::class,
            ],
            'view' => [
                'on beforePerform' => function (Event $event) {
                    $action = $event->sender;
                    $action->getDataProvider()->query
                        ->joinWith('sales')
                        ->with([
                            'prices' => function (Query $query) {
                                $query->addSelect('main_object_id');
                                $query->joinWith('object');
                            },
                        ]);
                },
                'class' => ViewAction::class,
            ],
            'set-note' => [
                'class' => SmartUpdateAction::class,
                'success' => Yii::t('hipanel', 'Note changed'),
            ],
            'validate-form' => [
                'class' => ValidateFormAction::class,
            ],
        ]);
    }
}
