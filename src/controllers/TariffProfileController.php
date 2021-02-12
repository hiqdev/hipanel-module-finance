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
use hipanel\actions\RenderAction;
use hipanel\actions\SmartCreateAction;
use hipanel\actions\SmartDeleteAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\ValidateFormAction;
use hipanel\actions\ViewAction;
use hipanel\base\CrudController;
use hipanel\filters\EasyAccessControl;
use hipanel\models\User;
use hipanel\modules\client\models\Client;
use Yii;

class TariffProfileController extends CrudController
{
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            'access-control' => [
                'class' => EasyAccessControl::class,
                'actions' => [
                    'create' => 'plan.create',
                    'update' => 'plan.update',
                    'delete' => 'plan.delete',
                    '*' => 'plan.read',
                ],
            ],
        ]);
    }

    public function actions(): array
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => IndexAction::class,
            ],
            'create' => [
                'class' => SmartCreateAction::class,
                'data' => function (): array {
                    /** @var User $user */
                    $user = Yii::$app->user->identity;
                    if ($user->type === Client::TYPE_SELLER) {
                        return [
                            'client' => $user->username,
                            'client_id' => $user->id,
                        ];
                    }

                    return [
                        'client' => $user->seller,
                        'client_id' => $user->seller_id,
                    ];
                },
            ],
            'update' => [
                'class' => SmartUpdateAction::class,
                'data' => function (RenderAction $action, array $data): array {
                    $model = $data['model'];

                    return [
                        'client' => $model->client,
                        'client_id' => $model->client_id,
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
