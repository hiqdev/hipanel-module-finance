<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\controllers;

use hipanel\actions\ComboSearchAction;
use hipanel\actions\IndexAction;
use hipanel\actions\SmartCreateAction;
use hipanel\actions\SmartDeleteAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\ValidateFormAction;
use hipanel\actions\ViewAction;
use hipanel\filters\EasyAccessControl;
use Yii;

class TariffProfileController extends \hipanel\base\CrudController
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access-control' => [
                'class' => EasyAccessControl::class,
                'actions' => [
                    'create' => 'plan.create',
                    'update' => 'plan.update',
                    'delete' => 'plan.delete',
                    '*'      => 'plan.read',
                ],
            ],
        ]);
    }

    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => IndexAction::class,
            ],
            'create' => [
                'class' => SmartCreateAction::class,
                'data' => function(\hipanel\actions\RenderAction $action) : array {
                    $user = Yii::$app->user->identity;
                    if ($user->type === 'reseller') {
                        return [
                            'client' => $user->username,
                            'client_id' => $user->id,
                        ];
                    }
                    return [
                        'client' => $user->seller,
                        'client_id' => $user->seller_id,
                    ];
                }
            ],
            'update' => [
                'class' => SmartUpdateAction::class,
                'data' => function(\hipanel\actions\RenderAction $action) : array {
                    $model = $action->model;
                    return [
                        'client' => $model->seller,
                        'client_id' => $model->seller_id,
                    ];
                },
            ],
            'search' => [
                'class' => ComboSearchAction::class,
            ],
            'validate-form' => [
                'class' => ValidateFormAction::class,
            ],
            'view' => [
                'class' => ViewAction::class,
            ],
            'delete' => [
                'class' => SmartDeleteAction::class,
                'success' => Yii::t('hipanel.finance.tariffprofile', 'Profile deleted'),
            ],
        ]);
    }
}
