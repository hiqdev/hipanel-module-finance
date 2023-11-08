<?php

declare(strict_types=1);

namespace hipanel\modules\finance\controllers;

use hipanel\actions\ProgressAction;
use hipanel\base\CrudController;
use hipanel\filters\EasyAccessControl;
use hipanel\modules\finance\models\Pnl;
use hipanel\modules\finance\widgets\PnlAggregateDataTable;
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
        ]);
    }

    public function actionReport(): string
    {
        $aggregateData = Pnl::batchPerform('search', ['groupby' => 'month']);

        return $this->render('report');
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
}
