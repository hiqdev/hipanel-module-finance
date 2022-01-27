<?php

namespace hipanel\modules\finance\controllers;

use hipanel\actions\ComboSearchAction;
use hipanel\actions\IndexAction;
use hipanel\actions\SearchAction;
use hipanel\actions\ValidateFormAction;
use hipanel\actions\ViewAction;
use hipanel\base\CrudController;
use hipanel\filters\EasyAccessControl;
use hipanel\modules\client\models\stub\ClientRelationFreeStub;
use hipanel\modules\finance\actions\TargetManagementAction;
use hipanel\modules\finance\forms\TargetManagementForm;
use hipanel\modules\finance\helpers\ConsumptionConfigurator;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\query\TargetQuery;
use hipanel\modules\finance\models\Target;
use hipanel\modules\finance\providers\ConsumptionsProvider;
use hipanel\base\Module;
use hiqdev\hiart\Collection;
use yii\base\Event;

class TargetController extends CrudController
{
    private ConsumptionConfigurator $consumptionConfigurator;

    private ConsumptionsProvider $consumptionsProvider;

    public function __construct(string $id, Module $module, ConsumptionConfigurator $consumptionConfigurator, ConsumptionsProvider $consumptionsProvider, array $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->consumptionConfigurator = $consumptionConfigurator;
        $this->consumptionsProvider = $consumptionsProvider;
    }

    public function behaviors(): array
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

    public function actions(): array
    {
        return array_merge(parent::actions(), [
            'search' => [
                'class' => ComboSearchAction::class,
            ],
            'index' => [
                'class' => IndexAction::class,
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
            'view' => [
                'class' => ViewAction::class,
                'on beforePerform' => function (Event $event) {
                    /** @var SearchAction $action */
                    $action = $event->sender;
                    /** @var TargetQuery $action */
                    $query = $action->getDataProvider()->query;
                    $query->withSales()->select(['*']);
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
        $tariff = Plan::find()->where(['id' => $target->tariff_id])->one();

        return array_merge($data, [
            'originalModel' => $target,
            'client' => $client,
            'tariff' => $tariff,
            'consumption' => $consumption,
            'configurator' => $this->consumptionConfigurator,
        ]);
    }
}
