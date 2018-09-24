<?php

namespace hipanel\modules\finance\grid;

use hipanel\modules\finance\logic\bill\QuantityFormatterFactoryInterface;
use hipanel\modules\finance\models\Charge;
use hipanel\modules\finance\widgets\ColoredBalance;
use hipanel\modules\finance\widgets\PriceType;
use Yii;
use yii\base\DynamicModel;

/**
 * Class GroupedChargesGridView
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class GroupedChargesGridView extends ChargeGridView
{
    /**
     * @var int|null
     */
    public $allowedParentId;
    /**
     * @var string[]|null array of columns that were originally requested to render.
     * Used to generate nested grids with the same columns.
     */
    protected $requestedColumns;

    public function __construct(QuantityFormatterFactoryInterface $formatterFactory, array $config = [])
    {
        parent::__construct($formatterFactory, $config);

        $this->afterRow = \Closure::fromCallable([$this, 'renderChildCharges']);
    }

    protected function initColumns()
    {
        if ($this->requestedColumns === null) {
            $this->requestedColumns = $this->columns;
        }

        parent::initColumns();
    }

    public function columns()
    {
        return array_merge(parent::columns(), [
            'type_label' => [
                'label' => Yii::t('hipanel', 'Type'),
                'format' => 'raw',
                'value' => function ($model) {
                    $html = '';

                    if ($this->allowedParentId !== null) {
                        $html .= '<i style="color: #717171" class="fa fa-arrow-up"></i>&nbsp;';
                    }
                    return $html . PriceType::widget([
                        'model' => $model,
                        'field' => 'ftype'
                    ]);
                },
            ],
            'sum_with_children' => [
                'label' => '',
                'format' => 'raw',
                'value' => function (Charge $model) {
                    $children = $this->findChargeChildren($model);
                    if (empty($children)) {
                        return '';
                    }

                    $sum = array_reduce([$model] + $children, function ($accumulator, Charge $model) {
                        return $model->sum + $accumulator;
                    }, 0);
                    return ColoredBalance::widget([
                        'model' => new DynamicModel(['sum' => $sum, 'currency' => $model->currency]),
                        'attribute' => 'sum',
                        'url' => false
                    ]);
                }
            ]
        ]);
    }

    /**
     * @param Charge $model
     * @param mixed $key
     * @param int $index
     * @return string
     */
    public function renderTableRow($model, $key, $index)
    {
        // Prevent rendering child prices, unless it is intended
        if ($model->parent_id !== $this->allowedParentId) {
            return '';
        }

        return parent::renderTableRow($model, $key, $index);
    }

    /**
     * @param Charge $parent
     * @return Charge[]
     */
    private function findChargeChildren(Charge $parent): array
    {
        return array_filter($this->dataProvider->getModels(),
            function (Charge $charge) use ($parent) {
                return $charge->parent_id === $parent->id;
            }
        );
    }

    private function renderChildCharges(Charge $parent, $key, $index): string
    {
        $children = $this->findChargeChildren($parent);
        if (empty($children)) {
            return '';
        }

        return $this->assumeRenderingForParent($parent, function () use ($children, $key, $index) {
            $columns = $this->requestedColumns;
            $columns[0] = 'parent_mark_and_type';

            $result = [];
            foreach ($children as $charge) {
                $result[] = $this->renderTableRow($charge, $key, $index);
            }

            return implode('', $result);
        });

    }

    private function assumeRenderingForParent(Charge $parent, $callback)
    {
        $allowedParent = $this->allowedParentId;
        $this->allowedParentId = $parent->id;

        $result = $callback();

        $this->allowedParentId = $allowedParent;

        return $result;
    }
}
