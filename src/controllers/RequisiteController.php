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

use hipanel\actions\IndexAction;
use hipanel\actions\ViewAction;
use hipanel\actions\ComboSearchAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\RedirectAction;
use hipanel\filters\EasyAccessControl;
use hipanel\base\CrudController;
use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\models\Requisite;
use yii\base\Event;
use yii\filters\VerbFilter;
use Yii;

class RequisiteController extends CrudController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => EasyAccessControl::class,
                'actions' => [
                    'reserve-number' => 'requisites.update',
                    'create' => 'requisites.create',
                    'copy' => 'requisites.create',
                    'update' => 'requisites.update',
                    '*' => 'requisites.read',
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
            'search' => [
                'class' => ComboSearchAction::class,
            ],
            'view' => [
                'class' => ViewAction::class,
                'findOptions' => ['with_counters' => 1],
                'on beforePerform' => function ($event) {
                    /** @var ViewAction $action */
                    $action = $event->sender;

                    /** @var ContactQuery $query */
                    $query = $action->getDataProvider()->query;

                    if (Yii::getAlias('@document', false)) {
                        $query->withDocuments();
                    }
                    $query->withLocalizations();
                },
            ],
            'reserve-number' => [
                'class' => SmartUpdateAction::class,
                'success' => Yii::t('hipanel:client', 'Document number was reserved'),
                'view' => 'modal/reserveNumber',
                'POST html' => [
                    'save' => true,
                    'success' => [
                        'class' => RedirectAction::class,
                        'url' => function () {
                            $requisite = Yii::$app->request->post('Requisite');

                            return ['@requisite/view', 'id' => $requisite['id']];
                        },
                    ],
                ],
            ],
        ]);
    }
}
