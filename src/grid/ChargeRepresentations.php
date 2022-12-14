<?php
/**
 * Finance module for HiPanel
 *
 * @link      https://github.com/hiqdev/hipanel-module-finance
 * @package   hipanel-module-finance
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2019, HiQDev (http://hiqdev.com/)
 */

namespace hipanel\modules\finance\grid;

use hiqdev\higrid\representations\RepresentationCollection;
use Yii;

class ChargeRepresentations extends RepresentationCollection
{
    protected function fillRepresentations()
    {
        $this->representations = array_filter([
            'common' => [
                'label' => Yii::t('hipanel', 'common'),
                'columns' => [
                    'checkbox',
                    'bill_id',
                    'client_id',
                    'seller',
                    'tariff',
                    'name',
                    'type_label',
                    'currency',
                    'sum',
                    'quantity',
                    'is_payed',
                    'time',
                    'description',
                ],
            ],
        ]);
    }
}
