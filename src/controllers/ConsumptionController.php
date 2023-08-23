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
use hipanel\modules\finance\models\ConsumptionSearch;
use hipanel\modules\finance\providers\ConsumptionsProvider;
use Yii;
use yii\base\Event;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ConsumptionController extends Controller
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
                    $query->joinWith('resources')->groupBy('month');
                },
                'data' => function (RenderAction $action, array $data): array {
                    return array_merge($data, [
                        'searchModel' => $action->parent->getSearchModel(),
                    ]);
                },
            ],
        ]);
    }

    public function actionView(string $id): string
    {
        $consumption = $this->consumptionsProvider->findById($id);
        if (!$consumption) {
            throw new NotFoundHttpException(Yii::t('hipanel:finance',
                'No consumption found for the requested resource'));
        }

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
            $consumptions = $this->consumptionsProvider->findAll($search->attributes);
            $consumption = reset($consumptions);
            if (!$consumption) {
                return [];
            }
            $resources = ResourceHelper::prepareDetailView($consumption->resources);

            return [
                'resources' => $resources,
                'totals' => ResourceHelper::calculateTotal($consumption->resources),
            ];
        }
    }
}
