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
use hipanel\actions\SmartDeleteAction;
use hipanel\actions\SmartPerformAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\ValidateFormAction;
use hipanel\actions\RedirectAction;
use hipanel\actions\ViewAction;
use hipanel\base\CrudController;
use hipanel\filters\EasyAccessControl;
use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\models\Requisite;
use hipanel\modules\client\models\DocumentUploadForm;
use hipanel\modules\client\models\query\ContactQuery;
use hipanel\modules\client\actions\ContactUpdateAction;
use hipanel\modules\client\models\Verification;
use hipanel\modules\client\repositories\NotifyTriesRepository;
use hipanel\modules\client\helpers\HasPINCode;
use Yii;
use yii\base\Event;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class RequisiteController extends CrudController
{
    /**
     * @var NotifyTriesRepository
     */
    private $notifyTriesRepository;
    /**
     * @var HasPINCode
     */
    private $hasPINCode;

    public function __construct($id, $module, NotifyTriesRepository $notifyTriesRepository, HasPINCode $hasPINCode, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->notifyTriesRepository = $notifyTriesRepository;
        $this->hasPINCode = $hasPINCode;
    }

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
            'validate-form' => [
                'class' => ValidateFormAction::class,
            ],
            'create' => [
                'class' => ContactCreateAction::class,
            ],
            'delete' => [
                'class' => SmartDeleteAction::class,
                'success' => Yii::t('hipanel:client', 'Contact was deleted'),
            ],
            'update' => [
                'class' => ContactUpdateAction::class,
            ],
            'copy' => [
                'class' => SmartUpdateAction::class,
                'scenario' => 'create',
                'data' => function ($action) {
                    return [
                        'countries' => $action->controller->getRefs('country_code'),
                        'action' => 'create',
                    ];
                },
            ],
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

    public function actionAttachDocuments($id)
    {
        $contact = Contact::findOne($id);

        if ($contact === null) {
            throw new NotFoundHttpException();
        }

        $model = new DocumentUploadForm(['id' => $contact->id]);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $session = Yii::$app->session;
            if ($model->save()) {
                $session->addFlash('success', Yii::t('hipanel:client', 'Documents were saved'));

                return $this->redirect(['attach-documents', 'id' => $id]);
            }

            $session->addFlash('error', $model->getFirstError('title'));
        }

        return $this->render('attach-documents', [
            'contact' => $contact,
            'model' => $model,
        ]);
    }
}
