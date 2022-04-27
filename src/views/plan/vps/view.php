<?php

echo $this->render('../server/view', [
    'model' => $model,
    'page' => $page,
    'grouper' => $grouper,
    'salesByObject' => $salesByObject ?? [],
    'pricesByMainObject' => $pricesByMainObject ?? [],
]);
