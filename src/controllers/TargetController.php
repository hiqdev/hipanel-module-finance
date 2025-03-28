<?php

declare(strict_types=1);

namespace hipanel\modules\finance\controllers;

use hipanel\actions\ComboSearchAction;
use hipanel\actions\IndexAction;
use hipanel\actions\SearchAction;
use hipanel\actions\SmartCreateAction;
use hipanel\actions\SmartDeleteAction;
use hipanel\actions\SmartPerformAction;
use hipanel\actions\SmartUpdateAction;
use hipanel\actions\ValidateFormAction;
use hipanel\actions\ViewAction;
use hipanel\base\CrudController;
use hipanel\base\Module;
use hipanel\filters\EasyAccessControl;
use hipanel\modules\client\models\stub\ClientRelationFreeStub;
use hipanel\modules\finance\actions\TargetManagementAction;
use hipanel\modules\finance\forms\TargetManagementForm;
use hipanel\modules\finance\helpers\ConsumptionConfigurator;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\query\TargetQuery;
use hipanel\modules\finance\models\Target;
use hipanel\modules\finance\providers\ConsumptionsProvider;
use hiqdev\hiart\Collection;
use Yii;
use yii\base\Event;

class TargetController extends CrudController
{
    public function __construct(
        string $id,
        Module $module,
        readonly private ConsumptionConfigurator $consumptionConfigurator,
        readonly private ConsumptionsProvider $consumptionsProvider,
        array $config = []
    )
    {
        parent::__construct($id, $module, $config);
    }

    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => EasyAccessControl::class,
                'actions' => [
                    'restore' => ['test.alpha'],
                    '*' => ['plan.read', 'price.read'],
                    'create' => 'target.create',
                    'update' => 'target.update',
                    'delete' => 'target.delete',
                ],
            ],
        ]);
    }

    public function actions(): array
    {
        return array_merge(parent::actions(), [
            'search' => [
                'class' => ComboSearchAction::class,
            ],
            'index' => [
                'class' => IndexAction::class,
            ],
            'create' => [
                'class' => SmartCreateAction::class,
                'success' => Yii::t('hipanel:finance', 'Target was successfully created'),
            ],
            'update' => [
                'class' => SmartUpdateAction::class,
                'success' => Yii::t('hipanel:finance', 'Target was successfully updated'),
                'on beforeFetch' => function (Event $event) {
                    $event->sender->getDataProvider()->query->andWhere(['show_deleted' => 1]);
                },
            ],
            'delete' => [
                'class' => SmartDeleteAction::class,
                'success' => Yii::t('hipanel:finance:sale', 'Target was successfully deleted.'),
            ],
            'change-plan' => [
                'class' => TargetManagementAction::class,
            ],
            'sale' => [
                'class' => TargetManagementAction::class,
            ],
            'close-sale' => [
                'class' => TargetManagementAction::class,
            ],
            'restore' => [
                'class' => SmartPerformAction::class,
            ],
            'view' => [
                'class' => ViewAction::class,
                'on beforePerform' => function (Event $event) {
                    /** @var SearchAction $action */
                    $action = $event->sender;
                    /** @var TargetQuery $action */
                    $query = $action->getDataProvider()->query;
                    $query->withSales()->andWhere(['show_deleted' => true])->select(['*']);
                },
                'data' => fn($action, $data): array => $this->getData($action, $data),
            ],
            'validate-form' => [
                'class' => ValidateFormAction::class,
                'validatedInputId' => false,
                'collection' => [
                    'class' => Collection::class,
                    'model' => new TargetManagementForm(),
                ],
            ],
        ]);
    }

    private function getData($action, $data)
    {
        $id = $action->controller->request->get('id');
        $consumption = $this->consumptionsProvider->findById($id);
        $target = $data['model'] ?? Target::find()->where(['id' => $id])->withSales()->one();
        $attributes = [
            'id' => $target->client_id,
            'login' => $target->client,
        ];
        $client = new ClientRelationFreeStub($attributes);
        $tariff = (!empty($target->tariff_id)) ? Plan::find()->where(['id' => $target->tariff_id])->one() : null;

        return array_merge($data, [
            'configurator' => $this->consumptionConfigurator,
            'originalModel' => $target,
            'client' => $client,
            'tariff' => $tariff,
            'consumption' => $consumption,
        ]);
    }
}
