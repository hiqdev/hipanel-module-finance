<?php

use hipanel\modules\finance\models\Price;
use hipanel\modules\finance\models\Sale;
use hipanel\widgets\IndexPage;

/**
 * @var \yii\web\View $this
 * @var \hipanel\modules\finance\models\Plan $model
 * @var IndexPage $page
 * @var \hipanel\modules\finance\helpers\PlanInternalsGrouper $grouper
 * @var Sale[] $salesByObject
 * @var Price[][] $pricesByMainObject
 */

echo $this->render('../server/view', compact('model', 'page', 'grouper', 'salesByObject', 'pricesByMainObject'));

