<?php

namespace hipanel\modules\finance\models\proxy;

use hipanel\base\Model;
use hipanel\base\ModelTrait;
use hipanel\modules\finance\helpers\ResourceConfigurator;
use hipanel\modules\finance\models\decorators\DecoratedInterface;
use hipanel\modules\finance\models\decorators\ResourceDecoratorInterface;
use hiqdev\php\units\Quantity;
use hiqdev\php\units\Unit;
use yii\db\QueryInterface;
use Yii;

class Resource extends Model implements DecoratedInterface
{
    use ModelTrait;

    private DecoratedInterface $resourceModel;

    public static function tableName(): string
    {
        return 'use';
    }

    /**
     * @return array|array[]
     */
    public function rules()
    {
        return [
            [['id', 'object_id', 'type_id'], 'integer'],
            [['last', 'total'], 'number'],
            [['type', 'aggregation', 'unit'], 'string'],
            [['time_from', 'time_till', 'date'], 'datetime', 'format' => 'php:Y-m-d'],
        ];
    }

    public function buildResourceModel(ResourceConfigurator $configurator): DecoratedInterface
    {
        if (!isset($this->resourceModel)) {
            $this->resourceModel = $configurator->getResourceModel([
                'type' => $this->type,
                'unit' => $this->unit,
                'quantity' => $this->getAmount(),
            ]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     * @return QueryInterface
     */
    public static function find(array $options = []): QueryInterface
    {
        return new ResourceQuery(get_called_class(), [
            'options' => $options,
        ]);
    }

    public function getAmount()
    {
        if (in_array($this->type, $this->getBandwidthTypes(), true)) {
            return $this->last;
        }

        return $this->total;
    }

    public function getConvertedAmount(string $from, string $to)
    {
        return Quantity::create(Unit::create($from), $this->getAmount())->convert(Unit::create($to))->getQuantity();
    }

    public function getChartAmount()
    {
        if (in_array($this->type, $this->getBandwidthTypes(), true)) {
            return round($this->getAmount() / (10 ** 6), 2);
        }
        if (in_array($this->type, $this->getTrafficTypes(), true)) {
            return round($this->getAmount() / (10 ** 9), 2);
        }

        return $this->getAmount();
    }

    public function getDisplayDate(): string
    {
        if ($this->aggregation === 'month') {
            return Yii::$app->formatter->asDate(strtotime($this->date), 'LLL y');
        }
        if ($this->aggregation === 'week') {
            return Yii::$app->formatter->asDate(strtotime($this->date), 'dd LLL y');
        }
        if ($this->aggregation === 'day') {
            return Yii::$app->formatter->asDate(strtotime($this->date), 'dd LLL y');
        }

        return Yii::$app->formatter->asDate(strtotime($this->date));
    }

    private function getTrafficTypes(): array
    {
        return ['server_traf_in', 'server_traf_max', 'server_traf'];
    }

    private function getBandwidthTypes(): array
    {
        return ['server_traf95_in', 'server_traf95_max', 'server_traf95'];
    }

    public function decorator(): ResourceDecoratorInterface
    {
        return $this->resourceModel->decorator();
    }
}