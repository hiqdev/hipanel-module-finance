<?php

/*
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

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
