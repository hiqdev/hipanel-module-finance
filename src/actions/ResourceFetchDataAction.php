<?php

namespace hipanel\modules\finance\actions;

use DateTime;
use hipanel\actions\Action;
use hipanel\actions\RenderJsonAction;
use hipanel\modules\finance\helpers\ResourceConfigurator;
use hipanel\modules\finance\helpers\ResourceHelper;
use hipanel\modules\finance\models\proxy\Resource;
use hiqdev\hiart\ResponseErrorException;
use yii\base\DynamicModel;

class ResourceFetchDataAction extends RenderJsonAction
{
    public ResourceConfigurator $configurator;


    public function init(): void
    {
        parent::init();
        $this->return = function (Action $action) {
            $request = $action->controller->request;
            if ($request->isPost) {
                $resources = $this->getResources([
                    'object_ids' => $request->post('object_ids'),
                    'time_from' => $request->post('time_from'),
                    'time_till' => $request->post('time_till'),
                    'groupby' => 'server_traf_month',
                ]);
                if (isset($resources['error'])) {
                    return $resources;
                }
                $resources = ResourceHelper::aggregateByObject($resources, $this->configurator);

                return [
                    'resources' => ResourceHelper::prepare($resources),
                    'totals' => ResourceHelper::calculateTotal($resources, $this->configurator),
                ];
            }
            $resources = $this->getResources([
                'object_ids' => $request->get('object_ids'),
                'time_from' => $request->get('time_from'),
                'time_till' => $request->get('time_till'),
                'type' => $request->get('type'),
                'groupby' => $request->get('groupby'),
            ]);

            return ResourceHelper::prepareDetailView($resources, $this->configurator);
        };
    }

    private function getResources(array $params): array
    {
        $options = DynamicModel::validateData([
            'object_ids' => $params['object_ids'],
            'time_from' => $params['time_from'] ?? (new DateTime())->modify('first day of this month')->format('Y-m-d'),
            'time_till' => $params['time_till'] ?? (new DateTime())->modify('last day of this month')->format('Y-m-d'),
            'groupby' => $params['groupby'],
            'type' => $params['type'],
        ], [
            [['object_ids', 'time_from', 'time_till', 'groupby'], 'required'],
            [['object_ids', 'type'], 'string'],
            [['time_from', 'time_till'], 'datetime', 'format' => 'php:Y-m-d'],
            ['groupby', 'in', 'range' => ['server_traf_month', 'server_traf_week', 'server_traf_day', 'server_traf_year']],
        ]);
        if ($options->hasErrors()) {
            return ['error' => $options->getErrors()[0]];
        }
        try {
            $resources = Resource::find()
                ->where([
                    'object_id' => explode(',', $options->object_ids),
                    'time_from' => $options->time_from,
                    'time_till' => $options->time_till,
                    'groupby' => $options->groupby,
                    'type' => $options->type,
                ])
                ->limit(-1)
                ->all();
        } catch (ResponseErrorException $exception) {
            return ['error' => $exception->getMessage()];
        }

        return $resources;
    }
}
