<?php

namespace hipanel\modules\finance\providers;

use hipanel\modules\finance\models\Consumption;

class ConsumptionsProvider
{
    public function findById($id): ?Consumption
    {
        $consumptions = Consumption::find()
            ->select(null)
            ->joinWith('resources')
            ->where(['object_id' => $id, 'groupby' => 'year'])
            ->all();
        if (empty($consumptions)) {
            return null;
        }

        return reset($consumptions);
    }

    public function findAll(array $searchAttributes = []): array
    {
        return Consumption::find()->joinWith('resources')->where($searchAttributes)->all();
    }
}
