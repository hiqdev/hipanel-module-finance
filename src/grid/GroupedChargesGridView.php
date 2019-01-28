<?php

namespace hipanel\modules\finance\grid;

use hipanel\modules\finance\logic\bill\QuantityFormatterFactoryInterface;
use hipanel\modules\finance\models\Charge;
use hipanel\modules\finance\widgets\BillType;
use hipanel\modules\finance\widgets\ColoredBalance;
use hipanel\modules\finance\widgets\PriceType;
use Yii;
use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;

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

                    return $html . BillType::widget([
                        'model' => $model,
                        'field' => 'ftype',
                        'labelField' => 'type_label',
                    ]);
                },
            ],
            'sum_with_children' => [
                'label' => '',
                'format' => 'raw',
                'value' => function (Charge $model) {
                    if ($model->parent_id) {
                        return '';
                    }

                    $children = $this->findChargeChildren($model);
                    if (empty($children)) {
                        return '';
                    }

                    $sum = array_reduce($children, function ($accumulator, Charge $charge) {
                        return $charge->sum + $accumulator;
                    }, $model->sum);

                    return ColoredBalance::widget([
                        'model' => new DynamicModel(['sum' => $sum, 'currency' => $model->currency]),
                        'attribute' => 'sum',
                        'url' => false,
                    ]);
                },
            ],
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
        if ($this->allowedParentId === null
            && $model->parent_id !== null
            && !\in_array($model->parent_id, $this->chrgeIds(), true)
        ) {
            return parent::renderTableRow($model, $key, $index);
        }

        if ($model->parent_id !== $this->allowedParentId) {
            return '';
        }

        return parent::renderTableRow($model, $key, $index);
    }

    private function chrgeIds(): array
    {
        return ArrayHelper::getColumn($this->dataProvider->getModels(), 'id');
    }

    /**
     * @param Charge $parent
     * @return Charge[]
     */
    private function findChargeChildren(Charge $parent): array
    {
        $result = [];
        foreach ($this->dataProvider->getModels() as $charge) {
            if ($charge->parent_id === $parent->id) {
                $result[] = $charge;
                $result = array_merge($result, $this->findChargeChildren($charge));
            }
        }

        return $result;
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
                $result[] = $this->renderTableRow($charge, $charge->id, $index);
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
