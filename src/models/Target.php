<?php

namespace hipanel\modules\finance\models;

use hipanel\base\Model;
use hipanel\base\ModelTrait;
use Yii;

class Target extends Model
{
    use ModelTrait;

    /**
     * {@inheritdoc}
     * @return array
     */
    public function rules()
    {
        return [
            [['id', 'type_id', 'state_id', 'client_id', 'access_id', 'tariff_id'], 'integer'],
            [['type', 'state', 'client', 'name', 'tariff'], 'string'],
        ];
    }

    public function getTypes(): array
    {
        return array_filter([
            'anycastcdn' => Yii::t('hipanel:finance', 'AnycastCDN'),
            'videocdn' => Yii::t('hipanel:finance', 'VideoCDN'),
        ]);
    }
}
