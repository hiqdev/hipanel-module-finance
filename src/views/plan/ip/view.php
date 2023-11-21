<?php
declare(strict_types=1);

/**
 * @var View $this
 * @var Plan $model
 * @var IndexPage $page
 * @var PlanInternalsGrouper $grouper
 * @var Sale[] $salesByObject
 * @var Price[][] $pricesByMainObject
 * @var SaleSearch $searchModel
 * @var int $pageSize
 */

use hipanel\modules\finance\helpers\PlanInternalsGrouper;
use hipanel\modules\finance\models\Plan;
use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\Sale;
use hipanel\modules\finance\models\SaleSearch;
use hipanel\widgets\IndexPage;
use yii\web\View;

echo $this->render('../server/view', [
    'model' => $model,
    'page' => $page,
    'grouper' => $grouper,
    'salesByObject' => $salesByObject ?? [],
    'pricesByMainObject' => $pricesByMainObject ?? [],
    'searchModel' => $searchModel,
    'pageSize' => $pageSize,
]);
