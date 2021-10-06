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
            [['show_deleted'], 'boolean'],
        ];
    }

    public function getTypes(): array
    {
        return array_filter([
            'anycastcdn' => Yii::t('hipanel:finance', 'Anycast CDN'),
            'videocdn' => Yii::t('hipanel:finance', 'Video CDN'),
        ]);
    }

    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'show_deleted' => Yii::t('hipanel:finance', 'Show deleted'),
        ]);
    }
}
