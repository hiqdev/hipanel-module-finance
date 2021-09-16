<?php

declare(strict_types=1);

namespace hipanel\modules\finance\controllers;

use hipanel\actions\IndexAction;
use hipanel\actions\RenderAction;
use hipanel\actions\SearchAction;
use hipanel\base\Controller;
use hipanel\base\Module;
use hipanel\filters\EasyAccessControl;
use hipanel\modules\finance\helpers\ConsumptionConfigurator;
use hipanel\modules\finance\helpers\ResourceHelper;
use hipanel\modules\finance\models\Consumption;
use hipanel\modules\finance\models\ConsumptionSearch;
use yii\base\Event;
use yii\web\Response;

class ConsumptionController extends Controller
{
    private ConsumptionConfigurator $consumptionConfigurator;

    public function __construct(string $id, Module $module, ConsumptionConfigurator $consumptionConfigurator, array $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->consumptionConfigurator = $consumptionConfigurator;
    }

    public function behaviors()
    {
        return [
            [
                'class' => EasyAccessControl::class,
                'actions' => [
                    '*' => 'consumption.read',
                ],
            ],
        ];
    }

    public function actions()
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => IndexAction::class,
                'on beforePerform' => function (Event $event) {
                    /** @var SearchAction $action */
                    $action = $event->sender;
                    $query = $action->getDataProvider()->query;
                    $query
                        ->joinWith('resources')
                        ->groupBy('month');
                },
                'data' => function (RenderAction $action, array $data): array {
                    return array_merge($data, [
                        'configurator' => $this->consumptionConfigurator,
                        'searchModel' => $action->parent->getSearchModel(),
                    ]);
                },
            ],
        ]);
    }

    public function actionView(string $id): string
    {
        $consumptions = Consumption::find()
            ->select(null)
            ->joinWith('resources')
            ->where(['object_id' => $id, 'groupby' => 'year'])
            ->all();
        $consumption = reset($consumptions);

        return $this->render('view', [
            'mainObject' => $this->consumptionConfigurator->fillTheOriginalModel($consumption),
            'consumption' => $consumption,
            'configurator' => $this->consumptionConfigurator,
        ]);
    }

    public function actionGetConsumption(): array
    {
        $request = $this->request;
        $this->response->format = Response::FORMAT_JSON;
        $search = new ConsumptionSearch();
        if ($search->load($request->get(), '') && $search->validate()) {
            $consumptions = Consumption::find()->joinWith('resources')->where($search->attributes)->all();
            $consumption = reset($consumptions);
            $resources = ResourceHelper::prepareDetailView($consumption->resources);

            return [
                'resources' => $resources,
                'totals' => ResourceHelper::calculateTotal($consumption->resources),
            ];
        }
    }
}
