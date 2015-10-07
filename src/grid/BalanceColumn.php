<?php

namespace hipanel\modules\finance\grid;

use hipanel\modules\finance\controllers\BillController;

class BalanceColumn extends \hipanel\grid\CurrencyColumn
{
    public $filter = false;
    public $compare = 'credit';
    public $attribute = 'balance';
    public $contentOptions = ['class' => 'text-right text-bold'];

    public function getUrl($model, $key, $index)
    {
        return BillController::getSearchUrl(['client_id' => $model->id]);
    }
}
