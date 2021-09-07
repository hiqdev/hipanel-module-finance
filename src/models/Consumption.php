<?php

declare(strict_types=1);

namespace hipanel\modules\finance\models;

use hipanel\modules\finance\helpers\ConsumptionConfigurator;
use hipanel\modules\finance\models\proxy\Resource;
use hipanel\base\Model;
use hipanel\base\ModelTrait;
use Yii;

class Consumption extends Model
{
    use ModelTrait;

    public ?ConsumptionConfigurator $consumptionConfigurator = null;

    public function init()
    {
        parent::init();
        $this->consumptionConfigurator = Yii::$container->get(ConsumptionConfigurator::class);
    }

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['id', 'type_id', 'state_id', 'client_id', 'object_id'], 'integer'],
            [['name', 'state', 'type', 'client'], 'string'],
            [['class'], 'string'],
            [['class'], 'default', 'value' => $this->consumptionConfigurator->getFirstAvailableClass()],
            [['mainObject'], 'safe'],
            [$this->consumptionConfigurator->getAllPossibleColumns(), 'safe'],
        ]);
    }

    public function attributeLabels()
    {
        return array_merge([
            'class' => Yii::t('hipanel:finance', 'Object class'),
        ], $this->consumptionConfigurator->getAllPossibleColumnsWithLabels());
    }

    public function getResources()
    {
        return $this->hasMany(Resource::class, ['object_id' => 'id']);
    }

    public function getSortColumns(): array
    {
        return $this->consumptionConfigurator->getColumns($this->class);
    }

    public function getColumns(): array
    {
        return $this->consumptionConfigurator->getColumns($this->class);
    }

    public function getColumnsWithLabels(): array
    {
        return $this->consumptionConfigurator->getColumnsWithLabels($this->class);
    }
}
