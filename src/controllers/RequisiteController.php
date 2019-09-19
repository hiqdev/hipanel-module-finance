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

use hipanel\actions\SmartUpdateAction;
use hipanel\actions\RedirectAction;
use hipanel\filters\EasyAccessControl;
use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\models\Requisite;
use yii\base\Event;
use yii\filters\VerbFilter;
use Yii;

class RequisiteController extends \hipanel\modules\client\controllers\ContactController
{
    /**
     * @var NotifyTriesRepository
     */
    private $notifyTriesRepository;
    /**
     * @var HasPINCode
     */
    private $hasPINCode;

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
                    'delete' => 'requisites.delete',
                    'create' => 'requisites.create',
                    'copy' => 'requisites.create',
                    'update' => 'requisites.update',
                    '*' => 'requisites.read',
                ],
            ],
            [
                'class' => VerbFilter::class,
                'actions' => [
                    'set-confirmation' => ['post'],
                    'request-email-verification' => ['post'],
                    'request-phone-confirmation-code' => ['post'],
                    'confirm-phone' => ['post'],
                ],
            ],
        ]);
    }

    public function actions()
    {
        return array_merge(parent::actions(), [
            'reserve-number' => [
                'class' => SmartUpdateAction::class,
                'success' => Yii::t('hipanel:client', 'Document number was reserved'),
                'view' => 'modal/reserveNumber',
                'on beforeFetch' => function (Event $event) {
                    /** @var \hipanel\actions\SearchAction $action */
                    $action = $event->sender;
                    /** @var RequisiteQuery $query */
                    $query = $action->getDataProvider()->query;
                },
                'on beforeLoad' => function (Event $event) {
                    /** @var Action $action */
                    $action = $event->sender;
                },
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
