<?php

declare(strict_types=1);

namespace hipanel\modules\finance\grid;

use hipanel\grid\BoxedGridView;
use hipanel\grid\DataColumn;
use hipanel\modules\client\grid\ClientColumn;
use hipanel\modules\finance\helpers\ConsumptionConfigurator;
use hipanel\modules\finance\helpers\ResourceHelper;
use hipanel\modules\finance\models\Consumption;
use Yii;
use yii\db\ActiveRecordInterface;
use yii\helpers\Html;

class ConsumptionGridView extends BoxedGridView
{
    public ConsumptionConfigurator $configurator;

    public $resizableColumns = false;

    public function init()
    {
        $this->columns = $this->getColumns();
        parent::init();
    }

    public function columns(): array
    {
        return array_merge(parent::columns(), []);
    }

    public function getColumns(): array
    {
        $columns = [];
        $columns['object'] = [
            'format' => 'raw',
            'attribute' => 'name',
            'label' => Yii::t('hipanel', 'Object'),
            'contentOptions' => ['style' => 'display: flex; flex-direction: row; justify-content: space-between; flex-wrap: nowrap;'],
            'footerOptions' => ['colspan' => Yii::$app->user->can('access-subclients') ? 2 : 1, 'rowspan' => 2],
            'value' => function (ActiveRecordInterface $model): string {
                $objectLabel = Html::tag('span', '-', ['class' => 'text-danger']);
                if ($model->name) {
                    $objectLabel = Html::tag('span', Html::encode($model->name) ?: '&nbsp;', ['class' => 'text-bold']);
                }

                return $objectLabel . Html::a(Yii::t('hipanel', 'Detail view'), ['view', 'id' => $model->id], ['class' => 'btn btn-default btn-xs']);
            },
        ];
        foreach ($this->filterModel->getColumnsWithLabels() as $type => $label) {
            $columns[$type] = [
                'attribute' => $type,
                'label' => $label,
                'enableSorting' => true,
                'filter' => false,
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right', 'data-type' => $type, 'style' => 'white-space:nowrap;'],
                'value' => function (Consumption $consumption) use ($type) {
                    $resources = array_filter($consumption->resources, static fn($resource) => $resource->type === $type);
                    if (!empty($resources)) {
                        $unit = reset($resources)->buildResourceModel()->decorator()->displayUnit();

                        return sprintf("%s %s", $this->summarize($resources), $unit);
                    }
                },
                'footer' => 'test_footer',
                'footerOptions' => ['class' => $type . ' text-right', 'data-type' => true, 'style' => 'white-space: nowrap;'],
            ];
        }

        return $columns;
    }

    private function summarize(array $resources): string
    {
        return ResourceHelper::summarize($resources);
    }
}
