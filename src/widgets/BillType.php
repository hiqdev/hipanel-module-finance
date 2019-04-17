<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\widgets;

use hipanel\models\Ref;
use hipanel\widgets\Type;

class BillType extends Type
{
    public $defaultValues = [
        'success' => [
            'deposit,*',
            'monthly,*',
        ],
        'info' => [
            'overuse,*',
        ],
        'warning' => [
            'monthly,monthly',
            'exchange,*',
        ],
        'primary' => [
            'monthly,leasing',
        ],
        'default' => [
            'monthly,hardware',
            'correction,*',
        ],
    ];
    public $field = 'type';
    public $i18nDictionary = 'hipanel.finance.billTypes';

    protected function getModelLabel(): string
    {
        $billTypes = Ref::getListRecursively('type,bill', false);

        return $billTypes[$this->getFieldValue()] ?? $this->getFieldValue();
    }
}
