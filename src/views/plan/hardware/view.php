<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

echo $this->render('../server/view', [
    'model' => $model,
    'page' => $page,
    'grouper' => $grouper,
    'salesByObject' => $salesByObject ?? [],
    'pricesByMainObject' => $pricesByMainObject ?? null,
    'searchModel' => $searchModel,
    'pageSize' => $pageSize,
]);
