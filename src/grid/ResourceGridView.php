<?php

namespace hipanel\modules\finance\grid;

use hipanel\grid\BoxedGridView;
use hipanel\grid\DataColumn;
use hipanel\modules\client\grid\ClientColumn;
use hipanel\modules\finance\helpers\ResourceConfigurator;
use hipanel\modules\finance\helpers\ResourceHelper;
use hipanel\modules\finance\models\proxy\Resource;
use hipanel\modules\finance\widgets\ResourceDetailTotalHook;
use hiqdev\yii2\daterangepicker\DateRangePicker;
use Yii;
use yii\db\ActiveRecordInterface;
use yii\helpers\Html;
use yii\web\JsExpression;

class ResourceGridView extends BoxedGridView
{
    public ResourceConfigurator $configurator;

    public function columns(): array
    {
        $columns = $this->configurator->getColumns();
        $columns['date'] = [
            'format' => 'html',
            'attribute' => 'date',
            'label' => Yii::t('hipanel', 'Date'),
            'footerOptions' => ['colspan' => 2, 'class' => 'text-center text-bold', 'style' => 'vertical-align: middle;'],
            'filter' => DateRangePicker::widget([
                'model' => $this->filterModel,
                'attribute' => 'time_from',
                'attribute2' => 'time_till',
                'defaultRanges' => false,
                'dateFormat' => 'yyyy-MM-dd',
                'options' => [
                    'class' => 'form-control',
                    'id' => 'grid_time_range',
                ],
                'clientOptions' => [
                    'maxDate' => new JsExpression('moment()'),
                ],
                'clientEvents' => [
                    'apply.daterangepicker' => new JsExpression(/** @lang ECMAScript 6 */ "(event, picker) => {
                        const form = $(picker.element[0]).closest('form');
                        const start = picker.startDate.format('yyyy-MM-dd'.toUpperCase());
                        const end = picker.endDate.format('yyyy-MM-dd'.toUpperCase());
                        $('#grid_time_range').val(start + ' - ' + end);
                        form.find(\"input[name*='time_from']\").val(start);
                        form.find(\"input[name*='time_till']\").val(end);
                        $(event.target).change();
                    }"),
                    'cancel.daterangepicker' => new JsExpression(/** @lang ECMAScript 6 */ "(event, picker) => {
                        const form = $(picker.element[0]).closest('form');
                        $('#grid_time_range').val('');
                        form.find(\"input[name*='time_from']\").val('');
                        form.find(\"input[name*='time_till']\").val('');
                        $(event.target).change();
                    }"),
                ],
            ]),
        ];
        $columns['type'] = [
            'format' => 'html',
            'attribute' => 'type',
            'label' => Yii::t('hipanel', 'Type'),
            'filter' => $this->configurator->getFilterColumns(),
            'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'prompt' => '---'],
            'footerOptions' => ['style' => 'display: none;'],
            'enableSorting' => false,
            'value' => fn($model): ?string => $columns[$model->type] ?? $model->type,
        ];
        $columns['total'] = [
            'format' => 'html',
            'attribute' => 'total',
            'label' => Yii::t('hipanel', 'Consumed'),
            'filter' => false,
            'contentOptions' => ['class' => 'text-right'],
            'footerOptions' => ['class' => 'text-right text-bold'],
            'value' => function (Resource $resource): ?string {
                if (in_array($resource->type, $this->configurator->getRawColumns(), true)) {
                    return $resource->buildResourceModel($this->configurator)->decorator()->displayAmountWithUnit();
                }

                return '';
            },
            'footer' => ResourceDetailTotalHook::widget(['id' => 'detail-resource-total']),
        ];

        return array_merge(parent::columns(), $columns);
    }

    public static function getColumns(ResourceConfigurator $configurator): array
    {
        $loader = ResourceHelper::getResourceLoader();
        $columns = [];
        $columns['object'] = [
            'format' => 'html',
            'attribute' => 'name',
            'label' => Yii::t('hipanel', 'Object'),
            'contentOptions' => ['style' => 'display: flex; flex-direction: row; justify-content: space-between; flex-wrap: nowrap;'],
            'footerOptions' => ['colspan' => Yii::$app->user->can('access-subclients') ? 2 : 1, 'rowspan' => 2],
            'value' => static function (ActiveRecordInterface $model) use ($configurator): string {
                $objectLabel = Html::tag('span', '-', ['class' => 'text-danger']);
                if ($model->name) {
                    $objectLabel = Html::tag('span', $model->name ?: '&nbsp;', ['class' => 'text-bold']);
                }

                return $objectLabel . Html::a(Yii::t('hipanel', 'Detail view'), [$configurator->getToObjectUrl(), 'id' => $model->id], ['class' => 'btn btn-default btn-xs']);
            },
        ];
        $columns['client_like'] = [
            'class' => ClientColumn::class,
            'filterOptions' => ['class' => 'narrow-filter'],
            'footerOptions' => ['style' => 'display: none;'],
            'footer' => false,
        ];
        foreach ($configurator->getColumns() as $type => $label) {
            $columns[$type] = [
                'class' => DataColumn::class,
                'label' => $label,
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right', 'data-type' => $type, 'style' => 'white-space:nowrap;'],
                'value' => fn() => $loader,
                'footer' => $loader,
                'footerOptions' => ['class' => $type . ' text-right', 'data-type' => true, 'style' => 'white-space: nowrap;'],
            ];
        }

        return $columns;
    }
}
