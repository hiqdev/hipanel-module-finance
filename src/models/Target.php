<?php

namespace hipanel\modules\finance\models;

use hipanel\base\Model;
use hipanel\base\ModelTrait;
use hipanel\modules\finance\helpers\ConsumptionConfigurator;
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
        $configurator = Yii::$container->get(ConsumptionConfigurator::class);
        $configurations = array_filter(
            $configurator->getConfigurations(),
            static fn(array $configuration): bool => $configuration['model'] instanceof self
        );
        $types = [];
        foreach ($configurations as $type => $configuration) {
            $types[$type] = $configuration['label'];
        }

        return $types;
    }

    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'show_deleted' => Yii::t('hipanel:finance', 'Show deleted'),
            'tariff_id' => Yii::t('hipanel:finance', 'Tariff'),
        ]);
    }
}
