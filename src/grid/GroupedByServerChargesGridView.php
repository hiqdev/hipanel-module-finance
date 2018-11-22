<?php

namespace hipanel\modules\finance\grid;

use hipanel\modules\finance\models\Charge;


/**
 * Class GroupedChargesGridView
 *
 */
class GroupedByServerChargesGridView extends BillGridView
{

    /**
     * @var Charge[]
     */
    public $idToNameObject;

    /**
     * @var Charge[][]
     */
    public $chargesByMainObject;

    public function init()
    {
        parent::init();
        $this->initAfterRow();
    }

    public function columns()
    {
        return array_merge(parent::columns(), [
            'object_link' => [
                'format' => 'html',
                'value' => function (Charge $obj) {
                    return $obj->common_object_name;
                },
            ],
        ]);
    }

    private function initAfterRow()
    {
        /**
         * @param Charge $obj
         * @return string
         */
        $this->afterRow = function (Charge $obj) {
            $model = $this->chargesByMainObject[$obj->common_object_id];
            if (empty($model)) {
                return '';
            }
            return GroupedChargesGridView::widget([
                'boxed' => false,
                'showHeader' => true,
                'showFooter' => false,
                'options' => [
                    'tag' => 'tr',
                    'id' => crc32($model->id ?? microtime(true)),
                ],
                'layout' => '<td colspan="' . \count($this->columns) . '">{items}</td>',
                'dataProvider' => new \yii\data\ArrayDataProvider([
                    'allModels' => \hipanel\modules\finance\helpers\ChargeSort::anyCharges()
                        ->values($model, true),
                    'sort' => false,
                    'pagination' => false
                ]),
                'filterModel' => $model,
                'tableOptions' => [
                    'class' => 'table table-striped table-bordered'
                ],
                'columns' => [
                    'type_label', 'label',
                    'quantity', 'sum', 'sum_with_children', 'time',
                ],
            ]);
        };
    }
}
