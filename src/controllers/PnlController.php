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

    public function actionFetchData($year): Response
    {
        $data = [];
        $result = Pnl::perform('sums', ['year' => $year])['year'];
        foreach ($result as $row) {
            $this->appendLeaf($data, $row);
            $this->appendNodesFromLeaf($data, $row);
        }
        usort($data, static fn(array $a, array $b): int => strcmp($a['sort'], $b['sort']));

        return $this->asJson(array_values($data));
    }

    public function actionReport(): string
    {
        $this->layout = 'mobile-manager';
        $initialState = [];
        foreach (['years', 'sections', 'directions', 'sets', 'items', 'details'] as $name) {
            $initialState[$name] = array_values(array_filter(Pnl::batchPerform('search', ['groupby' => $name])));
        }


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
                'section' => $row['section'],
                'direction' => $row['direction'],
                'set' => $row['set'],
                'item' => $row['item'],
                'detail' => $row['detail'],
                'sort' => $this->toSortString($row),
                'type' => $type,
                'month' => $row['month'],
            ];
            $this->fillWithMonths($data[$type]);
        }
        $data[$type][(new DateTime($row['month']))->format('M')] = (float)$sum;
    }

    private function toSortString(array $row): string
    {
        return implode('', [$row['section'], $row['direction'], $row['set'], $row['item'], $row['detail']]);
    }

    private function fillWithMonths(array &$row): void
    {
        for ($m = 1; $m <= 12; $m++) {
            $month = date('M', mktime(0, 0, 0, $m, 1));
            if (!isset($row[$month])) {
                $row[$month] = 0;
            }
        }
    }

    public function appendNodesFromLeaf(array &$data, $leaf): void
    {
        $nodeType = $leaf['type'];
        while (str_contains($nodeType, ',')) {
            $nodeType = str_replace(strrchr($nodeType, ','), '', $nodeType);
            if (!isset($data[$nodeType])) {
                [$section, $direction, $set, $item, $detail] = explode(',', $nodeType);
                $nodeRow = compact('section', 'direction', 'set', 'item', 'detail');
                $this->fillWithMonths($nodeRow);
                $nodeRow['sort'] = $this->toSortString($nodeRow);
                $nodeRow['type'] = $nodeType;
                $data[$nodeType] = $nodeRow;
            }
            $month = (new DateTime($leaf['month']))->format('M');
            if (isset($data[$nodeType][$month])) {
                $data[$nodeType][$month] = (float)bcadd((string)$data[$nodeType][$month], (string)$leaf['sum']);
            } else {
                $data[$nodeType][$month] = $leaf['sum'];
            }
        }
    }
}
