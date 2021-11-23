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
            'vps' => Yii::t('hipanel:finance', 'VPS'),
            'snapshot' => Yii::t('hipanel:finance', 'Snapshot'),
            'volume' => Yii::t('hipanel:finance', 'Volume'),
            'storage' => Yii::t('hipanel:finance', 'Storage'),
            'private_cloud' => Yii::t('hipanel:finance', 'Private cloud'),
            'private_cloud_backup' => Yii::t('hipanel:finance', 'Private cloud backup'),
        ]);
    }

    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'show_deleted' => Yii::t('hipanel:finance', 'Show deleted'),
            'tariff_id' => Yii::t('hipanel:finance', 'Tariff'),
        ]);
    }
}
