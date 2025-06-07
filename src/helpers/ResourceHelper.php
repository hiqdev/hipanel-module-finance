<?php declare(strict_types=1);

namespace hipanel\modules\finance\helpers;

use hipanel\helpers\ArrayHelper;
use hipanel\modules\finance\helpers\resource\ResourceAnalyticsService;
use hipanel\modules\finance\models\Consumption;
use hipanel\modules\finance\models\proxy\Resource;
use hipanel\modules\server\models\Hub;
use hipanel\modules\server\models\Server;
use hiqdev\hiart\ActiveRecord;
use hiqdev\yii\compat\yii;
use Yii as BaseYii;
use yii\db\ActiveRecordInterface;
use yii\helpers\Html;
use yii\helpers\Json;

class ResourceHelper
{
    public static function prepareDetailView(array $resources): array
    {
        return self::getResourceAnalyticsService()->prepareDetailView($resources);
    }

    private static function getResourceAnalyticsService(): ResourceAnalyticsService
    {
        static $service;
        if ($service === null) {
            $service = (new ResourceAnalyticsService());
        }

        return $service;
    }

    public static function summarize(array $resources): string
    {
        return self::getResourceAnalyticsService()->summarize($resources);
    }

    public static function calculateTotal(array $resources): array
    {
        return self::getResourceAnalyticsService()->calculateTotal($resources);
    }

    public static function buildGridColumns(array $columnsWithLabels): array
    {
        $columns = [];
        $user = yii::getApp()->user;
        $columns['object'] = [
            'format' => 'raw',
            'attribute' => 'name',
            'label' => BaseYii::t('hipanel', 'Object'),
            'contentOptions' => ['style' => 'display: flex; flex-direction: row; justify-content: space-between; flex-wrap: nowrap;'],
            'footerOptions' => ['colspan' => $user->can('access-subclients') ? 2 : 1, 'rowspan' => 2],
            'value' => function (ActiveRecordInterface $model): string {
                $objectLabel = Html::tag('span', '-', ['class' => 'text-danger']);
                if ($model->hasAttribute('name') && $model->name) {
                    $objectLabel = Html::tag('span', Html::encode($model->name) ?: '&nbsp;', ['class' => 'text-bold']);
                }

                return implode("", [
                    $objectLabel,
                    Html::a(
                        BaseYii::t('hipanel', 'Detail view'),
                        ['view', 'id' => $model->id],
                        ['class' => 'btn btn-default btn-xs']
                    ),
                ]);
            },
        ];
        foreach ($columnsWithLabels as $type => $label) {
            $columns[$type] = [
                'attribute' => $type,
                'label' => $label,
                'enableSorting' => true,
                'filter' => false,
                'format' => 'raw',
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => ['text-right', 'consumption-cell'], 'data-type' => $type, 'style' => 'white-space:nowrap;'],
                'value' => static function (ActiveRecord $model) use ($type): ?string {
                    $map = [
                        Hub::class => 'switch',
                        Server::class => 'server'
                    ];
                    $model = $model instanceof Consumption ? $model : self::transformToConsumptionModel($model, $map[$model::class]);
                    if (!$model->isRelationPopulated('resources') || !$model->resources) {
                        return null;
                    }
                    $resources = array_filter($model->resources, static fn($resource) => $resource->type === $type);
                    $resourceData = [];
                    foreach (ArrayHelper::index($resources, 'date') as $date => $resource) {
                        $decorator = $resource->decorator();
                        $resourceData[$date] = [
                            'amount' => \hiqdev\billing\registry\helper\ResourceHelper::convertAmount($decorator),
                            'unit' => $decorator->displayUnit(),
                        ];
                    }
                    if (!empty($resources)) {
                        $unit = reset($resources)->decorator()->displayUnit();

                        return Html::tag(
                            'span',
                            implode(" ", [self::summarize($resources), $unit]),
                            ['data-resources' => Json::encode($resourceData)]
                        );
                    }

                    return null;
                },
                'footer' => '',
                'footerOptions' => [
                    'class' => [$type, 'text-right'],
                    'data-type' => true,
                    'style' => 'white-space: nowrap;',
                ],
            ];
        }

        return $columns;
    }

    private static function transformToConsumptionModel(ActiveRecord $model, string $class): Consumption
    {
        if ($model instanceof Consumption) {
            return $model;
        }
        $consumption = new Consumption();
        $consumption->class = $class;
        $consumption->setAttributes($model->toArray());
        $uses = [];
        if ($model->isRelationPopulated('resources')) {
            $uses = $model->resources;
        } elseif ($model->isRelationPopulated('uses')) {
            $uses = $model->uses;
        }
        $resources = [];
        foreach ($uses as $use) {
            $resource = new Resource();
            $resource->class = $class;
            $resource->setAttributes($use->toArray());
            $resources[] = $resource;
        }
        $consumption->populateRelation(
            'resources',
            $resources
        );

        return $consumption;
    }
}
