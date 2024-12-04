<?php

declare(strict_types=1);

namespace hipanel\modules\finance\models;

use DateTime;
use hipanel\base\SearchModelTrait;
use yii\db\QueryInterface;
use yii\helpers\ArrayHelper;

class ConsumptionSearch extends Consumption
{
    use SearchModelTrait {
        SearchModelTrait::searchAttributes as defaultSearchAttributes;
        SearchModelTrait::rules as defaultRules;
    }

    public function rules()
    {
        $date = new DateTime();

        return ArrayHelper::merge($this->defaultRules(), [
            [['time_from', 'time_till'], 'date', 'format' => 'php:Y-m-d'],
            [['groupby'], 'string'],
            ['time_from', 'default', 'value' => $date->modify('first day of this month')->format('Y-m-d')],
            ['time_till', 'default', 'value' => $date->modify('last day of this month')->format('Y-m-d')],
            ['groupby', 'default', 'value' => 'month'],
        ]);
    }

    public function searchAttributes()
    {
        return ArrayHelper::merge($this->defaultSearchAttributes(), [
            'time_from', 'time_till', 'class', 'groupby', 'types',
        ]);
    }

    /**
     * {@inheritdoc}
     * @return ConsumptionQuery
     */
    public static function find(array $options = []): QueryInterface
    {
        return new ConsumptionQuery(static::class, [
            'options' => $options,
        ]);
    }

    public function getClasses(): array
    {
        return $this->consumptionConfigurator->getClassesDropDownOptions();
    }

    public function getCurrentClassLabel()
    {
        return $this->classes[$this->class];
    }
}
