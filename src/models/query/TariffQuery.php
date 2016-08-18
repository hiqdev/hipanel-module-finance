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
        ]);

        $this->joinWith('resources');

        return $this;
    }

    public function byId($id)
    {
        return $this->andWhere(['id' => $id]);
    }
}
