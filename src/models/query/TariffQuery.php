<?php

namespace hipanel\modules\finance\models\query;

class TariffQuery extends \hiqdev\hiart\ActiveQuery
{
    public function details()
    {
        $this->andWhere([
            'show_final' => true,
            'show_deleted' => true,
            'with_resources' => true,
            'with_parts' => true,
        ]);

        $this->joinWith(['resources' => function ($query) {
            return $query->joinWith('part');
        }]);

        return $this;
    }

    public function byId($id)
    {
        return $this->andWhere(['id' => $id]);
    }
}
