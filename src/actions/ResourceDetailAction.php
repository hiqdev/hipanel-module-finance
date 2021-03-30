<?php

namespace hipanel\modules\finance\actions;

use DateTime;
use hipanel\actions\Action;
use hipanel\actions\IndexAction;
use hipanel\actions\VariantsAction;
use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\helpers\ResourceConfigurator;
use hipanel\modules\finance\helpers\ResourceHelper;
use hipanel\modules\finance\models\proxy\ResourceSearch;
use Yii;

class ResourceDetailAction extends IndexAction
{
    public string $model;

    public ResourceConfigurator $configurator;

    public function init(): void
    {
        if (!$this->data) {
            $this->data = fn(Action $action, $data): array => [
                'originalModel' => call_user_func([$this->model, 'findOne'], $action->controller->request->get('id')),
            ];
        }
        $this->setSearchModel(new ResourceSearch());
        parent::init();
    }

    public function beforePerform()
    {
        parent::beforePerform();
        $this->getDataProvider()->query->andWhere([
            'time_from' => (new DateTime('2007-01-01'))->format('Y-m-d'),
            'time_till' => (new DateTime())->modify('last day of this month')->format('Y-m-d'),
            'object_id' => Yii::$app->request->get('id'),
            'groupby' => 'server_traf_year',
            'limit' => 'ALL',
        ]);
    }
}
