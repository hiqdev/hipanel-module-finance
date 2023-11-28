<?php

declare(strict_types=1);

namespace hipanel\modules\finance\controllers;

use DateTime;
use hipanel\actions\IndexAction;
use hipanel\base\CrudController;
use hipanel\filters\EasyAccessControl;
use hipanel\modules\finance\models\Pnl;
use hipanel\modules\finance\widgets\PnlAggregateDataTable;
use Yii;
use yii\base\Event;
use yii\web\Response;

class PnlController extends CrudController
{
    public function behaviors(): array
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => EasyAccessControl::class,
                'actions' => [
                    '*' => ['costprice.read'],
                ],
            ],
        ]);
    }

    public function actions(): array
    {
        return array_merge(parent::actions(), [
            'index' => [
                'class' => IndexAction::class,
                'on beforePerform' => function (Event $event) {
                    $event->sender->getDataProvider()->enableSynchronousCount();
                },
            ],
        ]);
    }

    public function actionFetchRows(?string $months = null): Response
    {
        $rows = [];
        if ($months) {
            $result = Pnl::perform('sums', ['months' => $months]);
            $rows = $this->prepareResult($result);
        }

        return $this->asJson([
            'rows' => $this->buildRowsTree($rows),
            'flatRows' => $rows,
        ]);
    }

    public function actionReport(): string
    {
        $this->layout = 'mobile-manager';
        $initialState = [];
        $initialState['monthTreeData'] = $this->prepareMonth(Pnl::batchPerform('search', ['groupby' => 'month']));
        $pnlTypes = array_filter(Pnl::batchPerform('search', ['groupby' => 'types']));
        $initialState['filtersTree'] = $this->buildFilersTree($pnlTypes);


        return $this->render('report', ['initialState' => $initialState]);
    }

    public function actionCalculation(?string $month = null): string|Response
    {
        if ($month) {
            $calculateResult = Pnl::perform('calculate', ['month' => $month]);

            return $this->asJson($calculateResult);
        }
        $aggregateData = Pnl::batchPerform('search', ['groupby' => 'month']);
        if ($this->request->isAjax) {
            return PnlAggregateDataTable::widget(['aggregateData' => $aggregateData]);
        }

        return $this->render('pnl-calculation', ['aggregateData' => $aggregateData]);
    }

    private function appendLeaf(array &$data, array $row): void
    {
        $sum = $row['sum'];
        $type = $row['type'];
        if (!isset($data[$type])) {
            $data[$type] = [
                'sort' => $this->toSortString($row['type']),
                'key' => $type,
                'type' => $type,
                'type_label' => Yii::$app->getI18n()->removeLegacyLangTags($row['type_label']),
                'month' => $row['month'],
            ];
            $this->fillWithMonths($data[$type]);
        }
        $data[$type][(new DateTime($row['month']))->format('M Y')] = (float)$sum;
    }

    public function appendNodesFromLeaf(array &$data, $leaf): void
    {
        $nodeType = $leaf['type'];
        while (str_contains($nodeType, ',')) {
            $nodeType = preg_replace('/,[^,]+$/', '', $nodeType);

            if (!isset($data[$nodeType])) {
                $nodeRow = [];
                $this->fillWithMonths($nodeRow);
                $nodeRow['sort'] = $this->toSortString($nodeType);
                $nodeRow['type'] = $nodeType;
                $nodeRow['type_label'] = strtoupper(str_contains($nodeType, ',') ? ltrim(strrchr($nodeType, ','), ',') : (string)$nodeType);
                $data[$nodeType] = $nodeRow;
            }
            $month = (new DateTime($leaf['month']))->format('M Y');
            if (isset($data[$nodeType][$month])) {
                $data[$nodeType][$month] = (float)bcadd((string)$data[$nodeType][$month], (string)$leaf['sum']);
            } else {
                $data[$nodeType][$month] = $leaf['sum'];
            }
        }
    }

    private function toSortString(string $type): string
    {
        return str_replace(',', '', $type);
    }

    private function fillWithMonths(array &$row): void
    {
        for ($m = 1; $m <= 12; $m++) {
            $month = date('M Y', mktime(0, 0, 0, $m, 1));
            if (!isset($row[$month])) {
                $row[$month] = 0;
            }
        }
    }

    private function prepareMonth(array $months = []): array
    {
        $result = $items = [];
        foreach ($months as $data) {
            $items[] = [
                'key' => $data['month'],
                'value' => $data['month'],
                'title' => (new DateTime($data['month']))->format('M Y'),
            ];
        }
        foreach ($items as $item) {
            $year = (new DateTime($item['key']))->format('Y');
            if (isset($result[$year])) {
                $result[$year]['children'][] = $item;
            } else {
                $result[$year] = [
                    'key' => $year,
                    'value' => $year,
                    'title' => $year,
                    'children' => [
                        $item,
                    ],
                ];
            }
        }

        return array_values($result);
    }

    private function prepareResult(mixed $result): array
    {
        $rows = [];
        foreach ($result as $row) {
            $this->appendLeaf($rows, $row);
            $this->appendNodesFromLeaf($rows, $row);
        }
        usort($rows, static fn(array $a, array $b): int => strcmp($a['sort'], $b['sort']));

        return $rows;
    }

    private function buildRowsTree(array $rows): array
    {
        $tree = [];

        foreach ($rows as $row) {
            $keys = explode(',', $row['type']);
            $currentLevel = &$tree;

            foreach ($keys as $key) {
                $found = false;

                foreach ($currentLevel as &$node) {
                    if ($node['tree'] === $key) {
                        $found = true;
                        $currentLevel = &$node['children'];
                        break;
                    }
                }
                unset($node);

                if (!$found) {
                    $currentLevel[] = [
                        ...$row,
                        'tree' => $key,
                        'key' => $row['type'],
                    ];
                    $currentLevel = &$currentLevel[count($currentLevel) - 1]['children'];
                }
            }
        }

        return $tree;
    }

    private function buildFilersTree(array $paths): array
    {
        $tree = [];
        usort($paths, static fn($a, $b): int => strcmp($a, $b));

        foreach ($paths as $path) {
            $keys = explode(',', $path);
            $currentLevel = &$tree;

            foreach ($keys as $key) {
                $found = false;

                foreach ($currentLevel as &$node) {
                    if ($node['text'] === $key) {
                        $found = true;
                        $currentLevel = &$node['children'];
                        break;
                    }
                }
                unset($node);

                if (!$found) {
                    $currentLevel[] = [
                        'text' => $key,
                        'value' => $path,
                        'key' => $path,
                        'dataIndex' => $path,
                        'children' => [],
                    ];
                    $currentLevel = &$currentLevel[count($currentLevel) - 1]['children'];
                }
            }
        }

        return $tree;
    }
}
