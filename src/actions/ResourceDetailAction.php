<?php

namespace hipanel\modules\finance\actions;

use DateTime;
use hipanel\actions\Action;
use hipanel\actions\IndexAction;
use hipanel\modules\finance\models\proxy\ResourceSearch;
use Yii;

class ResourceDetailAction extends IndexAction
{
    public string $model;

    public function init()
    {
        $this->data = fn(Action $action): array => [
            'originalModel' => call_user_func([$this->model, 'findOne'], $action->controller->request->get('id')),
        ];
        $this->setSearchModel(new ResourceSearch());
        parent::init();
    }

    public function beforePerform()
    {
        parent::beforePerform();
        $query = $this->getDataProvider()->query;
        $query->andWhere([
            'time_from' => (new DateTime())->modify('first day of last month')->format('Y-m-d'),
            'time_till' => (new DateTime())->modify('last day of last month')->format('Y-m-d'),
        ]);
        $query->andWhere([
            'object_id' => Yii::$app->request->get('id'),
            'groupby' => 'server_traf_day',
        ]);
    }
}
